<?php
require 'config.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$id = $_GET['id'];

$stmt = $conn->prepare("DELETE FROM contacts WHERE id = ? AND user_id = ?");
$stmt->bind_param("ii", $id, $_SESSION['user_id']);

if ($stmt->execute()) {
    header("Location: contacts.php");
} else {
    echo "Error: " . $stmt->error;
}
$stmt->close();
?>
