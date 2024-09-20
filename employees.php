<?php

require "core/DBManager.php";
require "models/Employee.php";

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

//Delete employee
if ($_SERVER['REQUEST_METHOD'] == "GET") {
    if (!empty($_GET['action']) && $_GET['action'] == 'delete') {

        $employeeID = $_GET['id'];

        //Delete pic
        // Get picture from My DB
        $sql = "SELECT `picture` FROM `employees` WHERE `id` = ?";
        $stmt = $pdo->query($sql, $employeeID);
        $employee = @$stmt->fetchAll(PDO::FETCH_CLASS, 'Employee')[0];

        //Check if employee exist
        if ($employee) {
            $pictureName = __DIR__ . '/uploads/' . $employee->picture;

            //Check if picture exist
            if (file_exists($pictureName)) {
                // Delete picture from uploads
                unlink($pictureName);
            } else {
                // if not found
                //throw new Exception("Error while deleting file";
            }
        }

        //Delete employee
        $sql = "DELETE FROM `employees` WHERE id=?";
        $stmt = $pdo->query($sql, $employeeID);

        if ($stmt->rowCount() <= 0) {
            $_SESSION['errors'] = ['No employee with provided id!'];
        } else {
            $_SESSION['done'] = ['employee deleted successfully!'];
        }
    }
}

//Show employee that is managed by manager that was login
$man = unserialize($_SESSION['user']);

$sql = "SELECT * FROM `employees` WHERE `manager_id` = ?";
$stmt = $pdo->query($sql, $man['id']);
$employees = @$stmt->fetchAll(PDO::FETCH_CLASS, 'Employee');

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
                        <a class="nav-link active" href="employees.php">Employees</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="managers.php">Managers</a>
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
            <div class="card-title text-start p-3 d-flex justify-content-between" style="align-items: baseline;">
                <h1>Employees</h1>
                <a href="employeesCreate.php" class="btn btn-success">Add Employee <i class="fa fa-plus"></i></a>
            </div>
            <div class="card-body">
                <table class="table table-dark">
                    <thead>
                        <tr class="table-light">
                            <th>ID</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Phone</th>
                            <th>Picture</th>
                            <th>Manager ID</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (count($employees) <= 0): ?>
                            <tr>
                                <td colspan="7">No data added yet!</td>
                            </tr>
                        <?php endif; ?>
                        <?php foreach ($employees as $employee): ?>
                            <tr>
                                <td><?= $employee->id ?></td>
                                <td><?= $employee->name ?></td>
                                <td><a href="mailto:<?= $employee->email ?>"><?= $employee->email ?></a></td>
                                <td><a href="tel:<?= $employee->phone ?>"><?= $employee->phone ?></a></td>
                                <td><img src="<?= 'uploads/' . $employee->picture ?>" width="50" height="50"
                                        alt="profile image"></td>
                                <td><?= $employee->manager_id ?></td>
                                <td>
                                    <a href="employeesEdit.php?id=<?= $employee->id ?>" class="btn btn-primary"><i
                                            class="fa fa-edit"></i></a>
                                    <a href="employees.php?action=delete&id=<?= $employee->id ?>" class="btn btn-danger"><i
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