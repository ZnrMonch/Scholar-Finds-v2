<?php

require_once 'config.php';

if (isset($_POST['update'])) {
    $name = $_POST['name'];
    $college = $_POST['college'];
    $year = $_POST['year'] ?? $_POST['shs-grade'];
    $section = $_POST['section'] ?? $_POST['shs-section'];
    $yearsection = $year . '-' . $section;
    $bio = $_POST['bio'];
    $id = $_POST['id'];

    $stmt = $conn->prepare("UPDATE accounts SET name = ?, college = ?, yearsection = ?, bio = ? WHERE user_id = ?");
    $stmt->bind_param("ssssi", $name, $college, $yearsection, $bio, $id);
    $stmt->execute();
    $stmt->close();
    
    $_SESSION['success'] = "Profile updated successfully!";
    header('location: profile.php');
    exit();
}