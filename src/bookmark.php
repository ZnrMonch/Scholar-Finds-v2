<?php

require_once 'config.php';
if (isset($_POST['bm'])) {
    $thesisId = $_POST['thesis_id'];
    $userId = $_POST['user_id'];

    $stmt = $conn->prepare('SELECT bookmarks FROM accounts WHERE user_id = ?');
    $stmt->bind_param('s', $userId);
    $stmt->execute();
    $row = $stmt->get_result()->fetch_assoc();

    // Ensure bookmarks are not empty and clean up any extra dashes
    $bookmarks = !empty($row['bookmarks']) ? explode('-', $row['bookmarks']) : [];

    // Check if the thesisId is already in the bookmarks
    if (in_array($thesisId, $bookmarks)) {
        // Remove the thesisId from bookmarks
        $bookmarks = array_diff($bookmarks, [$thesisId]);
    } else {
        // Add the thesisId to bookmarks
        $bookmarks[] = $thesisId;
    }

    // Remove empty values (if any) and re-index the array
    $bookmarks = array_filter($bookmarks);
    $bookmarks = array_values($bookmarks);

    // Convert back to string and ensure no leading or trailing dashes
    $newBookmarks = implode('-', $bookmarks);

    // Update the bookmarks in the database
    $update = $conn->prepare('UPDATE accounts SET bookmarks = ? WHERE user_id = ?');
    $update->bind_param('ss', $newBookmarks, $userId);
    $update->execute();
}

header('location: library.php?view=' . $thesisId);
exit();
