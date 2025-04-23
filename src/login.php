<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'dependency/PHPMailer/src/Exception.php';
require 'dependency/PHPMailer/src/PHPMailer.php';
require 'dependency/PHPMailer/src/SMTP.php';
require_once 'config.php';
session_start();

// ================================================================================================================================ LOGIN FORM
if (isset($_POST['login'])) {
    $email = trim($_POST['lemail']);
    $password = trim($_POST['lpassword']);

    $stmt = $conn->prepare("SELECT * FROM accounts WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        $stmt->close();
        
        if (
            ($user['role'] === 'regular' && $user['password'] !== decrypt($password)) ||
            (($user['role'] === 'admin' || $user['role'] === 'superadmin') && $user['password'] !== $password)  
        ) {
            $_SESSION['log-error'] = 'Error: Incorrect password!';
        } else {
            $stmt = $conn->prepare("SELECT user_id FROM accounts WHERE email = ?");
            $stmt->bind_param("s", $email);
            $stmt->execute();
            $result = $stmt->get_result();
            $userId = $result->fetch_assoc()['user_id'] ?? null;
            $stmt->close();
            
            $_SESSION['success'] = 'Success! You are now logged in.';
            setcookie("id", $userId, time() + (86400 * 30), "/");
            setcookie("role", $user['role'], time() + (86400 * 30), "/");;
        }
        
    } else {
        $_SESSION['log-error'] = 'Error: Email does not exist!';
    }

    $_SESSION['activeForm'] = 'loginform';
    header("Location: access.php");
    exit();
}

// ================================================================================================================================ REGISTER FORM
if (isset($_POST['register'])) {
    $username = trim($_POST['rusername']);
    $name = trim($_POST['rname']);
    $email = trim($_POST['remail']);
    $password = encrypt(trim($_POST['rpassword']));
    $membership = trim($_POST['membership']);

    if (isset($_POST['g-recaptcha-response']) && !empty($_POST['g-recaptcha-response'])) {
        $verifyResponse = file_get_contents('https://www.google.com/recaptcha/api/siteverify?secret='. "6LdD-hkrAAAAAD-Y3PZCSNd318vaIRHUQt3uPxdQ" .'&response='.$_POST['g-recaptcha-response']);
        $responseData = json_decode($verifyResponse);

        if($responseData->success) {
            $stmt = $conn->prepare("SELECT email, username FROM accounts WHERE email = ? OR username = ?");
            $stmt->bind_param("ss", $email, $username);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($row = $result->fetch_assoc()) {
                if ($row['email'] === $email) {
                    $_SESSION['reg-error'] = 'Error: Email already exists!';
                } else if ($row['username'] === $username) {
                    $_SESSION['reg-error'] = 'Error: Username already taken!';
                }
            } else if (!str_ends_with($email, '@umak.edu.ph') && !str_ends_with($email, '@gmail.com')) {
                $_SESSION['reg-error'] = 'Error: Invalid email format.';
            } else if ($_POST['secure-level'] < 2) { 
                $_SESSION['reg-error'] = 'Error: Password is too weak!';
            } else {
                $stmt->close();

                $date = date('Y-m-d');
                $stmt = $conn->prepare("INSERT INTO accounts (date_joined, membership, username, name, email, password) VALUES (?, ?, ?, ?, ?, ?)");
                $stmt->bind_param("ssssss", $date, $membership, $username, $name, $email, $password);
                $stmt->execute();
                $stmt->close();

                $stmt = $conn->prepare("SELECT user_id FROM accounts WHERE email = ?");
                $stmt->bind_param("s", $email);
                $stmt->execute();
                $result = $stmt->get_result();
                $userId = $result->fetch_assoc()['user_id'] ?? null;
                $stmt->close();
                
                $_SESSION['success'] = 'Success! Account created successfully. Welcome user!';
                setcookie("id", $userId, time() + (86400 * 30), "/");
                setcookie("role", "regular", time() + (86400 * 30), "/");

                $userId = '';
                $stmt = $conn->prepare("SELECT user_id FROM accounts WHERE email = ?");
                $stmt->bind_param('s', $userEmail);
                $stmt->execute();
                $stmt->bind_result($userId);
                $stmt->fetch();
                $stmt->close();

                $lastRefId = '';
                $stmt = $conn->prepare("SELECT reference_id FROM logs WHERE reference_id LIKE 'ACT#%' ORDER BY CAST(SUBSTRING_INDEX(reference_id, '#', -1) AS UNSIGNED) DESC LIMIT 1");
                $stmt->execute();
                $stmt->bind_result($lastRefId);
                $stmt->fetch();
                $stmt->close();

                if ($lastRefId) {
                    $lastNum = (int)substr($lastRefId, 4);
                    $newId = 'ACT#' . str_pad($lastNum + 1, 6, '0', STR_PAD_LEFT);
                } else {
                    $newId = 'ACT#000001';
                }

                $type = 'account';
                $operation = 'added';
                $details = "Created a new user with an ID of [ID#" . str_pad($userId, 4, '0', STR_PAD_LEFT) . "].";

                $stmt = $conn->prepare("INSERT INTO logs (reference_id, type, operation, date, details, initiator) VALUES (?, ?, ?, NOW(), ?, ?)");
                $stmt->bind_param('sssss', $newId, $type, $operation, $details, $email);
                $stmt->execute();
                $stmt->close();
            }
        } else {
            $_SESSION['reg-error'] = 'Error! Robot verification failed, please try again.';
        }

    } else {
        $_SESSION['reg-error'] = 'Error! Please check the reCAPTCHA checkbox.'; 
    }
    
    $_SESSION['activeForm'] = 'regisform';
    header("Location: access.php");
    exit();
}

