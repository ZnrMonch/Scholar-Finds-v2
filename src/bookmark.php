<?php

require_once 'config.php';
if (isset($_POST['bm'])) {
    $thesisId = $_POST['thesis_id'];
    $userId = $_POST['user_id'];

    $stmt = $conn->prepare('SELECT bookmarks FROM accounts WHERE user_id = ?');
    $stmt->bind_param('s', $userId);
    $stmt->execute();
    $row = $stmt->get_result()->fetch_assoc();

    $bookmarks = !empty($row['bookmarks']) ? explode('-', $row['bookmarks']) : [];

    if (in_array($thesisId, $bookmarks)) {
        $bookmarks = array_diff($bookmarks, [$thesisId]);
    } else {
        $bookmarks[] = $thesisId;
    }

    $bookmarks = array_filter($bookmarks);
    $bookmarks = array_values($bookmarks);

    $newBookmarks = implode('-', $bookmarks);

    $update = $conn->prepare('UPDATE accounts SET bookmarks = ? WHERE user_id = ?');
    $update->bind_param('ss', $newBookmarks, $userId);
    $update->execute();
}

header('location: library.php?view=' . $thesisId);
exit();
