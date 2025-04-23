<?php
require_once 'config.php';
session_start();

if (isset($_POST['t-act'])) {
    $action = $_POST['t-act'];
    $a_data = $_POST['a-data'];
    $padded_a_data = str_pad($a_data, 4, '0', STR_PAD_LEFT);

    // ARCHIVE
    if ($action === 'archive') {
        $query = "UPDATE theses SET archived = 1 WHERE thesis_id = ?";

        $stmt = $conn->prepare($query);
        $stmt->bind_param('i', $a_data);

        $stmt->execute();
        $stmt->close();

        $_SESSION['success'] = 'Successfully archived thesis with an ID of ' . $padded_a_data . '.';
        logAction($conn, 'archived', 'thesis', $a_data, $_COOKIE['id']);
    }

    // RETRIEVE
    if ($action === 'retrieve') {
        $query = "UPDATE theses SET archived = 0 WHERE thesis_id = ?";

        $stmt = $conn->prepare($query);
        $stmt->bind_param('i', $a_data);

        $stmt->execute();
        $stmt->close();

        $_SESSION['success'] = 'Successfully retrieved thesis with an ID of ' . $padded_a_data . '.';
        logAction($conn, 'retrieved', 'thesis', $a_data, $_COOKIE['id']);
    }

    // DELETE
    if ($action === 'delete') {
        $transferQuery = "INSERT INTO theses_backup SELECT * FROM theses WHERE thesis_id = ?";
        $stmt = $conn->prepare($transferQuery);
        $stmt->bind_param('i', $a_data);
        $stmt->execute();
        $stmt->close();

        $deleteQuery = "DELETE FROM theses WHERE thesis_id = ?";
        $stmt = $conn->prepare($deleteQuery);
        $stmt->bind_param('i', $a_data);
        $stmt->execute();
        $stmt->close();
    
        $result = $conn->query("SELECT IFNULL(MAX(thesis_id), 0) + 1 AS next_id FROM theses");
        $row = $result->fetch_assoc();
        $nextAutoIncrement = $row['next_id'];
        $conn->query("ALTER TABLE theses AUTO_INCREMENT = $nextAutoIncrement");
    
        $_SESSION['success'] = 'Successfully deleted thesis with an ID of ' . $padded_a_data . '.';
        logAction($conn, 'deleted', 'thesis', $a_data, $_COOKIE['id']);
    }

    header("Location: admin.php");
    exit();
}

if (isset($_POST['u-act'])) {
    $action = $_POST['u-act'];
    $a_data = $_POST['ua-data'];
    $padded_a_data = str_pad($a_data, 4, '0', STR_PAD_LEFT);

    // ARCHIVE
    if ($action === 'archive') {
        $query = "UPDATE accounts SET archived = 1 WHERE user_id = ?";

        $stmt = $conn->prepare($query);
        $stmt->bind_param('i', $a_data);

        $stmt->execute();
        $stmt->close();

        $_SESSION['success'] = 'Successfully archived account with an ID of ' . $padded_a_data . '.';
        logAction($conn, 'archived', 'account', $a_data, $_COOKIE['id']);
    }

    // RETRIEVE
    if ($action === 'retrieve') {
        $query = "UPDATE accounts SET archived = 0 WHERE user_id = ?";

        $stmt = $conn->prepare($query);
        $stmt->bind_param('i', $a_data);

        $stmt->execute();
        $stmt->close();

        $_SESSION['success'] = 'Successfully retrieved account with an ID of ' . $padded_a_data . '.';
        logAction($conn, 'retrieved', 'account', $a_data, $_COOKIE['id']);
    }

    // DELETE
    if ($action === 'delete') {
        $transferQuery = "INSERT INTO accounts_backup SELECT * FROM accounts WHERE user_id = ?";
        $stmt = $conn->prepare($transferQuery);
        $stmt->bind_param('i', $a_data);
        $stmt->execute();
        $stmt->close();

        $deleteQuery = "DELETE FROM accounts WHERE user_id = ?";
        $stmt = $conn->prepare($deleteQuery);
        $stmt->bind_param('i', $a_data);
        $stmt->execute();
        $stmt->close();
    
        $result = $conn->query("SELECT IFNULL(MAX(user_id), 0) + 1 AS next_id FROM accounts");
        $row = $result->fetch_assoc();
        $nextAutoIncrement = $row['next_id'];
        $conn->query("ALTER TABLE accounts AUTO_INCREMENT = $nextAutoIncrement");

        $_SESSION['success'] = 'Successfully deleted account with an ID of ' . $padded_a_data . '.';
        logAction($conn, 'deleted', 'account', $a_data, $_COOKIE['id']);
    }

    header("Location: admin.php");
    exit();
}

