<?php
require_once('config.php');

if (isset($_POST['save-cp'])) {
    $newBg = 'bg-' . $_POST['bg'];
    $newDp = substr($_POST['dp'], 2);
    $userId = $_COOKIE['id'];
    $newPersonalization = "$newDp-$newBg";

    $stmt = $conn->prepare("UPDATE accounts SET personalization = ? WHERE user_id = ?");
    $stmt->bind_param("si", $newPersonalization, $userId);
    $stmt->execute();
    $stmt->close();

    $_SESSION['success'] = "Preferences updated successfully!";
    header("Location: profile.php#preferences");
    exit();
}

if (isset($_POST['notify-save']) || isset($_POST['interface-save'])) {
    $new_theses = $_POST['new-theses'];
    $updates = $_POST['updates'];
    $usage = $_POST['usage'];
    $default_layout = $_POST['default-layout'];
    $music = $_POST['music'];

    $preferences = array_filter([$new_theses, $updates, $usage, $default_layout, $music]);
    $preferences_str = implode(',', $preferences);  
    $userId = $_COOKIE['id'];

    $stmt = $conn->prepare("UPDATE accounts SET preferences = ? WHERE user_id = ?");
    $stmt->bind_param("si", $preferences_str, $userId);
    $stmt->execute();
    $stmt->close();

    $_SESSION['success'] = "Preferences updated successfully!";
    header("Location: profile.php#preferences");
    exit();
}
