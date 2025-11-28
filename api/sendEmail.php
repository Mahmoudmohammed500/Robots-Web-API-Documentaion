<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Content-Type");
header("Access-Control-Allow-Methods: POST, OPTIONS");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

require __DIR__ . "/../vendor/autoload.php";


use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Read request
$input = json_decode(file_get_contents("php://input"), true);

$email   = $input['email']   ?? null;
$message = $input['message'] ?? null;

if (!$email || !$message) {
    http_response_code(400);
    echo json_encode([
        "status" => "error",
        "message" => "Required fields: email & message"
    ]);
    exit;
}

// Fixed subject
$subject = "Alert from Alpha Robots Control Web";

$mail = new PHPMailer(true);

try {
    // SMTP settings
    $mail->isSMTP();
    $mail->Host       = 'smtp.gmail.com';
    $mail->SMTPAuth   = true;
    $mail->Username   = 'mahmoudmohammed2652001@gmail.com'; 
    $mail->Password   = 'jmnb hjpo utbs javh';
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port       = 587;

    // Receiver = the email sent from request
    $mail->setFrom('mahmoudmohammed2652001@gmail.com', 'Alpha Robots Control Web System');
    $mail->addAddress($email);

    // Email format
    $mail->isHTML(true);
    $mail->Subject = $subject;
    $mail->Body    = nl2br($message);

    $mail->send();

    echo json_encode([
        "status" => "success",
        "message" => "Email successfully sent to $email"
    ]);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        "status" => "error",
        "message" => "Error sending email: " . $mail->ErrorInfo
    ]);
}
