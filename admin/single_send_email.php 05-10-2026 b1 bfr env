<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require '../vendor/autoload.php';
include('../database/connection.php');

// Start session to get admin ID
session_start();
$admin_id = isset($_SESSION['admin_id']) ? $_SESSION['admin_id'] : null;

// Get both emails
$email = isset($_POST['email']) ? trim($_POST['email']) : '';
$given_email_add = isset($_POST['given_email_add']) ? trim($_POST['given_email_add']) : '';

// Validate both emails
$email_valid = !empty($email) && filter_var($email, FILTER_VALIDATE_EMAIL);
$given_email_valid = !empty($given_email_add) && filter_var($given_email_add, FILTER_VALIDATE_EMAIL);

// Check if at least one valid email exists
if (!$email_valid && !$given_email_valid) {
    echo 'No valid email address provided';
    exit;
}

// Get student data
$studentId    = $_POST['student_id'] ?? '';
$nameInFull   = $_POST['name_in_full'] ?? '';
$seatNo       = $_POST['seat_no'] ?? '';
$programName  = $_POST['program_name'] ?? '';
// $sessionTime  = $_POST['session_time'] ?? '';
$sessionTimeRaw = $_POST['session_time'] ?? '';
$sessionTime = $sessionTimeRaw;
// if (strcasecmp(trim($sessionTimeRaw), 'morning') === 0) {
//     $sessionTime = "Session 1 ( 10.00am - 12.00pm )";
// } elseif (strcasecmp(trim($sessionTimeRaw), 'evening') === 0) {
//     $sessionTime = "Session 2 ( 4.00pm - 7.00pm )";
// }


if (strcasecmp(trim($sessionTimeRaw), 'morning') === 0) {

    $sessionTime = "Session 1 ( 10.00am - 12.00pm )";
} elseif (strcasecmp(trim($sessionTimeRaw), 'evening') === 0) {

    $sessionTime = "Session 2 ( 4.00pm - 7.00pm )";
} elseif (strcasecmp(trim($sessionTimeRaw), 'SESSION_01') === 0) {

    $sessionTime = "Session 1 ( 9.00am - 12.00pm )";
} elseif (strcasecmp(trim($sessionTimeRaw), 'SESSION_02') === 0) {

    $sessionTime = "Session 2 ( 2.00pm - 4.00pm )";
} elseif (strcasecmp(trim($sessionTimeRaw), 'SESSION_03') === 0) {

    $sessionTime = "Session 3 ( 5.30pm - 7.30pm )";
}



$callingName  = $_POST['calling_name'] ?? '';

// Determine which emails to send to
$emails_to_send = [];

if ($email_valid && $given_email_valid) {
    // Both emails are valid
    if (strtolower($email) === strtolower($given_email_add)) {
        // Both emails are the same - send only once
        $emails_to_send[] = $email;
    } else {
        // Different emails - send to both
        $emails_to_send[] = $email;
        $emails_to_send[] = $given_email_add;
    }
} elseif ($email_valid) {
    // Only primary email is valid
    $emails_to_send[] = $email;
} elseif ($given_email_valid) {
    // Only given email is valid
    $emails_to_send[] = $given_email_add;
}

// Function to send email
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

        // Generate QR code
        $qrCodeImage = "https://api.qrserver.com/v1/create-qr-code/?size=250x250&bgcolor=FFFFFF&margin=50&data=" . urlencode($studentId);

        // Email body
        $mail->Body = '
        <div style="font-family: Arial, Helvetica, sans-serif; color: #222;">
            <div style="max-width:500px; margin:auto; border:1px solid #E5E5E5; font-size: 16px; background:#fafbfc; border-radius:8px; box-shadow:0 1px 5px rgba(0,0,0,0.04); padding:36px 32px 32px 32px;">
                <div style="border-bottom:2px solid #1d3557; padding-bottom:6px; margin-bottom:24px;">
                    <h2 style="margin:0; color:#1d3557; letter-spacing:1px; font-weight:600; font-size:23px;">BMS Award Ceremony 2026 - Seat Number</h2>
                </div>

                <p style="margin:0 0 18px 0;">Dear <strong>' . htmlspecialchars(!empty($callingName) ? $callingName : $nameInFull) . '</strong>,</p>
                
                <p style="color:#2b3137;">  <span style="color:#2980b9;">Congratulations and thank you for the registration for the BMS Award Ceremony 2026.</span>  Please find below your Seat Number allocated for you. <br><br>
                 Please provide the QR code given below at the Registration Desk at the Entrance of the BMICH Lotus Hall before 8.30am to mark your presence and to guide you to your seat </p>
                
                <table style="width:100%; border-collapse:collapse; margin:24px 0 16px 0;">
                    <tr style="background-color:#F1F6FA;">
                        <th align="left" style="padding:9px 13px; border-bottom:1px solid #dde8f5; width:125px;">Student ID</th>
                        <td style="padding:9px 13px; border-bottom:1px solid #dde8f5;">' . htmlspecialchars($studentId) . '</td>
                    </tr>
                    <tr>
                        <th align="left" style="padding:9px 13px; border-bottom:1px solid #dde8f5;">Full Name</th>
                        <td style="padding:9px 13px; border-bottom:1px solid #dde8f5;">' . htmlspecialchars($callingName) . '</td>
                    </tr>
                    <tr>
                        <th align="left" style="padding:9px 13px; border-bottom:1px solid #dde8f5;">Program</th>
                        <td style="padding:9px 13px; border-bottom:1px solid #dde8f5;">' . htmlspecialchars($programName) . '</td>
                    </tr>
                  
                    <tr>
                        <th align="left" style="padding:9px 13px;">Session</th>
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
                    <span style="font-weight:600;">BMS Award Ceremony Team</span>
                </div>
            </div>
        </div>
        ';

        $mail->send();
        return ['success' => true, 'error' => null];
    } catch (Exception $e) {
        return ['success' => false, 'error' => $mail->ErrorInfo];
    }
}

// Function to log email to database
function logEmail($conn, $studentId, $nameInFull, $email, $programName, $seatNo, $sessionTime, $status, $errorMsg, $adminId, $emailType = 'single')
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

// Send emails
$success_count = 0;
$failed_emails = [];

foreach ($emails_to_send as $email_address) {
    $result = sendGraduationEmail($email_address, $studentId, $nameInFull, $seatNo, $programName, $sessionTime, $callingName);

    if ($result['success']) {
        $success_count++;
        // Log successful email
        logEmail($conn, $studentId, $nameInFull, $email_address, $programName, $seatNo, $sessionTime, 'sent', null, $admin_id, 'single');
    } else {
        $failed_emails[] = $email_address;
        // Log failed email
        logEmail($conn, $studentId, $nameInFull, $email_address, $programName, $seatNo, $sessionTime, 'failed', $result['error'], $admin_id, 'single');
    }
}

// Response
if ($success_count > 0 && count($failed_emails) === 0) {
    if ($success_count === 1) {
        echo "Email sent successfully to " . $emails_to_send[0];
    } else {
        echo "Emails sent successfully to both addresses";
    }
} elseif ($success_count > 0 && count($failed_emails) > 0) {
    echo "Email sent to some addresses. Failed: " . implode(', ', $failed_emails);
} else {
    echo "Failed to send email. Please try again.";
}