// ================================================================================================================================ FORGET PASSWORD FORM
if (isset($_POST['fpw']) || isset($_POST['send-otp'])) {
    $email = trim($_POST['femail']); 

    if (!empty($_POST['otp'])) {
        $otpInput = trim($_POST['otp']);
        $otpCookie = array_values(array_filter(array_keys($_COOKIE), fn($k) => str_starts_with($k, 'otp-')))[0] ?? null;
        $realOtp = $otpCookie ? $_COOKIE[$otpCookie] : null;
        if (!$otpCookie) {
            $_SESSION['fpw-error'] = 'Error! OTP is expired.';
        } else if ($otpInput != $realOtp) {
            $_SESSION['fpw-error'] = 'Error! Wrong OTP.';
        } else {
            setcookie($otpCookie, '', time() - 3600, '/');
            setcookie("email", $email, time() + (86400 * 30), "/");
            $_SESSION['fpw-success'] = 'Success! You can now reset your password.';
        }

        if ($_SESSION['fpw-error']) {
            $_SESSION['email'] = $email;
        }

        header("Location: fpw.php");
        exit();
    }

    $stmt = $conn->prepare('SELECT * FROM accounts WHERE email = ?');
    $stmt->bind_param('s', $email);
    $stmt->execute();
    $result = $stmt->get_result();
    $stmt->close();

    if (!isset($_POST['g-recaptcha-response']) && empty($_POST['g-recaptcha-response'])) {
        $_SESSION['fpw-error'] = 'Error! Please check the reCAPTCHA checkbox.'; 
    } else {
        $verifyResponse = file_get_contents('https://www.google.com/recaptcha/api/siteverify?secret='. "6LdD-xkrAAAAAMLhtv-PDRGKNjGO-svkWu8c4ptu" .'&response='.$_POST['g-recaptcha-response']);
        $responseData = json_decode($verifyResponse);

        if (!$responseData->success) {
            $_SESSION['fpw-error'] = 'Error! Robot verification failed, please try again.';
        } else {
            if (!$result->num_rows > 0) {
                $_SESSION['fpw-error'] = 'Error! Email does not exist.';     
            } else {
                $otp = rand(100000, 999999);
                setCookie('otp-' . $email, $otp, time() + 600, '/');

                $message = file_get_contents('fpw-email.php');
                $message = str_replace('not generated', $otp, $message);
                $message = str_replace('no-time', '10 minutes', $message);

                $mail = new PHPMailer(true);
                
                $_SESSION['femail'] = $email;

                try {
                    // Server settings
                    $mail->isSMTP();
                    $mail->Host       = 'smtp.gmail.com';                   // Set your SMTP server
                    $mail->SMTPAuth   = true;
                    $mail->Username   = 'renzjan.moncinilla@umak.edu.ph';   // SMTP username
                    $mail->Password   = 'lqdn wude utoj smds';              // SMTP password
                    $mail->SMTPSecure = 'tls';                              // Encryption: 'ssl' or 'tls'
                    $mail->Port       = 587;                                // TCP port to connect to

                    // Sender and recipient
                    $mail->setFrom('no-reply@scholarfinds.com', 'Scholar Finds');
                    $mail->addAddress($email); // recipient's email

                    // Content
                    $mail->isHTML(true);
                    $mail->Subject = 'Scholar Finds OTP';
                    $mail->Body    = $message;

                    $mail->send();
                    
                    $_SESSION['fpw-success'] = 'Success! Check your email for the OTP.';
                } catch (Exception $e) {
                    $_SESSION['fpw-error'] = "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
                }
            }
        }
    }

    if ($_SESSION['fpw-error']) {
        $_SESSION['email'] = $email;
    }

    header("Location: fpw.php");
    exit();
}

if (isset($_POST['change-pw'])) {
    $email = $_COOKIE['email'] ?? null;
    $newPassword = $_POST['npassword'];

    if (!$email) {
        $_SESSION['fpw-error'] = 'Error! Email not found.';
    } else if ($_POST['secure-level'] < 2) { 
        $_SESSION['fpw-error'] = 'Error: New password is too weak!';
    } else {
        $stmt = $conn->prepare("SELECT role FROM accounts WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        $role = $result->fetch_assoc()['role'] ?? null;
        $stmt->close();

        $finalPassword = ($role === 'regular') ? encrypt($newPassword) : $newPassword;

        $stmt = $conn->prepare("UPDATE accounts SET password = ? WHERE email = ?");
        $stmt->bind_param("ss", $finalPassword, $email);
        $stmt->execute();
        $stmt->close();

        setcookie("email", '', time() - 3600, '/');
        $_SESSION['fpw-success'] = 'Success! Password changed successfully.';
        header("Location: access.php");
        exit();
    }
}
