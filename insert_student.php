<?php
include('./database/connection.php');

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use Endroid\QrCode\Builder\Builder;
use Endroid\QrCode\Encoding\Encoding;
use Endroid\QrCode\ErrorCorrectionLevel;
use Endroid\QrCode\RoundBlockSizeMode;
use Endroid\QrCode\Writer\PngWriter;

require 'vendor/autoload.php';

// Load environment variables from .env (kept outside version control)
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();
$dotenv->required([
    'SMTP_HOST',
    'SMTP_PORT',
    'SMTP_USERNAME',
    'SMTP_PASSWORD',
    'MAIL_FROM_ADDRESS',
    'MAIL_FROM_NAME',
])->notEmpty();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $student_id = strtoupper(trim($_POST['student_id'] ?? ''));
    $dob = trim($_POST['dob'] ?? '');
    $name_in_full = trim($_POST['name_in_full'] ?? '');
    $title = trim($_POST['title'] ?? '');
    $calling_name = trim($_POST['calling_name'] ?? '');
    $confirmation = (isset($_POST['confirmation']) && $_POST['confirmation'] === '1') ? 1 : 0;
    $program_name = trim($_POST['program'] ?? '');
    $given_email_add = trim($_POST['email_address_given'] ?? '');
    $email_address = trim($_POST['email_address'] ?? '');
    $phone_no = trim($_POST['phone_no'] ?? '');
    $student_meals = trim($_POST['student_meals'] ?? '');
    $guest_meals = trim($_POST['guest_meals'] ?? '');
    $guest_meals_02 = trim($_POST['guest_meals_02'] ?? '');

    // Validation
    if (empty($student_id) || empty($dob) || empty($name_in_full) || empty($email_address) || empty($student_meals) || empty($guest_meals) || empty($guest_meals_02)) {
        echo 'All required fields must be filled.';
        exit;
    }

    if (empty($title) || !in_array($title, ['Mr.', 'Ms.', 'Mrs.', 'Miss.'], true)) {
        echo 'Please select a valid title.';
        exit;
    }

    if (empty($calling_name) || strlen($calling_name) < 2) {
        echo 'Please enter a valid calling name (at least 2 characters).';
        exit;
    }

    // Validate calling name format: letters, spaces, hyphens, apostrophes only
    if (!preg_match("/^[\p{L}\s\-']+$/u", $calling_name)) {
        echo 'Calling name can only contain letters, spaces, hyphens and apostrophes.';
        exit;
    }

    if ($confirmation !== 1) {
        echo 'You must confirm that the calling name is accurate to proceed.';
        exit;
    }

    if (!filter_var($email_address, FILTER_VALIDATE_EMAIL) || ($given_email_add && !filter_var($given_email_add, FILTER_VALIDATE_EMAIL))) {
        echo 'Invalid email address.';
        exit;
    }

    // Format phone
    $phone_no = preg_replace('/[^0-9]/', '', $phone_no);
    if (strlen($phone_no) < 9) {
        echo 'Please enter a valid mobile number.';
        exit;
    }
    if (strlen($phone_no) >= 9) {
        $phone_no = '+94' . substr($phone_no, -9);
    }

    $crsfee_payment_status = trim($_POST['crsfee_payment_status'] ?? '');
    $graduation_payment_status = trim($_POST['graduation_payment_status'] ?? 'Not-Completed');

    // Look up the program's session from data_tables (server-side, not trusted from the form)
    $session = null;
    $sessStmt = $conn->prepare("SELECT `session` FROM data_tables WHERE TRIM(programName) = TRIM(?) ORDER BY active DESC, id ASC LIMIT 1");
    $sessStmt->bind_param("s", $program_name);
    $sessStmt->execute();
    $sessStmt->bind_result($sessionValue);
    if ($sessStmt->fetch()) {
        $session = $sessionValue;
    }
    $sessStmt->close();

    $stmt = $conn->prepare("
    INSERT INTO registered_students
    (student_id, dob, name_in_full, title, calling_name, confirmation, program_name, `session`, given_email_add, email_address, phone_no, crsfee_payment_status, graduation_payment_status, student_meals, guest_meals, guest_meals_02)
    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
    ON DUPLICATE KEY UPDATE
        dob = VALUES(dob),
        name_in_full = VALUES(name_in_full),
        title = VALUES(title),
        calling_name = VALUES(calling_name),
        confirmation = VALUES(confirmation),
        program_name = VALUES(program_name),
        `session` = VALUES(`session`),
        given_email_add = VALUES(given_email_add),
        email_address = VALUES(email_address),
        phone_no = VALUES(phone_no),
        crsfee_payment_status = VALUES(crsfee_payment_status),
        graduation_payment_status = VALUES(graduation_payment_status),
        student_meals = VALUES(student_meals),
        guest_meals = VALUES(guest_meals),
        guest_meals_02 = VALUES(guest_meals_02)
");

    $stmt->bind_param(
        "sssssissssssssss",
        $student_id,
        $dob,
        $name_in_full,
        $title,
        $calling_name,
        $confirmation,
        $program_name,
        $session,
        $given_email_add,
        $email_address,
        $phone_no,
        $crsfee_payment_status,
        $graduation_payment_status,
        $student_meals,
        $guest_meals,
        $guest_meals_02
    );


    if ($stmt->execute()) {

        // --- Update old_student_db status to 'registered' ---
        $updateOld = $conn->prepare("UPDATE old_student_db SET status = 'registered' WHERE student_id = ?");
        $updateOld->bind_param("s", $student_id);
        $updateOld->execute();
        $updateOld->close();
        // --- Prepare QR Code Image with only student_id ---
        // PHPMailer object is created here so the QR can be embedded in the email
        $mail = new PHPMailer(true);

        $qr_embedded = false;

        try {
            $qr_result = (new Builder(
                writer: new PngWriter(),
                data: $student_id,
                encoding: new Encoding('UTF-8'),
                errorCorrectionLevel: ErrorCorrectionLevel::High,
                size: 300,
                margin: 40, // white padding around the QR (increase for more, decrease for less)
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
            // A QR problem must never block the registration email
            error_log('QR generation failed for student ' . $student_id . ': ' . $qrError->getMessage());
        }

        // Used in the email body: <img src='$qrCodeImage'>
        $qrCodeImage = $qr_embedded
            ? 'cid:student_qr'
            : "https://api.qrserver.com/v1/create-qr-code/?size=250x250&bgcolor=FFFFFF&margin=50&data=" . urlencode($student_id);

        // Session text for the email (e.g. SESSION_01 -> Session 01)
        $sessionHtml = '';
        if (!empty($session)) {
            $sessionLabel = ucwords(strtolower(str_replace('_', ' ', $session)));
            $sessionHtml = "<p style='font-size:30px; font-weight: 900; color:#1e3a8a; margin:0 0 20px;'>" . htmlspecialchars($sessionLabel) . "</p>";
        }
        // Path to the banner image
        $bannerImagePath = __DIR__ . '/images/graduation_nov_2026.jpg';

        // --- Build HTML Email ---
        // --- Build Professional HTML Email ---

        $emailBody = "
            <!DOCTYPE html>
            <html lang='en'>
            <head>
                <meta charset='UTF-8'>
                <meta name='viewport' content='width=device-width, initial-scale=1.0'>
                <style>
                    * {
                        margin: 0;
                        padding: 0;
                        box-sizing: border-box;
                    }
                    body {
                        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
                        background-color: #f4f7f9;
                        margin: 0;
                        padding: 20px;
                        line-height: 1.6;
                    }
                    .email-wrapper {
                        max-width: 600px;
                        margin: 0 auto;
                        background-color: #ffffff;
                        border-radius: 8px;
                        overflow: hidden;
                        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
                    }
                    .email-header {
                        background: linear-gradient(135deg, #1e3a8a 0%, #3b82f6 100%);
                        padding: 0;
                        text-align: center;
                    }
                    .email-header img {
                        width: 100%;
                        height: auto;
                        display: block;
                    }
                    .email-title {
                        background-color: #ffffffff;
                        color: #000000ff;
                        padding: 10px 20px;
                        text-align: center;
                    }
                    .email-title h1 {
                        font-size: 28px;
                        margin-bottom: 8px;
                        font-weight: 600;
                    }
                    .email-title p {
                        font-size: 16px;
                        opacity: 0.95;
                        margin: 0;
                    }
                    .email-content {
                        padding: 35px 30px;
                        color: #333333;
                    }
                    .greeting {
                        font-size: 18px;
                        margin-bottom: 20px;
                        color: #1e3a8a;
                    }
                    .greeting strong {
                        color: #1e40af;
                    }
                    .message-text {
                        font-size: 15px;
                        color: #555555;
                        margin-bottom: 25px;
                        line-height: 1.8;
                    }
                    .qr-section {
                        background-color:rgb(255, 255, 255);
                        border: 2px dashed #cbd5e1;
                        border-radius: 8px;
                        padding: 25px;
                        text-align: center;
                        margin: 25px 0;
                    }
                    .qr-section p {
                        font-size: 15px;
                        color: #334155;
                        margin-bottom: 20px;
                        font-weight: 500;
                    }
                    .qr-section img {
                        width: 200px;
                        height: 200px;
                    
                    
                        padding: 10px;
                        background-color: #ffffff;
                    }
                    .qr-label {
                        margin-top: 15px;
                        font-size: 13px;
                        color: #64748b;
                        font-style: italic;
                    }
                    .important-note {
                        background-color: #fef3c7;
                        border-left: 4px solid #f59e0b;
                        padding: 15px 20px;
                        margin: 25px 0;
                        border-radius: 4px;
                    }
                    .important-note p {
                        margin: 0;
                        font-size: 14px;
                        color: #92400e;
                    }
                    .important-note strong {
                        color: #78350f;
                    }
                    .details-box {
                        background-color: #f1f5f9;
                        padding: 20px;
                        border-radius: 6px;
                        margin: 20px 0;
                    }
                    .details-box h3 {
                        color: #1e3a8a;
                        font-size: 16px;
                        margin-bottom: 12px;
                    }
                    .details-box p {
                        font-size: 14px;
                        color: #475569;
                        margin: 8px 0;
                    }
                    .email-footer {
                    background-color: #1e293b;
                        color: #cbd5e1;
                        padding: 25px 30px;
                        text-align: center;
                    }
                    .contact-info {
                        margin-bottom: 15px;
                    }
                    .contact-info p {
                        font-size: 14px;
                        margin: 8px 0;
                    }
                    .contact-info a {
                        color: #60a5fa;
                        text-decoration: none;
                        font-weight: 500;
                    }
                    .contact-info a:hover {
                        text-decoration: underline;
                    }
                    .signature {
                        margin: 20px 0;
                        font-size: 14px;
                    }
                    .copyright {
                        font-size: 12px;
                        color: #94a3b8;
                        margin-top: 15px;
                        padding-top: 15px;
                        border-top: 1px solid #334155;
                    }
                </style>
            </head>
            <body>
                <div class='email-wrapper'>
                    <!-- Header with Banner -->
                    <div class='email-header'>
                        <img src='cid:bmslogo' alt='BMS Graduation Banner' style='width:100%; height:auto; display:block;'>
                    </div>
                    
                    <!-- Title Section -->
                    <div class='email-title'>
                        <h1>BMS DEGREE CONVOCATION 2026</h1>
                        <p>Registration Confirmation</p>
                    </div>
                    
                    <!-- Main Content -->
                    <div class='email-content'>
                        <div class='greeting'>
                            Dear <strong>$name_in_full</strong>,
                        </div>
                        
                        <div class='message-text'>
                            <p>Congratulations! We are delighted to confirm your successful registration for the <strong>BMS DEGREE CONVOCATION 2026</strong>.</p>
                        </div>
                        
                    
                        <!-- Registration & Payment Deadline -->
                        <table role='presentation' width='100%' cellpadding='0' cellspacing='0' border='0' style='margin:25px 0; border:1px solid #e2e8f0; border-radius:8px; border-collapse:separate; overflow:hidden;'>
                            <tr>
                                <td style='background-color:#1e3a8a; color:#ffffff; padding:12px 20px; font-size:16px; font-weight:600; text-align:center;'>
                                    Registration and Payment Deadline
                                </td>
                            </tr>
                            <tr>
                                <td style='background-color:#fef2f2; padding:20px; text-align:center; border-bottom:1px solid #e2e8f0;'>
                                    <p style='margin:0; font-size:28px; font-weight:800; color:#b91c1c; letter-spacing:1px;'>30/10/2026</p>
                                    <p style='margin:8px 0 0; font-size:14px; color:#7f1d1d;'>(Kindly make the payments on or before this date)</p>
                                </td>
                            </tr>
                            <tr>
                                <td style='background-color:#ffffff; padding:18px 20px; text-align:center; font-size:14px; color:#334155; line-height:1.7;'>
                                    Kindly visit the <strong>BMS Finance Department</strong> to make the payment by scanning your QR code.
                                </td>
                            </tr>
                        </table>

                          <!-- QR Code Section -->
                        <div class='qr-section' style='background-color: #ffffff;'>
                            <p><strong>Your Payment QR Code</strong></p>
                            <p>Please present this QR code to the cashier when making your graduation fee payment:</p>
                            $sessionHtml
                            <img src='$qrCodeImage' alt='Payment QR Code'>
                        </div>

                        <!-- Finance Department Office Hours -->
                        <table role='presentation' width='100%' cellpadding='0' cellspacing='0' border='0' style='margin:25px 0; border:1px solid #e2e8f0; border-radius:8px; border-collapse:separate; overflow:hidden;'>
                            <tr>
                                <td colspan='2' style='background-color:#f1f5f9; color:#1e3a8a; padding:12px 20px; font-size:16px; font-weight:600; text-align:center; border-bottom:1px solid #e2e8f0;'>
                                    Finance Department Office Hours
                                </td>
                            </tr>
                            <tr>
                                <td style='padding:14px 20px; font-size:14px; color:#334155; font-weight:600; border-bottom:1px solid #e2e8f0; width:45%;'>Monday to Saturday</td>
                                <td style='padding:14px 20px; font-size:14px; color:#475569; border-bottom:1px solid #e2e8f0;'>9.00 am &ndash; 4.30 pm</td>
                            </tr>
                            <tr>
                                <td style='padding:14px 20px; font-size:14px; color:#334155; font-weight:600; width:45%;'>Sunday</td>
                                <td style='padding:14px 20px; font-size:14px; color:#475569;'>9.00 am &ndash; 12.30 pm</td>
                            </tr>
                        </table>

                        <!-- Important Note -->
                        <div class='important-note'>
                            <p><strong> Important:</strong> Please save this email for your records. You will need to present the QR code above during the payment process.</p>
                        </div>
                        
                    </div>
                    
                    <!-- Footer -->
                    <div class='email-footer'>
                        <div class='contact-info'>
                            <p><strong>Need Assistance?</strong></p>
                            <p>If you have any questions or concerns, please feel free to contact us:</p>
                            <p>Email: <a href='mailto:graduation@bms.ac.lk'>graduation@bms.ac.lk</a></p>
                        </div>
                        
                        <div class='signature'>
                            <p>Warm regards,</p>
                            <p><strong>Graduation Team</strong></p>
                        </div>
                        
                        <div class='copyright'>
                            <p>&copy; 2026 BMS (Business Management School). All rights reserved.</p>
                        </div>
                    </div>
                </div>
            </body>
            </html>
            ";

        // --- Send Email via PHPMailer ---
        try {
            $mail->isSMTP();
            $mail->Host       = $_ENV['SMTP_HOST'];
            $mail->SMTPAuth   = true;
            $mail->Username   = $_ENV['SMTP_USERNAME'];
            $mail->Password   = $_ENV['SMTP_PASSWORD'];
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port       = (int) $_ENV['SMTP_PORT'];

            $mail->setFrom($_ENV['MAIL_FROM_ADDRESS'], $_ENV['MAIL_FROM_NAME']);

            // Add recipients conditionally
            if (!empty($given_email_add) && strtolower($given_email_add) !== strtolower($email_address)) {
                // If emails are different, send to both
                $mail->addAddress($email_address);
                $mail->addAddress($given_email_add);
            } else {
                // If same or given_email_add empty, send to main email only
                $mail->addAddress($email_address);
            }

            $mail->addEmbeddedImage($bannerImagePath, 'bmslogo');

            $mail->isHTML(true);
            $mail->Subject = "BMS DEGREE CONVOCATION 2026 - Registration Confirmation";
            $mail->Body = $emailBody;

            $mail->send();
            echo 'Registration successful';
        } catch (Exception $e) {
            error_log('Mail error for student ' . $student_id . ': ' . $mail->ErrorInfo);
            echo 'Registration saved, but the confirmation email could not be sent.';
        }
    } else {
        echo 'Database Error: ' . htmlspecialchars($stmt->error);
    }

    $stmt->close();
    $conn->close();
} else {
    echo 'Invalid request method.';
}
