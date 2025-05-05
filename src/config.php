<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'dependency/PHPMailer/src/Exception.php';
require 'dependency/PHPMailer/src/PHPMailer.php';
require 'dependency/PHPMailer/src/SMTP.php';

// KEYS
define('CRYPT_KEY', 'umak_scholar_finds_AY_2024-2025_');

// DATABASE CONFIGURATION
$host = "localhost";
$username = "root";
$password = "";
$database = "scholarfindsdb";
$port = "3306";

$conn = new mysqli($host, $username, $password, $database, $port);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// PHP MAILER CONFIGURATION
$mail = new PHPMailer(true);

try {
    $mail->isSMTP();
    $mail->Host       = 'smtp.gmail.com';                   // Set your SMTP server
    $mail->SMTPAuth   = true;
    $mail->Username   = 'renzjan.moncinilla@umak.edu.ph';   // SMTP username
    $mail->Password   = 'lqdn wude utoj smds';              // SMTP password
    $mail->SMTPSecure = 'tls';                              // Encryption: 'ssl' or 'tls'
    $mail->Port       = 587;                                // TCP port to connect to

    $mail->setFrom('no-reply@scholarfinds.com', 'Scholar Finds');
} catch (Exception $e) {
}

// CRYPTOGRAPHY FUNCTIONS
function encrypt($data) {
    $method = 'AES-256-CBC';
    $iv = openssl_random_pseudo_bytes(openssl_cipher_iv_length($method));
    $encrypted = openssl_encrypt($data, $method, CRYPT_KEY, 0, $iv);
    return base64_encode($iv . $encrypted);
}

function decrypt($encryptedData) {
    $method = 'AES-256-CBC';
    $data = base64_decode($encryptedData);
    $iv_length = openssl_cipher_iv_length($method);
    $iv = substr($data, 0, $iv_length);
    $encrypted = substr($data, $iv_length);
    return openssl_decrypt($encrypted, $method, CRYPT_KEY, 0, $iv);
}