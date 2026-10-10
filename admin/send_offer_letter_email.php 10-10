<?php

session_start();

$admin_name = $_SESSION['admin_name'] ?? 'Unknown Admin';
$admin_id   = intval($_SESSION['admin_id'] ?? 0);

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use Endroid\QrCode\Builder\Builder;
use Endroid\QrCode\Encoding\Encoding;
use Endroid\QrCode\ErrorCorrectionLevel;
use Endroid\QrCode\RoundBlockSizeMode;
use Endroid\QrCode\Writer\PngWriter;

include("../database/connection.php");
require '../vendor/autoload.php';

ini_set('display_errors', 0);
ini_set('log_errors', 1);
error_reporting(E_ALL);

header('Content-Type: application/json');

// Load environment variables from .env (project root, one level above this folder)
try {
    $dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/..');
    $dotenv->load();
    $dotenv->required([
        'SMTP_HOST',
        'SMTP_PORT',
        'SMTP_USERNAME',
        'SMTP_PASSWORD',
        'MAIL_FROM_ADDRESS',
    ])->notEmpty();
} catch (\Throwable $envError) {
    error_log('Env load failed: ' . $envError->getMessage());
    echo json_encode([
        'success' => false,
        'payment_saved' => false,
        'email_sent' => false,
        'message' => 'Server configuration error.'
    ]);
    exit;
}