if (isset($_POST['t-bulk'])) {
    $action = $_POST['t-bulk'];
    $ba_data = explode('-', $_POST['ba-data']);
    $ba_data = array_map('intval', $ba_data);

    // ARCHIVE
    if ($action === 'archive') {
        $placeholders = implode(',', array_fill(0, count($ba_data), '?'));
        $query = "UPDATE theses SET archived = 1 WHERE thesis_id IN ($placeholders)";

        $stmt = $conn->prepare($query);
        $stmt->bind_param(str_repeat('i', count($ba_data)), ...$ba_data);

        $stmt->execute();
        $stmt->close();

        $_SESSION['success'] = 'Successfully archived selected theses.';
        logAction($conn, 'archived', 'thesis', $ba_data, $_COOKIE['id']);
    }

    // RETRIEVE
    if ($action === 'retrieve') {
        $placeholders = implode(',', array_fill(0, count($ba_data), '?'));
        $query = "UPDATE theses SET archived = 0 WHERE thesis_id IN ($placeholders)";

        $stmt = $conn->prepare($query);
        $stmt->bind_param(str_repeat('i', count($ba_data)), ...$ba_data);

        $stmt->execute();
        $stmt->close();

        $_SESSION['success'] = 'Successfully retrieved selected theses.';
        logAction($conn, 'retrieved', 'thesis', $ba_data, $_COOKIE['id']);
    } 

    // DELETE
    if ($action === 'delete') {
        $placeholders = implode(',', array_fill(0, count($ba_data), '?'));

        $columns = "thesis_id, archived, visits, published_date, course, category, title, authors, abstract, keywords";

        $transferQuery = "INSERT INTO theses_backup ($columns) SELECT $columns FROM theses WHERE thesis_id IN ($placeholders)";
        $stmt = $conn->prepare($transferQuery);
        $stmt->bind_param(str_repeat('i', count($ba_data)), ...$ba_data);
        $stmt->execute();
        $stmt->close();

        $deleteQuery = "DELETE FROM theses WHERE thesis_id IN ($placeholders)";
        $stmt = $conn->prepare($deleteQuery);
        $stmt->bind_param(str_repeat('i', count($ba_data)), ...$ba_data);
        $stmt->execute();
        $stmt->close();
    
        $result = $conn->query("SELECT IFNULL(MAX(thesis_id), 0) + 1 AS next_id FROM theses");
        $row = $result->fetch_assoc();
        $nextAutoIncrement = $row['next_id'];
    
        $conn->query("ALTER TABLE theses AUTO_INCREMENT = $nextAutoIncrement");

        $_SESSION['success'] = 'Successfully deleted selected theses.';
        logAction($conn, 'deleted', 'thesis', $ba_data, $_COOKIE['id']);
    }
    
    header("Location: admin.php");
    exit();
}

