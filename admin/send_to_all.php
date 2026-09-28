<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require '../vendor/autoload.php';
include('../database/connection.php');

// Start session to get admin ID
session_start();
$admin_id = isset($_SESSION['admin_id']) ? $_SESSION['admin_id'] : null;

if (!isset($_POST['program_name'])) {
    echo 'Program name is required';
    exit;
}

$program_name = mysqli_real_escape_string($conn, $_POST['program_name']);

// 🔹 Fetch all students in the program
$query = "SELECT 
        b.student_id, 
        b.seat_no, 
        b.program_name, 
        b.session_time, 
        b.calling_name,
        r.given_email_add, 
        r.email_address, 
        r.name_in_full
      FROM bulk_data_table b
      LEFT JOIN registered_students r 
        ON b.student_id = r.student_id
      WHERE b.program_name = ?
      ORDER BY b.student_id ASC";

$stmt = $conn->prepare($query);
$stmt->bind_param("s", $program_name);
$stmt->execute();
$dbResult = $stmt->get_result();

if ($dbResult->num_rows === 0) {
    echo 'No students found for this program';
    exit;
}

// ------------------------------------------------------
// Function to send email
// ------------------------------------------------------
function sendGraduationEmail($toEmail, $studentId, $nameInFull, $seatNo, $programName, $sessionTime, $callingName = '')
{
    $mail = new PHPMailer(true);

    try {
        // SMTP settings
        $mail->isSMTP();
        $mail->Host       = 'smtp.office365.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = 'bmsgraduation@bms.ac.lk';
        $mail->Password   = 'vspcktnnkhtwhxgr';
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = 587;

        $mail->setFrom('bmsgraduation@bms.ac.lk', 'BMS Graduation');
        $mail->addAddress($toEmail);

        $mail->isHTML(true);
        $mail->Subject = 'BMS Award Ceremony Seat Allocation Notification';

        // QR code
        $qrCodeImage = "https://api.qrserver.com/v1/create-qr-code/?size=250x250&bgcolor=FFFFFF&margin=50&data=" . urlencode($studentId);

        // Email body
        $mail->Body = '
        <div style="font-family: Arial, Helvetica, sans-serif; color: #222;">
            <div style="max-width:500px; margin:auto; border:1px solid #E5E5E5; font-size: 16px; background:#fafbfc; border-radius:8px; box-shadow:0 1px 5px rgba(0,0,0,0.04); padding:36px 32px 32px 32px;">
                <div style="border-bottom:2px solid #1d3557; padding-bottom:6px; margin-bottom:24px;">
                    <h2 style="margin:0; color:#1d3557; letter-spacing:1px; font-weight:600; font-size:23px;">BMS Award Ceremony 2026 - Seat Number</h2>
                </div>

                <p>Dear <strong>' . htmlspecialchars(!empty($callingName) ? $callingName : $nameInFull) . '</strong>,</p>

               <p style="color:#2b3137;">  <span style="color:#2980b9;">Congratulations and thank you for the registration for the BMS Award Ceremony 2026.</span>  Please find below your Seat Number allocated for you. <br><br>
                 Please provide the QR code given below at the Registration Desk at the Entrance of the BMICH Lotus Hall before 8.30am to mark your presence and to guide you to your seat </p>
                
                 
                <table style="width:100%; border-collapse:collapse; margin:24px 0 16px 0;">
                    <tr style="background-color:#F1F6FA;">
                        <th align="left" style="padding:9px 13px;">Student ID</th>
                        <td style="padding:9px 13px;">' . htmlspecialchars($studentId) . '</td>
                    </tr>
                    <tr style="background-color:#F1F6FA;">
                        <th align="left" style="padding:9px 13px;">Full Name</th>
                        <td style="padding:9px 13px;">' . htmlspecialchars($callingName) . '</td>
                    </tr>

                    <tr>
                        <th align="left" style="padding:9px 13px;">Program</th>
                        <td style="padding:9px 13px;">' . htmlspecialchars($programName) . '</td>
                    </tr>
                    
                    <tr>
                        <th align="left" style="padding:9px 13px;">Session </th>
                        <td style="padding:9px 13px;">' . htmlspecialchars($sessionTime) . '</td>
                    </tr>
                </table>

                <div style="text-align:center; margin:24px 0 18px 0;">
                    <span style="display:inline-block; font-size:32px; font-weight:bold; color:#133a5d; letter-spacing:2px;">
                        Seat No : ' . htmlspecialchars($seatNo) . '
                    </span>
                </div>
                <div style="text-align:center; margin:18px 0;">
                    <img src="' . $qrCodeImage . '" alt="QR Code" style="width:160px; height:160px; border-radius:10px; border:2px solid #dde8f5; background:#fff;" />
                </div>

                 <p style="margin:12px 0 0 0;">
                    <span style="color:#ef233c; font-weight:bold;">Note:</span> Please note that using Mobile phone inside the hall is strictly prohibited.
                </p>
                <div style="margin-top:36px; color:#67717c;">
                    Best regards,<br>
                    <strong>BMS Award Ceremony Team</strong>
                </div>
            </div>
        </div>';

        $mail->send();
        return ['success' => true, 'error' => null];
    } catch (Exception $e) {
        return ['success' => false, 'error' => $mail->ErrorInfo];
    }
}

