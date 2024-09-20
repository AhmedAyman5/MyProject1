<?php

require "core/DBManager.php";
require "models/Manager.php";

session_start();

//Authentication
$is_authenticated = !empty($_SESSION['user']);

if (!$is_authenticated) {
    $_SESSION['errors'] = [
        'Unauthenticated!'
    ];
    header(header: 'Location: login.php');
    exit;
}

$pdo = new DBManager('localhost', 'company', 'root', '');

//Delete manager
if ($_SERVER['REQUEST_METHOD'] == "GET") {
    if (!empty($_GET['action']) && $_GET['action'] == 'delete') {
        $managerID = $_GET['id'];
        $sql = "DELETE FROM `managers` WHERE id=?";
        $stmt = $pdo->query($sql, $managerID);
        if ($stmt->rowCount() <= 0) {
            $_SESSION['errors'] = ['No manager with provided id!'];
        } else {
            $_SESSION['done'] = ['manager deleted successfully!'];
        }
    }
}

$stmt = $pdo->query("SELECT * FROM `managers`");
$managers = @$stmt->fetchAll(PDO::FETCH_CLASS, 'Manager');


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

    <nav class="navbar navbar-expand-lg navbar-light bg-light">
        <div class="container-fluid">
            <a class="navbar-brand" href="#">Employees Management</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse"
                data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false"
                aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarSupportedContent">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                    <li class="nav-item">
                        <a class="nav-link" aria-current="page" href="index.php">Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="employees.php">Employees</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="managers.php">Managers</a>
                    </li>
                </ul>
                <a href="logout.php" class="btn btn-danger ms-2">
                    Logout
                    <i class="fa-solid fa-right-from-bracket"></i>
                </a>
            </div>
        </div>
    </nav>

    <section class="h-100 mt-5">
        <div class="card w-100 bg-transparent text-light text-center border border-light">
            <div class="card-body">
                <table class="table table-dark">
                    <thead>
                        <tr class="table-light">
                            <th>ID</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (count($managers) <= 0): ?>
                            <tr>
                                <td colspan="7">No data added yet!</td>
                            </tr>
                        <?php endif; ?>
                        <?php foreach ($managers as $manager): ?>
                            <tr>
                                <td><?= $manager->id ?></td>
                                <td><?= $manager->name ?></td>
                                <td><a href="mailto:<?= $manager->email ?>"><?= $manager->email ?></a></td>
                                <td>
                                    <a href="managers.php?action=delete&id=<?= $manager->id ?>" class="btn btn-danger"><i
                                            class="fa fa-trash"></i></a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
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