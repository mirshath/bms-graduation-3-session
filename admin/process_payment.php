<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

include("../database/connection.php"); // DB connection
require '../vendor/autoload.php';

// -----------------------------
// Prevent HTML output breaking JSON
// -----------------------------
ini_set('display_errors', 0);
ini_set('log_errors', 1);
error_reporting(E_ALL);
header('Content-Type: application/json');

try {
    // -----------------------------
    // 1️⃣ Collect POST data safely
    // -----------------------------    
    $student_id = $_POST['student_id'] ?? '';
    $program_name = $_POST['program_name'] ?? '';
    $graduation_fee = floatval($_POST['graduation_fee'] ?? 0);
    $free_ticket_count = intval($_POST['free_ticket'] ?? 0);
    $extra_ticket_count = intval($_POST['extra_tickets'] ?? 0);
    $extra_ticket_fee = floatval($_POST['extra_ticket_fee'] ?? 0);
    $total_amount = floatval($_POST['total_amount'] ?? 0);
    $receipt_number = $_POST['receipt_number'] ?? '';
    $pdf_data = $_POST['pdf_data'] ?? '';

    $extra_ticket_fee_total =  $extra_ticket_count *  $extra_ticket_fee;
    if (!$student_id || !$program_name || $total_amount <= 0 || !$pdf_data) {
        throw new Exception('Missing or invalid payment data.');
    }

    // -----------------------------
    // 2️⃣ Check student exists
    // -----------------------------
    $stmt = $conn->prepare("SELECT * FROM registered_students WHERE student_id = ?");
    $stmt->bind_param("s", $student_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 0) {
        throw new Exception('Student not found.');
    }

    $student = $result->fetch_assoc();
    $full_name = $student['name_in_full'];
    $university_email = $student['email_address'];

    if (!$university_email) {
        throw new Exception('Student email not found.');
    }

    // -----------------------------
    // 3️⃣ Save PDF to folder
    // -----------------------------
    $folder = __DIR__ . '/saved_receipts/';
    if (!is_dir($folder)) mkdir($folder, 0777, true);

    $email_date = date('Y-m-d_H-i-s');
    $pdf_name = "Receipt-{$receipt_number}-{$student_id}-{$email_date}.pdf";
    $pdf_path = $folder . $pdf_name;

    file_put_contents($pdf_path, base64_decode($pdf_data));

    // -----------------------------
    // 4️⃣ Insert payment record
    // -----------------------------
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
        $extra_ticket_fee_total, // Corrected: This should be the per-ticket fee, not the total for extra tickets
        $total_amount,
        $receipt_number
    );
    if (!$stmt2->execute()) {
        throw new Exception('Failed to record payment: ' . $stmt2->error);
    }

    // -----------------------------
    // 5️⃣ Update graduation payment status
    // -----------------------------
    $stmt3 = $conn->prepare("UPDATE registered_students SET graduation_payment_status='paid' WHERE student_id = ?");
    $stmt3->bind_param("s", $student_id);
    $stmt3->execute();

    // -----------------------------
    // 6️⃣ Send Email
    // -----------------------------
    $mail = new PHPMailer(true);
    $mail->isSMTP();
    $mail->Host       = 'smtp.office365.com';
    $mail->SMTPAuth   = true;
    $mail->Username   = 'bmsgraduation@bms.ac.lk';
    $mail->Password   = 'nvjqswbrtcccghph';
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port       = 587;

    $mail->setFrom('bmsgraduation@bms.ac.lk', 'BMS Finance Department');
    $mail->addAddress($university_email, $full_name);

    $mail->Subject = "Graduation Payment Receipt (Ref: $receipt_number) - $program_name";

    $body = '
    <html>
    <body style="font-family: Arial, sans-serif; color:#333;">
        <div style="text-align:center; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); padding:20px; color:#fff;">
            <h2>Graduation Payment Receipt</h2>
            <p>Payment Successful</p>
        </div>
        <div style="padding:20px;">
            <p>Dear <strong>' . $full_name . '</strong>,</p>
            <p>Thank you for your payment for Graduation Ceremony 2025. Please find attached receipt.</p>
            <p>Please provide your QR code to collect your invitation.</p>
            <table style="width:100%; border-collapse:collapse; margin-top:20px;">
                <tr>
                    <td style="padding:8px; border:1px solid #ddd;"><strong>Student ID</strong></td>
                    <td style="padding:8px; border:1px solid #ddd;">' . $student_id . '</td>
                </tr>
                <tr>
                    <td style="padding:8px; border:1px solid #ddd;"><strong>Program</strong></td>
                    <td style="padding:8px; border:1px solid #ddd;">' . $program_name . '</td>
                </tr>
                <tr>
                    <td style="padding:8px; border:1px solid #ddd;"><strong>Free Tickets</strong></td>
                    <td style="padding:8px; border:1px solid #ddd;">' . $free_ticket_count . '</td>
                </tr>'
        . ($extra_ticket_count > 0 ? '<tr>
                    <td style="padding:8px; border:1px solid #ddd;"><strong>Extra Tickets</strong></td>
                    <td style="padding:8px; border:1px solid #ddd;">' . $extra_ticket_count . '</td>
                </tr>' : '') .
        '<tr>
                    <td style="padding:8px; border:1px solid #ddd;"><strong>Total Paid</strong></td>
                    <td style="padding:8px; border:1px solid #ddd;">LKR ' . $total_amount . '</td>
                </tr>
            </table>
            <p style="margin-top:20px;">Best regards,<br>BMS Finance Department,<br>BMS Graduation Team 2025</p>
        </div>
    </body>
    </html>';

    $mail->isHTML(true);
    $mail->Body = $body;
    $mail->addAttachment($pdf_path, $pdf_name);
    $mail->send();

    // -----------------------------
    // 7️⃣ Return JSON
    // -----------------------------
    echo json_encode([
        'success' => true,
        'pdf_path' => $pdf_path,
        'pdf_name' => $pdf_name
    ]);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
