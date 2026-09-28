<?php
include("../database/connection.php");

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require '../vendor/autoload.php';  // Composer autoload

// ----------------------
// 1️⃣ Fetch POST data
// ----------------------
$student_id = $_POST['student_id'] ?? '';
$program_name = $_POST['program_name'] ?? '';
$graduation_fee = floatval($_POST['graduation_fee'] ?? 0);
$free_ticket_count = intval($_POST['free_ticket_count'] ?? 2);
$extra_ticket_count = intval($_POST['extra_ticket_count'] ?? 0);
$extra_ticket_fee = floatval($_POST['extra_ticket_fee'] ?? 0);
$total_amount = floatval($_POST['total_amount'] ?? 0);
$receipt_number = $_POST['receipt_number'] ?? '';
$student_name = $_POST['student_name'] ?? '';
$student_email = $_POST['student_email'] ?? '';
$phone_no = $_POST['phone_no'] ?? '';

// ----------------------
// 2️⃣ Basic validation
// ----------------------
if (empty($student_id) || empty($program_name) || $total_amount <= 0) {
    echo json_encode(['success' => false, 'message' => 'Invalid payment data']);
    exit;
}

// ----------------------
// 3️⃣ Check student exists
// ----------------------
$stmt = $conn->prepare("SELECT * FROM registered_students WHERE student_id = ?");
$stmt->bind_param("s", $student_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo json_encode(['success' => false, 'message' => 'Student not found']);
    exit;
}

// ----------------------
// 4️⃣ Insert payment record
// ----------------------
$stmt2 = $conn->prepare("
    INSERT INTO payment_records 
    (student_id, program_name, graduation_fee, free_ticket_count, extra_ticket_count, extra_ticket_fee, total_amount, receipt_number, payment_date)
    VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW())
");
$stmt2->bind_param(
    "ssdiidds",
    $student_id,
    $program_name,
    $graduation_fee,
    $free_ticket_count,
    $extra_ticket_count,
    $extra_ticket_fee,
    $total_amount,
    $receipt_number
);

if (!$stmt2->execute()) {
    echo json_encode(['success' => false, 'message' => 'Failed to record payment: ' . $stmt2->error]);
    exit;
}

// ----------------------
// 5️⃣ Update graduation payment status
// ----------------------
$stmt3 = $conn->prepare("UPDATE registered_students SET graduation_payment_status='paid' WHERE student_id = ?");
$stmt3->bind_param("s", $student_id);
$stmt3->execute();

// ----------------------
// 6️⃣ Send email with PDF attachment
// ----------------------
$mail = new PHPMailer(true);

try {
    $mail->isSMTP();
    $mail->Host       = 'smtp.office365.com';
    $mail->SMTPAuth   = true;
    $mail->Username   = 'bmsgraduation@bms.ac.lk';
    $mail->Password   = 'nvjqswbrtcccghph'; // SMTP password or app password
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port       = 587;

    $mail->setFrom('bmsgraduation@bms.ac.lk', 'BMS Graduation');
    $mail->addAddress($student_email);

    // Attach PDF uploaded from browser
    if (isset($_FILES['pdf_file']) && $_FILES['pdf_file']['error'] === 0) {
        $receipt_folder = '../receipts/'; // Define your receipt storage folder
        $filename = "Receipt_{$receipt_number}.pdf";
        $target_path = $receipt_folder . $filename;
        $tmp_name = $_FILES['pdf_file']['tmp_name'];

        // Read the content of the uploaded PDF file
        $pdfData = file_get_contents($tmp_name);

        if ($pdfData !== false) {
            // Ensure the directory exists
            if (!is_dir($receipt_folder)) {
                // Create directory recursively with full permissions (0777 for development, adjust for production)
                if (!mkdir($receipt_folder, 0777, true)) {
                    error_log("Failed to create receipt directory: {$receipt_folder}");
                    // Optionally, handle this error more gracefully, e.g., by exiting or informing the user.
                }
            }

            // Store the PDF in the receipt folder
            if (is_dir($receipt_folder) && file_put_contents($target_path, $pdfData) === false) {
                error_log("Failed to save PDF receipt to folder: {$target_path}");
                // The email attachment will still proceed if $pdfData was read successfully.
            }

            // Attach the PDF content to the email
            $mail->addStringAttachment($pdfData, $filename);
        } else {
            error_log("Failed to read uploaded PDF temporary file: {$tmp_name}");
            // If PDF data couldn't be read, it cannot be attached or saved.
        }
    }

    $mail->isHTML(true);
    $mail->Subject = "BMS Graduation Payment Confirmation - Receipt #{$receipt_number}";
    $mail->Body = "
        <p>Dear {$student_name},</p>
        <p>We are pleased to inform you that your graduation payment has been successfully recorded.</p>
        <p>Please find the receipt attached.</p>
        <p>Receipt Number: <strong>{$receipt_number}</strong></p>
        <p>Total Amount Paid: <strong>Rs. {$total_amount}</strong></p>
        <p>Thank you!</p>
    ";

    $mail->send();
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => "Mailer Error: {$mail->ErrorInfo}"
    ]);
    exit;
}

// ----------------------
// 7️⃣ Return success response
// ----------------------
echo json_encode([
    'success' => true,
    'message' => 'Payment recorded and receipt sent successfully',
    'receiptNumber' => $receipt_number
]);

// ----------------------
// 8️⃣ Close statements & connection
// ----------------------
$stmt->close();
$stmt2->close();
$stmt3->close();
$conn->close();