try {

    // =========================================================
    // 1. COLLECT POST DATA
    // =========================================================

    $student_id         = trim($_POST['student_id'] ?? '');
    $invitation_number  = trim($_POST['invitation_number'] ?? '');
    $program_name       = trim($_POST['program_name'] ?? '');
    $graduation_fee     = floatval($_POST['graduation_fee'] ?? 0);
    $free_ticket_count  = intval($_POST['free_ticket'] ?? 0);
    $extra_ticket_count = intval($_POST['extra_tickets'] ?? 0);
    $extra_ticket_fee   = floatval($_POST['extra_ticket_fee'] ?? 0);
    $total_amount       = floatval($_POST['total_amount'] ?? 0);
    $receipt_number      = trim($_POST['receipt_number'] ?? '');
    $pdf_data            = $_POST['pdf_data'] ?? '';

    // =========================================================
    // 2. BASIC VALIDATION
    // =========================================================

    if (
        empty($student_id) ||
        empty($program_name) ||
        $total_amount <= 0 ||
        empty($pdf_data) ||
        empty($receipt_number)
    ) {
        throw new Exception('Missing or invalid payment data.');
    }

    // =========================================================
    // 3. VERIFY STUDENT
    // =========================================================

    $stmt = $conn->prepare("
        SELECT *
        FROM registered_students
        WHERE student_id = ?
        LIMIT 1
    ");

    if (!$stmt) {
        throw new Exception(
            'Student query preparation failed: ' . $conn->error
        );
    }

    $stmt->bind_param("s", $student_id);

    if (!$stmt->execute()) {
        throw new Exception(
            'Student query failed: ' . $stmt->error
        );
    }

    $result = $stmt->get_result();

    if ($result->num_rows === 0) {
        throw new Exception('Student not found.');
    }

    $student = $result->fetch_assoc();

    $full_name       = $student['name_in_full'] ?? '';
    $university_email = trim($student['email_address'] ?? '');

    $stmt->close();

    if (empty($university_email)) {
        throw new Exception('Student email address not found.');
    }

    // Invitation Number from Student Payment Details, else old_student_db / existing row
    if ($invitation_number === '') {
        $stmtInv = $conn->prepare("
            SELECT in_no
            FROM old_student_db
            WHERE student_id = ?
            LIMIT 1
        ");
        if ($stmtInv) {
            $stmtInv->bind_param("s", $student_id);
            if ($stmtInv->execute()) {
                $invRes = $stmtInv->get_result();
                if ($invRes && $invRow = $invRes->fetch_assoc()) {
                    $invitation_number = trim((string)($invRow['in_no'] ?? ''));
                }
            }
            $stmtInv->close();
        }
    }

    if ($invitation_number === '') {
        $invitation_number = trim((string)($student['in_no'] ?? ''));
    }

    // =========================================================
    // 4. CALCULATE EXTRA TICKET TOTAL
    // =========================================================

    $extra_ticket_total =
        $extra_ticket_count * $extra_ticket_fee;

    // =========================================================
    // 5. PREPARE PDF
    // =========================================================

    $email_date = date('Y-m-d_H-i-s');

    $pdf_name =
        "Receipt-" .
        $receipt_number .
        "-" .
        $student_id .
        "-" .
        $email_date .
        ".pdf";

    // Remove possible data URL prefix
    if (strpos($pdf_data, ',') !== false) {
        $pdf_data = substr($pdf_data, strpos($pdf_data, ',') + 1);
    }

    $pdf_binary = base64_decode($pdf_data, true);

    if ($pdf_binary === false) {
        throw new Exception('Invalid PDF data.');
    }

    // =========================================================
    // 6. SAVE PDF
    // =========================================================

    $folder = __DIR__ . '/saved_receipts/';

    if (!is_dir($folder)) {

        if (!mkdir($folder, 0755, true)) {
            throw new Exception(
                'Unable to create saved_receipts directory.'
            );
        }
    }

    if (!is_writable($folder)) {
        throw new Exception(
            'saved_receipts directory is not writable.'
        );
    }

    $pdf_path = $folder . $pdf_name;

    $pdf_saved = file_put_contents(
        $pdf_path,
        $pdf_binary
    );

    if ($pdf_saved === false) {
        throw new Exception(
            'Failed to save PDF receipt.'
        );
    }

    // =========================================================
    // 7. INSERT PAYMENT RECORD
    // =========================================================
    // IMPORTANT:
    // Payment is stored BEFORE sending email.
    // Therefore an email failure will NOT lose the payment.

    $stmt2 = $conn->prepare("
        INSERT INTO payment_records
        (
            student_id,
            program_name,
            graduation_fee,
            free_ticket_count,
            extra_ticket_count,
            extra_ticket_fee,
            total_amount,
            receipt_number,
            payment_date,
            created_by
        )
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW(), ?)
    ");

    if (!$stmt2) {
        throw new Exception(
            'Payment INSERT preparation failed: ' . $conn->error
        );
    }

    $stmt2->bind_param(
        "ssdiiddss",
        $student_id,
        $program_name,
        $graduation_fee,
        $free_ticket_count,
        $extra_ticket_count,
        $extra_ticket_total,
        $total_amount,
        $receipt_number,
        $admin_name
    );

    if (!$stmt2->execute()) {
        throw new Exception(
            'Failed to record payment: ' . $stmt2->error
        );
    }

    $payment_record_id = $stmt2->insert_id;

    $stmt2->close();

    // =========================================================
    // 8. UPDATE STUDENT PAYMENT STATUS
    // =========================================================

    $in_no_for_db = ($invitation_number === '')
        ? null
        : intval($invitation_number);

    if ($in_no_for_db === null) {
        $stmt3 = $conn->prepare("
            UPDATE registered_students
            SET graduation_payment_status = 'paid',
                invitation_collected = 'collected',
                updated_at_invitation = NOW()
            WHERE student_id = ?
        ");

        if (!$stmt3) {
            throw new Exception(
                'Payment status query preparation failed: ' .
                    $conn->error
            );
        }

        $stmt3->bind_param("s", $student_id);
    } else {
        $stmt3 = $conn->prepare("
            UPDATE registered_students
            SET graduation_payment_status = 'paid',
                invitation_collected = 'collected',
                updated_at_invitation = NOW(),
                in_no = ?
            WHERE student_id = ?
        ");

        if (!$stmt3) {
            throw new Exception(
                'Payment status query preparation failed: ' .
                    $conn->error
            );
        }

        $stmt3->bind_param("is", $in_no_for_db, $student_id);
    }

    if (!$stmt3->execute()) {
        throw new Exception(
            'Failed to update payment status: ' .
                $stmt3->error
        );
    }

    $stmt3->close();

    // =========================================================
    // 9. GET SEAT / SESSION INFORMATION
    // =========================================================

    $seat_no     = null;
    $session_time = null;

    $stmtSeat = $conn->prepare("
        SELECT seat_no, session_time
        FROM bulk_data_table
        WHERE student_id = ?
        LIMIT 1
    ");

    if ($stmtSeat) {

        $stmtSeat->bind_param("s", $student_id);

        if ($stmtSeat->execute()) {

            $seatRes = $stmtSeat->get_result();

            if ($seatRes && $seatRes->num_rows > 0) {

                $seatRow = $seatRes->fetch_assoc();

                $seat_no =
                    $seatRow['seat_no'] ?? null;

                $session_time =
                    $seatRow['session_time'] ?? null;
            }
        }

        $stmtSeat->close();
    }

    // =========================================================
    // 9B. GET PROGRAM SESSION (data_tables.session)
    // =========================================================
    // Same lookup as the payment page: program name -> data_tables.session

    $program_session = '';

    $stmtProgSession = $conn->prepare("
        SELECT `session`
        FROM `data_tables`
        WHERE TRIM(`programName`) = TRIM(?)
        ORDER BY `active` DESC, `id` DESC
        LIMIT 1
    ");

    if ($stmtProgSession) {
        $stmtProgSession->bind_param("s", $program_name);

        if ($stmtProgSession->execute()) {
            $progSessionRes = $stmtProgSession->get_result();

            if ($progSessionRes && $progSessionRow = $progSessionRes->fetch_assoc()) {
                $program_session = trim((string)($progSessionRow['session'] ?? ''));
            }
        }

        $stmtProgSession->close();
    }

    // Fallback: the session saved on the student record
    if ($program_session === '') {
        $program_session = trim((string)($student['session'] ?? ''));
    }

    // =========================================================
    // 10. SEND EMAIL
    // =========================================================

    $email_sent = false;
    $email_error = null;

    try {

        $mail = new PHPMailer(true);

        $mail->isSMTP();

        $mail->Host       = $_ENV['SMTP_HOST'];
        $mail->SMTPAuth   = true;
        $mail->Username   = $_ENV['SMTP_USERNAME'];
        $mail->Password   = $_ENV['SMTP_PASSWORD'];

        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = (int) $_ENV['SMTP_PORT'];

        // Optional but useful
        $mail->CharSet = 'UTF-8';

        $mail->setFrom(
            $_ENV['MAIL_FROM_ADDRESS'],
            'BMS Finance Department'
        );

        $mail->addAddress(
            $university_email,
            $full_name
        );

        $mail->isHTML(true);

        $mail->Subject =
            "Graduation Payment Receipt (Ref: " .
            $receipt_number .
            ") - " .
            $program_name;

        // =====================================================
        // EMAIL BODY
        // =====================================================

        $safe_name       = htmlspecialchars($full_name, ENT_QUOTES, 'UTF-8');
        $safe_student_id = htmlspecialchars($student_id, ENT_QUOTES, 'UTF-8');
        $safe_program    = htmlspecialchars($program_name, ENT_QUOTES, 'UTF-8');
        $safe_receipt    = htmlspecialchars($receipt_number, ENT_QUOTES, 'UTF-8');
        $total_formatted = number_format($total_amount, 2);

        // ---------- QR CODE (student ID only, same as the payment page) ----------
        // Generated locally with endroid/qr-code (composer) and embedded in the email.
        $qr_embedded = false;

        try {
            $qr_result = (new Builder(
                writer: new PngWriter(),
                data: $student_id,
                encoding: new Encoding('UTF-8'),
                errorCorrectionLevel: ErrorCorrectionLevel::High,
                size: 300,
                margin: 40,
                roundBlockSizeMode: RoundBlockSizeMode::Margin
            ))->build();

            // Embedded inline -> referenced in the HTML as cid:student_qr
            $mail->addStringEmbeddedImage(
                $qr_result->getString(),
                'student_qr',
                'student-qr-' . $student_id . '.png',
                PHPMailer::ENCODING_BASE64,
                'image/png'
            );

            $qr_embedded = true;
        } catch (\Throwable $qrError) {
            // A QR problem must never block the receipt email
            error_log('QR generation failed for student ' . $student_id . ': ' . $qrError->getMessage());
        }

        if ($qr_embedded) {
            $qr_block = '
                <img src="cid:student_qr" width="180" height="180" alt="Student ID QR Code: ' . $safe_student_id . '"
                     style="display:block; margin:0 auto; width:180px; height:180px; border:0;">';
        } else {
            $qr_block = '
                <p style="margin:0; font-size:14px; color:#555555;">
                    QR code unavailable. Please quote your Student ID at the counter.
                </p>';
        }

        // Session label above the QR, e.g. SESSION_01 -> "Session 01"
        $session_block = '';

        if ($program_session !== '') {
            $session_label = ucwords(strtolower(str_replace('_', ' ', $program_session)));
            $safe_session  = htmlspecialchars($session_label, ENT_QUOTES, 'UTF-8');

            $session_block = '
                            <div style="margin-bottom:16px;">
                                <span style="display:inline-block; background-color:#667eea; color:#ffffff; font-size:16px; font-weight:bold; letter-spacing:1px; padding:8px 22px; border-radius:20px;">
                                    ' . $safe_session . '
                                </span>
                                <div style="font-size:12px; color:#6b7280; margin-top:6px;">Your graduation session</div>
                            </div>';
        }

        // Optional extra-ticket row
        $extra_row = '';
        if ($extra_ticket_count > 0) {
            $extra_row = '
                <tr>
                    <td style="padding:12px 16px; border-bottom:1px solid #eceef5; color:#6b7280; font-size:14px;">Extra Tickets</td>
                    <td align="right" style="padding:12px 16px; border-bottom:1px solid #eceef5; color:#111827; font-size:14px; font-weight:bold;">' . $extra_ticket_count . '</td>
                </tr>';
        }

        $body = <<<HTML
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="color-scheme" content="light only">
<title>Graduation Payment Receipt</title>
</head>
<body style="margin:0; padding:0; background-color:#f0f2f8; font-family:Arial, Helvetica, sans-serif; color:#333333;">

<!-- Preheader (inbox preview text) -->
<div style="display:none; max-height:0; overflow:hidden; opacity:0; font-size:1px; line-height:1px; color:#f0f2f8;">
    Payment received. Show your QR code to collect your invitation.
</div>

<table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" bgcolor="#f0f2f8" style="background-color:#f0f2f8;">
<tr>
<td align="center" style="padding:24px 12px;">

    <table role="presentation" width="600" cellpadding="0" cellspacing="0" border="0" style="width:100%; max-width:600px; background-color:#ffffff; border-radius:10px; overflow:hidden;">

        <!-- HEADER -->
        <tr>
            <td align="center" bgcolor="#667eea" style="background-color:#667eea; padding:32px 24px;">
                <div style="font-size:12px; letter-spacing:2px; color:#dfe4ff; text-transform:uppercase; margin-bottom:8px;">
                BMS DEGREE CONVOCATION 2026
                </div>
                <h1 style="margin:0; font-size:24px; line-height:30px; color:#ffffff; font-weight:bold;">
                    Graduation Payment Receipt
                </h1>
                <div style="margin-top:14px;">
                    <span style="display:inline-block; background-color:#e7f8ee; color:#15803d; font-size:13px; font-weight:bold; padding:6px 16px; border-radius:20px;">
                        &#10003; Payment Successful
                    </span>
                </div>
            </td>
        </tr>

        <!-- GREETING -->
        <tr>
            <td style="padding:28px 32px 8px 32px; font-size:15px; line-height:24px; color:#374151;">
                <p style="margin:0 0 14px 0;">Dear <strong>{$safe_name}</strong>,</p>
                <p style="margin:0 0 14px 0;">
                    Thank you for your payment for <strong>BMS DEGREE CONVOCATION 2026</strong>.
                    Your payment receipt is attached to this email as a PDF.
                </p>
            </td>
        </tr>

     
        <!-- QR CODE -->
        <tr>
            <td align="center" style="padding:24px 32px 8px 32px;">
                <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="border:2px dashed #667eea; border-radius:10px;">
                    <tr>
                        <td align="center" style="padding:22px 16px;">
                            {$session_block}
                            <div style="font-size:13px; font-weight:bold; color:#667eea; text-transform:uppercase; letter-spacing:1px; margin-bottom:4px;">
                                Your Invitation and Tickets
                            </div>

                            <div style="font-size:13px; color:#6b7280; margin-bottom:14px;">
                                Show this at the counter to collect your invitation and tickets
                            </div>

                            <!-- white card keeps the QR scannable even in dark mode -->
                            <table role="presentation" cellpadding="0" cellspacing="0" border="0" align="center" bgcolor="#ffffff" style="background-color:#ffffff; border:1px solid #e5e7eb; border-radius:8px;">
                                <tr>
                                    <td align="center" style="padding:12px;">
                                        {$qr_block}
                                    </td>
                                </tr>
                            </table>
                    
                        </td>
                    </tr>
                </table>
            </td>
        </tr>

           <!-- PAYMENT SUMMARY -->
           <tr>
            <td style="padding:8px 32px 8px 32px;">
                <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="border:1px solid #eceef5; border-radius:8px; border-collapse:separate; overflow:hidden;">
                    <tr>
                        <td colspan="2" bgcolor="#f7f8fd" style="background-color:#f7f8fd; padding:12px 16px; font-size:13px; font-weight:bold; color:#667eea; text-transform:uppercase; letter-spacing:1px; border-bottom:1px solid #eceef5;">
                            Payment Summary
                        </td>
                    </tr>
                    <tr>
                        <td style="padding:12px 16px; border-bottom:1px solid #eceef5; color:#6b7280; font-size:14px;">Receipt No.</td>
                        <td align="right" style="padding:12px 16px; border-bottom:1px solid #eceef5; color:#111827; font-size:14px; font-weight:bold;">{$safe_receipt}</td>
                    </tr>
                    <tr>
                        <td style="padding:12px 16px; border-bottom:1px solid #eceef5; color:#6b7280; font-size:14px;">Student ID</td>
                        <td align="right" style="padding:12px 16px; border-bottom:1px solid #eceef5; color:#111827; font-size:14px; font-weight:bold;">{$safe_student_id}</td>
                    </tr>
                    <tr>
                        <td style="padding:12px 16px; border-bottom:1px solid #eceef5; color:#6b7280; font-size:14px;">Program</td>
                        <td align="right" style="padding:12px 16px; border-bottom:1px solid #eceef5; color:#111827; font-size:14px; font-weight:bold;">{$safe_program}</td>
                    </tr>
                    <tr>
                        <td style="padding:12px 16px; border-bottom:1px solid #eceef5; color:#6b7280; font-size:14px;">Free Tickets</td>
                        <td align="right" style="padding:12px 16px; border-bottom:1px solid #eceef5; color:#111827; font-size:14px; font-weight:bold;">{$free_ticket_count}</td>
                    </tr>
                    {$extra_row}
                    <tr>
                        <td bgcolor="#f7f8fd" style="background-color:#f7f8fd; padding:14px 16px; color:#111827; font-size:15px; font-weight:bold;">Total Paid</td>
                        <td align="right" bgcolor="#f7f8fd" style="background-color:#f7f8fd; padding:14px 16px; color:#667eea; font-size:18px; font-weight:bold;">LKR {$total_formatted}</td>
                    </tr>
                </table>
            </td>
        </tr>

        <!-- SIGN-OFF -->
        <tr>
            <td style="padding:20px 32px 28px 32px; font-size:14px; line-height:22px; color:#374151;">
                Best regards,<br>
                <strong>BMS Finance Department</strong><br>
                BMS Graduation Team 2026
            </td>
        </tr>

        <!-- FOOTER -->
        <tr>
            <td align="center" bgcolor="#f7f8fd" style="background-color:#f7f8fd; padding:16px 24px; font-size:12px; color:#9ca3af; border-top:1px solid #eceef5;">
                This is an automated message. Please keep this email for your records.
            </td>
        </tr>

    </table>

</td>
</tr>
</table>

</body>
</html>
HTML;

        $mail->Body = $body;

        $mail->addStringAttachment(
            $pdf_binary,
            $pdf_name,
            PHPMailer::ENCODING_BASE64,
            'application/pdf'
        );

        // SEND
        $mail->send();

        $email_sent = true;
    } catch (Exception $mailException) {

        $email_sent = false;

        $email_error =
            $mailException->getMessage();

        // Log the actual PHPMailer error
        error_log(
            'Graduation email failed for student ' .
                $student_id .
                ': ' .
                $email_error
        );

        if (isset($mail) && !empty($mail->ErrorInfo)) {

            error_log(
                'PHPMailer ErrorInfo: ' .
                    $mail->ErrorInfo
            );
        }
    }

    // =========================================================
    // 11. INSERT EMAIL LOG
    // =========================================================

    $email_status =
        $email_sent ? 'sent' : 'failed';

    $log_error =
        $email_sent ? null : $email_error;

    $logStmt = $conn->prepare("
        INSERT INTO payment_email_log
        (
            student_id,
            name_in_full,
            email_address,
            program_name,
            seat_no,
            session_time,
            email_type,
            status,
            error_message,
            sent_by
        )
        VALUES (?, ?, ?, ?, ?, ?, 'single', ?, ?, ?)
    ");

    if (!$logStmt) {

        error_log(
            'Email log preparation failed: ' .
                $conn->error
        );
    } else {

        $logStmt->bind_param(
            "ssssssssi",
            $student_id,
            $full_name,
            $university_email,
            $program_name,
            $seat_no,
            $session_time,
            $email_status,
            $log_error,
            $admin_id
        );

        if (!$logStmt->execute()) {

            error_log(
                'Email log insert failed: ' .
                    $logStmt->error
            );
        }

        $logStmt->close();
    }

    // =========================================================
    // 12. RETURN JSON
    // =========================================================

    if ($email_sent) {

        echo json_encode([
            'success' => true,
            'payment_saved' => true,
            'email_sent' => true,
            'payment_record_id' => $payment_record_id,
            'pdf_path' => $pdf_path,
            'pdf_name' => $pdf_name,
            'message' =>
            'Payment recorded and email sent successfully.'
        ]);
    } else {

        // Payment is still successful.
        // Only email failed.

        echo json_encode([
            'success' => true,
            'payment_saved' => true,
            'email_sent' => false,
            'payment_record_id' => $payment_record_id,
            'pdf_path' => $pdf_path,
            'pdf_name' => $pdf_name,
            'message' =>
            'Payment recorded successfully, but the email could not be sent.',
            'email_error' => $email_error
        ]);
    }

    exit;
} catch (Exception $e) {

    error_log(
        'Graduation payment error: ' .
            $e->getMessage()
    );

    echo json_encode([
        'success' => false,
        'payment_saved' => false,
        'email_sent' => false,
        'message' => $e->getMessage()
    ]);

    exit;
}