if (isset($_POST['u-bulk'])) {
    $action = $_POST['u-bulk'];
    $ba_data = explode('-', $_POST['uba-data']);
    $ba_data = array_map('intval', $ba_data);

    // ARCHIVE
    if ($action === 'archive') {
        $placeholders = implode(',', array_fill(0, count($ba_data), '?'));
        $query = "UPDATE accounts SET archived = 1 WHERE user_id IN ($placeholders)";

        $stmt = $conn->prepare($query);
        $stmt->bind_param(str_repeat('i', count($ba_data)), ...$ba_data);

        $stmt->execute();
        $stmt->close();

        $_SESSION['success'] = 'Successfully archived selected accounts.';
        logAction($conn, 'archived', 'account', $ba_data, $_COOKIE['id']);
    }

    // RETRIEVE
    if ($action === 'retrieve') {
        $placeholders = implode(',', array_fill(0, count($ba_data), '?'));
        $query = "UPDATE accounts SET archived = 0 WHERE user_id IN ($placeholders)";

        $stmt = $conn->prepare($query);
        $stmt->bind_param(str_repeat('i', count($ba_data)), ...$ba_data);

        $stmt->execute();
        $stmt->close();

        $_SESSION['success'] = 'Successfully retrieved selected accounts.';
        logAction($conn, 'retrieved', 'account', $ba_data, $_COOKIE['id']);
    } 

    // DELETE
    if ($action === 'delete') {
        $placeholders = implode(',', array_fill(0, count($ba_data), '?'));

        $columns = "user_id, date_joined, archived, role, membership, username, name, email, password, college, yearsection, bio, bookmarks, personalization";

        $transferQuery = "INSERT INTO accounts_backup ($columns) SELECT $columns FROM accounts WHERE user_id IN ($placeholders)";
        $stmt = $conn->prepare($transferQuery);
        $stmt->bind_param(str_repeat('i', count($ba_data)), ...$ba_data);
        $stmt->execute();
        $stmt->close();

        $deleteQuery = "DELETE FROM accounts WHERE user_id IN ($placeholders)";
        $stmt = $conn->prepare($deleteQuery);
        $stmt->bind_param(str_repeat('i', count($ba_data)), ...$ba_data);
        $stmt->execute();
        $stmt->close();
    
        $result = $conn->query("SELECT IFNULL(MAX(user_id), 0) + 1 AS next_id FROM accounts");
        $row = $result->fetch_assoc();
        $nextAutoIncrement = $row['next_id'];
    
        $conn->query("ALTER TABLE theses AUTO_INCREMENT = $nextAutoIncrement");

        $_SESSION['success'] = 'Successfully deleted selected accounts.';
        logAction($conn, 'deleted', 'account', $ba_data, $_COOKIE['id']);
    }
    
    header("Location: admin.php");
    exit();
}

if (isset($_POST['new-thesis'])) {
    $title = addslashes($_POST['title']);
    $authors = [];
    for ($i = 1; $i <= 5; $i++) {
        if (!empty($_POST["author$i"])) {
            $authors[] = trim($_POST["author$i"]);
        }
    }
    $authors = implode('-', $authors);
    $abstract = addslashes($_POST['abstract'] ?? '');
    $keywords = implode(', ', array_map('trim', explode(',', $_POST['keywords'] ?? '')));
    $course = $_POST['course'] ?? '';
    $pdate = $_POST['pdate'];

    $thesis_id = $_POST['thesis-id'];
    $check = $conn->prepare("SELECT thesis_id FROM theses WHERE thesis_id = ?");
    $check->bind_param('i', $thesis_id);
    $check->execute();
    $check->store_result();

    if ($check->num_rows > 0) {
        $query = "UPDATE theses SET published_date = ?, course = ?, title = ?, authors = ?, abstract = ?, keywords = ? WHERE thesis_id = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param('ssssssi', $pdate, $course, $title, $authors, $abstract, $keywords, $thesis_id);
        $_SESSION['success'] = 'Successfully updated thesis with an ID of ' . str_pad($thesis_id, 4, '0', STR_PAD_LEFT) . '.';
        logAction($conn, 'modified', 'thesis', $thesis_id, $_COOKIE['id']);
    } else {
        $query = "INSERT INTO theses (published_date, course, title, authors, abstract, keywords) VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($query);
        $stmt->bind_param('ssssss', $pdate, $course, $title, $authors, $abstract, $keywords);
        $_SESSION['success'] = 'Successfully added new thesis';
        logAction($conn, 'added', 'thesis', $thesis_id, $_COOKIE['id']);
    }
    $check->close();

    $stmt->execute();
    $stmt->close();
   
    header("Location: admin.php");
    exit();
}

