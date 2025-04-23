<?php

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