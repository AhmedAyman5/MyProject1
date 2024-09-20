<?php

require "core/DBManager.php";

session_start();

//Authentication
require "MyFunctions/functions.php";
guest();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    extract($_POST);

    try {

        $dbManager = new DBManager('localhost', 'company', 'root', '');
        $sql = "SELECT * FROM managers WHERE email=?";
        $stmt = $dbManager->query($sql, $email);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($user) {
            if (password_verify($password, $user['password'])) {
                $_SESSION['user'] = serialize($user);
                $_SESSION['done'] = ['logged in successfully'];
                header("Location: index.php");
                exit;
            } else {
                $_SESSION['errors'] = ['wrong password'];
                header("Location: login.php");
                exit;
            }
        } else {
            $_SESSION['errors'] = ['user not found'];
            header("Location: login.php");
            exit;
        }
    } catch (\PDOException $e) {
        $_SESSION['errors'] = [$e->getMessage()];
        header("Location: login.php");
        die;
    }

}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Employees Management</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css"
        integrity="sha512-Kc323vGBEqzTmouAECnVceyQqyqdsSiqLQISBL29aUW4U/M7pSPA/gEUZQqv1cwx4OnYxTxve5UMg5GT6L4JJg=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />
</head>

<body class="bg-dark h-100">

    <?php include "components/messages/success.php" ?>
    <?php include "components/messages/error.php" ?>

    <section class="h-100 mt-5">
        <div class="card w-100 bg-transparent text-light text-center border border-light">
            <div class="card-title p-3">
                <h1>Login page</h1>
            </div>
            <div class="card-body text-start">
                <form class="px-5" action="login.php" method="POST">
                    <div class="mb-3">
                        <label for="email" class="form-label">Email address</label>
                        <input type="email" class="form-control" id="email" name="email">
                    </div>
                    <div class="mb-3">
                        <label for="password" class="form-label">Password</label>
                        <input type="password" class="form-control" id="password" name="password">
                    </div>
                    <button type="submit" class="btn btn-primary">Login</button>

                    <!-- My own feature -->
                    <div class="form-text">
                        New manager?
                        <a href="register.php" class="btn btn-success ms-2">
                            Register now
                            <i class="fa-solid fa-right-from-bracket"></i>
                        </a>
                    </div>

                </form>
            </div>
        </div>
    </section>


    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM"
        crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/js/all.min.js"
        integrity="sha512-6sSYJqDreZRZGkJ3b+YfdhB3MzmuP9R7X1QZ6g5aIXhRvR1Y/N/P47jmnkENm7YL3oqsmI6AK+V6AD99uWDnIw=="
        crossorigin="anonymous" referrerpolicy="no-referrer"></script>
</body>

</html>