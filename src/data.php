<?php

$theses_sql = "SELECT thesis_id, archived, visits, published_date, course, category, title, authors, abstract, keywords FROM theses";
$theses_result = $conn->query($theses_sql);
$theses = [];
$accounts_sql = "SELECT user_id, date_joined, archived, role, membership, username, name, email, password, college, yearsection, bio, bookmarks, personalization FROM accounts";
$accounts_result = $conn->query($accounts_sql);
$accounts = [];
$logs_sql = "SELECT reference_id, type, operation, date, details, initiator FROM logs";
$logs_result = $conn->query($logs_sql);
$logs = [];

while ($row = $theses_result->fetch_assoc()) {
    $theses[] = $row;
}

while ($row = $accounts_result->fetch_assoc()) {
    $accounts[] = $row;
}

while ($row = $logs_result->fetch_assoc()) {
    $row['date'] = date("F j, Y", strtotime($row['date']));
    $logs[] = $row;
}

$data = [
    "theses" => $theses,
    "accounts" => $accounts
];

$logs = [
    "logs" => $logs
];

$json_data = json_encode($data, JSON_PRETTY_PRINT);
file_put_contents("data.json", $json_data);

$json_logs = json_encode($logs, JSON_PRETTY_PRINT);
file_put_contents("logs.json", $json_logs);
