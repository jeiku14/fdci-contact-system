<?php
require 'config.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    exit();
}

$user_id = $_SESSION['user_id'];
$search = isset($_GET['search']) ? $_GET['search'] : '';
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$limit = 7;
$offset = ($page - 1) * $limit;

$query = "SELECT * FROM contacts WHERE user_id = ? AND (name LIKE ? OR email LIKE ? OR phone LIKE ? OR address LIKE ?) LIMIT ? OFFSET ?";
$search_param = '%' . $search . '%';

$stmt = $conn->prepare($query);
$stmt->bind_param("issssii", $user_id, $search_param, $search_param, $search_param, $search_param, $limit, $offset);
$stmt->execute();
$result = $stmt->get_result();

while ($row = $result->fetch_assoc()): ?>
    <tr>
        <td><?= htmlspecialchars($row['name']) ?></td>
        <td><?= htmlspecialchars($row['phone']) ?></td>
        <td><?= htmlspecialchars($row['email']) ?></td>
        <td><?= htmlspecialchars($row['address']) ?></td>
        <td>
            <a href="edit-contact.php?id=<?= $row['id'] ?>">Edit</a>
            <a href="delete-contact.php?id=<?= $row['id'] ?>" class="delete-contact">Delete</a>
        </td>
    </tr>
<?php endwhile; ?>