if (isset($_POST['new-user'])) {
    $username = $_POST['username'];
    $name = $_POST['name'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $membership = $_POST['membership'];
    $college = $_POST['college'] ?? '';
    $yearsection = $_POST['yearsection'] ?? '';
    $role = $_POST['role'];
    $user_id = $_POST['user-id'];

    $check = $conn->prepare("SELECT user_id FROM accounts WHERE user_id = ?");
    $check->bind_param('i', $user_id);
    $check->execute();
    $check->store_result();

    if ($check->num_rows > 0) {
        $query = "UPDATE accounts SET username = ?, name = ?, email = ?, password = ?, membership = ?, college = ?, yearsection = ?, role = ? WHERE user_id = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param('ssssssssi', $username, $name, $email, $password, $membership, $college, $yearsection, $role, $user_id);
        $_SESSION['success'] = 'Successfully updated user account with and ID of ' . str_pad($user_id, 4, '0', STR_PAD_LEFT) . '.';
        logAction($conn, 'modified', 'account', $user_id, $_COOKIE['id']);
    } else {
        $query = "INSERT INTO accounts (date_joined, username, name, email, password, membership, college, yearsection, role) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($query);
        $stmt->bind_param('sssssssss', date('Y-m-d'), $username, $name, $email, $password, $membership, $college, $yearsection, $role);
        $_SESSION['success'] = 'Successfully added new user account.';
        logAction($conn, 'added', 'account', $user_id, $_COOKIE['id']);
    }
    $check->close();

    $stmt->execute();
    $stmt->close();

    header("Location: admin.php");
    exit();
}

function logAction($conn, $operation, $itemType, $itemIds, $initiatorId) {
    $initiatorEmail = '';
    $lastRefId = '';

    // Get initiator's email
    $stmt = $conn->prepare("SELECT email FROM accounts WHERE user_id = ?");
    $stmt->bind_param('i', $initiatorId);
    $stmt->execute();
    $stmt->bind_result($initiatorEmail);
    $stmt->fetch();
    $stmt->close();

    // Ensure itemIds is an array
    if (!is_array($itemIds)) {
        $itemIds = [$itemIds];
    }

    $prefix = $itemType === 'thesis' ? 'THS#' : 'ACT#';

    // Get last reference ID for prefix
    $stmt = $conn->prepare("SELECT reference_id FROM logs WHERE reference_id LIKE ? ORDER BY CAST(SUBSTRING_INDEX(reference_id, '#', -1) AS UNSIGNED) DESC LIMIT 1");
    $likePattern = $prefix . '%';
    $stmt->bind_param('s', $likePattern);
    $stmt->execute();
    $stmt->bind_result($lastRefId);
    $stmt->fetch();
    $stmt->close();

    if ($lastRefId) {
        $lastNum = (int)substr($lastRefId, 4);
        $newId = $prefix . str_pad($lastNum + 1, 6, '0', STR_PAD_LEFT);
    } else {
        $newId = $prefix . '000001';
    }

    if (count($itemIds) > 1) {
        // Summary log for bulk action
        $details = ucfirst($operation) . " " . count($itemIds) . " " . ucfirst($itemType) . "s.";
    } else {
        $details = ucfirst($operation) . " " . ucfirst($itemType) . " with an ID of [ID#" . str_pad($itemIds[0], 4, '0', STR_PAD_LEFT) . "].";
    }

    // Insert log entry
    $stmt = $conn->prepare("INSERT INTO logs (reference_id, type, operation, date, details, initiator) VALUES (?, ?, ?, NOW(), ?, ?)");
    $stmt->bind_param('sssss', $newId, $itemType, $operation, $details, $initiatorEmail);
    $stmt->execute();
    $stmt->close();
}


