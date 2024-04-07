<?php

require '../database.php';
$db = new Database();

$result = $db->table('user_predicition')->get();

header('Content-Type: application/json');
echo json_encode($result);
exit;
