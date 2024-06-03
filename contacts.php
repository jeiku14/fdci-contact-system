<?php
require 'config.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
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

$total_query = "SELECT COUNT(*) FROM contacts WHERE user_id = ? AND (name LIKE ? OR email LIKE ? OR phone LIKE ? OR address LIKE ?)";
$total_stmt = $conn->prepare($total_query);
$total_stmt->bind_param("issss", $user_id, $search_param, $search_param, $search_param, $search_param);
$total_stmt->execute();
$total_result = $total_stmt->get_result();
$total_contacts = $total_result->fetch_row()[0];
$total_pages = ceil($total_contacts / $limit);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Contacts</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
    $(document).ready(function() {
        $('input[name="search"]').on('input', function() {
            var search = $(this).val();
            $.ajax({
                url: 'search-contact.php',
                method: 'GET',
                data: { search: search },
                success: function(response) {
                    $('table tbody').html(response);
                }
            });
        });

        $(document).on('click', '.delete-contact', function(e) {
            e.preventDefault();
            var link = $(this).attr('href');
            if (confirm('Are you sure you want to delete this contact?')) {
                window.location.href = link;
            }
        });
    });
    </script>
</head>
<body>
    <div class="container mt-5">
        <div class="row">
            <div class="col-md-12 d-flex justify-content-between align-items-center">
                <form method="GET" action="contacts.php" class="form-inline my-2 my-lg-0">
                    <input class="form-control mr-sm-2" type="text" name="search" placeholder="Search contacts" value="<?= htmlspecialchars($search) ?>">
                    <button class="btn btn-outline-success my-2 my-sm-0" type="submit">Search</button>
                </form>
                <div>
                    <a href="add-contact.php" class="btn btn-primary mr-2">Add Contact</a>
                    <a href="logout.php" class="btn btn-danger">Logout</a>
                </div>
            </div>
        </div>
        <div class="row mt-3">
            <div class="col-md-12">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Phone</th>
                            <th>Email</th>
                            <th>Address</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($row = $result->fetch_assoc()): ?>
                            <tr>
                                <td><?= htmlspecialchars($row['name']) ?></td>
                                <td><?= htmlspecialchars($row['phone']) ?></td>
                                <td><?= htmlspecialchars($row['email']) ?></td>
                                <td><?= htmlspecialchars($row['address']) ?></td>
                                <td>
                                    <a href="edit-contact.php?id=<?= $row['id'] ?>" class="btn btn-info">Edit</a>
                                    <a href="delete-contact.php?id=<?= $row['id'] ?>" class="btn btn-danger delete-contact">Delete</a>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
                <div>
                    <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                        <a href="contacts.php?page=<?= $i ?>&search=<?= htmlspecialchars($search) ?>" class="btn btn-secondary"><?= $i ?></a>
                    <?php endfor; ?>
                </div>
            </div>
        </div>
    </div>
</body>
</html>