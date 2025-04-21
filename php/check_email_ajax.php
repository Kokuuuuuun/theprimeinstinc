<?php
require_once 'conexion.php';
require_once 'check_email.php';

$email = $_GET['email'] ?? '';
$exclude_id = $_GET['exclude_id'] ?? null;

$exists = checkDuplicateEmail($conexion, $email, $exclude_id);

header('Content-Type: application/json');
echo json_encode(['exists' => $exists]);
?>