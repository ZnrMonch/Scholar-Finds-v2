<?php
require_once('config.php');

if (isset($_POST['save-cp'])) {
    $newBg = 'bg-' . $_POST['bg'];
    $newDp = $_POST['dp'];
    $userId = $_POST['userid'];
    $newPersonalization = "$newDp-$newBg";

    $stmt = $conn->prepare("UPDATE accounts SET personalization = ? WHERE user_id = ?");
    $stmt->bind_param("si", $newPersonalization, $userId);
    $stmt->execute();
    $stmt->close();

    header("Location: profile.php");
    exit();
}