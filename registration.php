<?php
    require_once 'config.php';

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $name = $_POST['name'];
        $email = $_POST['email'];
        // Hash the password before saving in the database
        $password = password_hash($_POST['password'], PASSWORD_BCRYPT);

        // pre validation to check if the password and confirm password match
        if ($_POST['password'] != $_POST['confirm_password']) {
            echo '<script>alert("Passwords do not match")</script>';
        }

        // Check if the email already exists
        $SQL = $conn->prepare('SELECT * FROM users WHERE email = ?');
        $SQL->bind_param('s', $email);
        $SQL->execute();
        $result = $SQL->get_result();

        if ($result->num_rows > 0) {
            echo '<script>alert("Email already exists")</script>';
        }
        // insert the record
        else {
            $SQL = $conn->prepare('INSERT INTO users (name, email, password) VALUES (?, ?, ?)');
            $SQL->bind_param('sss', $name, $email, $password);

            if ($SQL->execute()) {
                header('Location: thank-you.php');
            } else {
                echo 'Error: '.$SQL->error;
            }
        }
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registration</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card mt-5">
                    <div class="card-header">Registration</div>
                    <div class="card-body">
                        <form method="post">
                            <div class="form-group">
                                <label for="name">Name</label>
                                <input type="text" name="name" id="name" class="form-control" required>
                            </div>

                            <div class="form-group">
                                <label for="email">Email</label>
                                <input type="email" name="email" id="email" class="form-control" required>
                            </div>

                            <div class="form-group">
                                <label for="password">Password</label>
                                <input type="password" name="password" id="password" class="form-control" required>
                            </div>

                            <div class="form-group">
                                <label for="confirm_password">Confirm Password</label>
                                <input type="password" name="confirm_password" id="confirm_password" class="form-control" required>
                            </div>

                            <button type="submit" class="btn btn-primary">Register</button>
                        </form>
                    </div>
                </div>
                <div class="text-center mt-3">
                    <a href="login.php">Login</a>
                </div>
            </div>
        </div>
    </div>
</body>
</html>