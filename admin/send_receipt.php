<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require '../vendor/autoload.php';
include("../database/connection.php");

$data = json_decode(file_get_contents('php://input'), true);

$student_id    = $data['student_id'];
$student_name  = $data['student_name'];
$student_email = $data['student_email'];
$program_name  = $data['program_name'];
$receipt_number = $data['receipt_number'];
$total_amount  = $data['total_amount'];
$pdf_base64    = $data['pdf_base64'];

$pdfContent = base64_decode($pdf_base64);

// Send email
$mail = new PHPMailer(true);
try {
    $mail->isSMTP();
    $mail->Host       = 'smtp.office365.com';
    $mail->SMTPAuth   = true;
    $mail->Username   = 'bmsgraduation@bms.ac.lk';
    $mail->Password   = 'nvjqswbrtcccghph';
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port       = 587;

    $mail->setFrom('bmsgraduation@bms.ac.lk', 'BMS Graduation');
    $mail->addAddress('mirshath.mmm@gmail.com');

    $mail->isHTML(true);
    $mail->Subject = "BMS Graduation Payment Confirmation - Receipt #{$receipt_number}";
    $mail->Body = "
        <p>Dear {$student_name},</p>
        <p>Your graduation payment has been successfully recorded.</p>
        <p>Receipt Number: <strong>{$receipt_number}</strong></p>
        <p>Total Amount Paid: <strong>Rs. {$total_amount}</strong></p>
        <p>Please find your receipt attached.</p>
        <p>Thank you!</p>
    ";

    $mail->addStringAttachment($pdfContent, "Receipt_{$receipt_number}.pdf");
    $mail->send();

    echo json_encode(['success' => true, 'message' => 'Receipt sent successfully!']);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Mailer Error: ' . $mail->ErrorInfo]);
}
$conn->close();
