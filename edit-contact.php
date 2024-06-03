<?php
require 'config.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$id = $_GET['id'];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name'];
    $phone = $_POST['phone'];
    $email = $_POST['email'];
    $address = $_POST['address'];

    $stmt = $conn->prepare("UPDATE contacts SET name = ?, phone = ?, email = ?, address = ? WHERE id = ?");
    $stmt->bind_param("ssssi", $name, $phone, $email, $address, $id);

    if ($stmt->execute()) {
        header("Location: contacts.php");
    } else {
        echo "Error: " . $stmt->error;
    }
    $stmt->close();
} else {
    $stmt = $conn->prepare("SELECT * FROM contacts WHERE id = ? AND user_id = ?");
    $stmt->bind_param("ii", $id, $_SESSION['user_id']);
    $stmt->execute();
    $result = $stmt->get_result();
    $contact = $result->fetch_assoc();
    $stmt->close();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit Contact</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <a href="logout.php" class="btn btn-danger float-right">Logout</a>
                <h1 class="text-center">Edit Contact</h1>
                <form method="POST">
                    <div class="form-group">
                        <label>Name</label>
                        <input type="text" name="name" class="form-control" placeholder="Name" value="<?= htmlspecialchars($contact['name']) ?>" required>
                    </div>
                    <div class="form-group">
                        <label>Phone</label>
                        <input type="text" name="phone" class="form-control" placeholder="Phone" value="<?= htmlspecialchars($contact['phone']) ?>">
                    </div>
                    <div class="form-group">
                        <label>Email</label>
                        <input type="email" name="email" class="form-control" placeholder="Email" value="<?= htmlspecialchars($contact['email']) ?>">
                    </div>
                    <div class="form-group">
                        <label>Address</label>
                        <textarea name="address" class="form-control" placeholder="Address"><?= htmlspecialchars($contact['address']) ?></textarea>
                    </div>
                    <button type="submit" class="btn btn-primary">Update Contact</button>
                </form>
            </div>
        </div>
    </div>
</body>
</html>
