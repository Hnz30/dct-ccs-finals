<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <title>Login</title>
</head>

<?php 
session_start();
include 'functions.php';

$errors = []; // Array to collect error messages

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    // Validate input fields
    if (empty($email)) {
        $errors[] = "Email is required.";
    }

    if (empty($password)) {
        $errors[] = "Password is required.";
    }

    // Proceed with authentication if no validation errors
    if (empty($errors)) {
        $user = authenticateUser($email, $password); // Use a function from functions.php to validate user

        if ($user) {
            $_SESSION['user'] = $user['email']; // Store user email in session
            header("Location: admin/dashboard.php");
            exit();
        } else {
            $errors[] = "Invalid email or password!";
        }
    }
}
?>

<body class="bg-secondary-subtle">
    <div class="d-flex align-items-center justify-content-center vh-100">
        <div class="col-3">
            <div class="card">
                <div class="card-body">
                    <h1 class="h3 mb-4 fw-normal">Login</h1>

                    <!-- Display error messages -->
                    <?php if (!empty($errors)) echo displayErrors($errors); ?>

                    <!-- Login Form -->
                    <form method="post" action="">
                        <div class="form-floating mb-3">
                            <input type="text" class="form-control" id="email" name="email" placeholder="user1@example.com">
                            <label for="email">Email address</label>
                        </div>
                        <div class="form-floating mb-3">
                            <input type="password" class="form-control" id="password" name="password" placeholder="Password">
                            <label for="password">Password</label>
                        </div>
                        <div class="form-floating mb-3">
                            <button type="submit" name="login" class="btn btn-primary w-100">Login</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</body>

</html>