// ------------------------------------------------------
// Function to log email attempts
// ------------------------------------------------------
function logEmail($conn, $studentId, $nameInFull, $email, $programName, $seatNo, $sessionTime, $status, $errorMsg, $adminId, $emailType = 'bulk')
{
    $stmt = $conn->prepare("INSERT INTO email_log 
        (student_id, name_in_full, email_address, program_name, seat_no, session_time, email_type, status, error_message, sent_by) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param(
        "sssssssssi",
        $studentId,
        $nameInFull,
        $email,
        $programName,
        $seatNo,
        $sessionTime,
        $emailType,
        $status,
        $errorMsg,
        $adminId
    );
    $stmt->execute();
    $stmt->close();
}

// ------------------------------------------------------
// Process all students
// ------------------------------------------------------
$total_students = 0;
$emails_sent = 0;
$emails_failed = 0;
$students_without_email = 0;

// while ($row = $dbResult->fetch_assoc()) {
//     $total_students++;

//     $email = trim($row['email_address'] ?? '');
//     $given_email_add = trim($row['given_email_add'] ?? '');

//     $email_valid = !empty($email) && filter_var($email, FILTER_VALIDATE_EMAIL);
//     $given_email_valid = !empty($given_email_add) && filter_var($given_email_add, FILTER_VALIDATE_EMAIL);

//     if (!$email_valid && !$given_email_valid) {
//         $students_without_email++;
//         continue;
//     }

//     $studentId = $row['student_id'];
//     $nameInFull = $row['name_in_full'];
//     $seatNo = $row['seat_no'];
//     $programName = $row['program_name'];
//     $sessionTime = $row['session_time'];

//     // Emails to send
//     $emails_to_send = [];

//     if ($email_valid && $given_email_valid) {
//         if (strtolower($email) === strtolower($given_email_add)) {
//             $emails_to_send[] = $email;
//         } else {
//             $emails_to_send = [$email, $given_email_add];
//         }
//     } elseif ($email_valid) {
//         $emails_to_send[] = $email;
//     } elseif ($given_email_valid) {
//         $emails_to_send[] = $given_email_add;
//     }

//     // Send each email
//     foreach ($emails_to_send as $email_address) {
//         $emailResult = sendGraduationEmail($email_address, $studentId, $nameInFull, $seatNo, $programName, $sessionTime);

//         if ($emailResult['success']) {
//             $emails_sent++;
//             logEmail($conn, $studentId, $nameInFull, $email_address, $programName, $seatNo, $sessionTime, 'sent', null, $admin_id);
//         } else {
//             $emails_failed++;
//             logEmail($conn, $studentId, $nameInFull, $email_address, $programName, $seatNo, $sessionTime, 'failed', $emailResult['error'], $admin_id);
//         }

//         // Delay between emails (prevent SMTP flood)
//         usleep(150000); // 0.15 seconds
//     }
// }



// Prevent PHP timeout for large batches
set_time_limit(0);

while ($row = $dbResult->fetch_assoc()) {
    $total_students++;

    $email = trim($row['email_address'] ?? '');
    $given_email_add = trim($row['given_email_add'] ?? '');

    $email_valid = !empty($email) && filter_var($email, FILTER_VALIDATE_EMAIL);
    $given_email_valid = !empty($given_email_add) && filter_var($given_email_add, FILTER_VALIDATE_EMAIL);

    if (!$email_valid && !$given_email_valid) {
        $students_without_email++;
        continue; // Skip students with no valid email
    }

    $studentId   = $row['student_id'];
    $nameInFull  = $row['name_in_full'];
    $seatNo      = $row['seat_no'];
    $programName = $row['program_name'];
    // $sessionTime = $row['session_time'];
    $sessionTimeRaw = $row['session_time'] ?? '';
    $sessionTime = $sessionTimeRaw;
    if (strcasecmp(trim($sessionTimeRaw), 'morning') === 0) {
        $sessionTime = "Session 1 ( 10.00am - 12.00pm )";
    } elseif (strcasecmp(trim($sessionTimeRaw), 'evening') === 0) {
        $sessionTime = "Session 2 ( 4.00pm - 7.00pm )";
    }
    $callingName = $row['calling_name'] ?? '';





    // Determine emails to send
    $emails_to_send = [];

    if ($email_valid && $given_email_valid) {
        $emails_to_send = (strtolower($email) === strtolower($given_email_add))
            ? [$email]
            : [$email, $given_email_add];
    } elseif ($email_valid) {
        $emails_to_send[] = $email;
    } elseif ($given_email_valid) {
        $emails_to_send[] = $given_email_add;
    }

    // Remove duplicate emails just in case
    $emails_to_send = array_unique($emails_to_send);

    // Send emails
    foreach ($emails_to_send as $email_address) {
        try {
            $emailResult = sendGraduationEmail($email_address, $studentId, $nameInFull, $seatNo, $programName, $sessionTime, $callingName);

            if ($emailResult['success']) {
                $emails_sent++;
                logEmail($conn, $studentId, $nameInFull, $email_address, $programName, $seatNo, $sessionTime, 'sent', null, $admin_id);
            } else {
                $emails_failed++;
                logEmail($conn, $studentId, $nameInFull, $email_address, $programName, $seatNo, $sessionTime, 'failed', $emailResult['error'], $admin_id);
            }

            // Delay to prevent SMTP flood
            usleep(150000); // 0.15 sec
        } catch (Exception $e) {
            $emails_failed++;
            logEmail($conn, $studentId, $nameInFull, $email_address, $programName, $seatNo, $sessionTime, 'failed', $e->getMessage(), $admin_id);
        }
    }
}


// ------------------------------------------------------
// Final summary
// ------------------------------------------------------
$message = "Email sending completed!\n\n";
$message .= "Total Students: $total_students\n";
$message .= "Emails Sent: $emails_sent\n";
$message .= "Emails Failed: $emails_failed\n";
$message .= "Students Without Valid Email: $students_without_email\n";

echo nl2br($message);

$stmt->close();
$conn->close();
