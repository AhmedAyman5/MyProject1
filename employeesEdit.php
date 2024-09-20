<?php
session_start();

require "core/DBManager.php";
require "models/Employee.php";
require "Myfunctions/functions.php";
require "models/Manager.php";

//Authentication
$is_authenticated = !empty($_SESSION['user']);

if (!$is_authenticated) {
    $_SESSION['errors'] = [
        'Unauthenticated!'
    ];
    header(header: 'Location: login.php');
    exit;
}

//Conection
$pdo = new DBManager('localhost', 'company', 'root', '');

//GET
if (!$_SERVER['REQUEST_METHOD'] == "GET") {
    header(header: "Location: employees.php", response_code: 404);
    exit;
}

extract($_GET);
$employeeID = $_GET['id'];

if (!$employeeID) {
    header(header: "Location: employees.php", response_code: 404);
    exit;
}

$sql = "SELECT * FROM `employees` WHERE `id` = ?";
$stmt = $pdo->query($sql, $employeeID);
$emp = $stmt->fetch(PDO::FETCH_ASSOC);

// get managers from My DB
$stmt = $pdo->query("SELECT * FROM `managers`");
$managers = $stmt->fetchAll(PDO::FETCH_CLASS, 'Manager');

//ID not exist
if (!$emp) {
    header(header: "Location: employees.php", response_code: 404);
    exit;
}

//POST
if ($_SERVER['REQUEST_METHOD'] == "POST") {

    extract($_POST);

    $sql = "UPDATE `employees` SET name=?, email=?, phone=?, manager_id=?";

    //picture exist
    if ($_FILES['picture']['size'] > 0) {
        //Delete old
        $pictureName = __DIR__ . '/uploads/' . $emp['picture'];
        if (file_exists($pictureName)) {
            unlink($pictureName);
        } else {
            throw new Exception("Error while deleting file");
        }
        //store new
        $new_file_name = time() . '.' . explode('.', $_FILES['picture']['name'])[1];
        $from = $_FILES['picture']['tmp_name'];
        $target = __DIR__ . '/uploads/' . $new_file_name;
        if (move_uploaded_file($from, $target)) {
            $picture = $new_file_name;
        } else {
            throw new Exception("Error in file paths while uploads");
        }

        $sql = $sql . ', picture=? WHERE id=' . $employeeID;
        $pdo->query($sql, $name, $email, $phone, $manager_id, $picture);

    } else {
        $sql = $sql . ' WHERE id=' . $employeeID;
        $pdo->query($sql, $name, $email, $phone, $manager_id);
    }

    $_SESSION['done'] = ['Employee updated successfully!'];
    header('Location: employees.php');
    exit;
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
                <h1>Add New Employee</h1>
            </div>
            <div class="card-body text-start">
                <form action="employeesEdit.php?id=<?= $employeeID ?>" method="POST" enctype="multipart/form-data">
                    <div class="mb-3">
                        <label for="name" class="form-label">Name</label>
                        <input type="text" class="form-control" id="name" name="name" value="<?= $emp['name'] ?>">
                    </div>
                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" class="form-control" id="email" name="email" value="<?= $emp['email'] ?>">
                    </div>
                    <div class="mb-3">
                        <label for="phone" class="form-label">Phone</label>
                        <input type="text" class="form-control" id="phone" name="phone" value="<?= $emp['phone'] ?>">
                    </div>
                    <div class="mb-3">
                        <label for="picture" class="form-label">Picture</label>
                        <input type="file" class="form-control" id="picture" name="picture">
                        <img src="uploads/<?= $emp['picture'] ?>" alt="" width="50" height="50">
                    </div>
                    <div class="mb-3">
                        <label for="manager_id" class="form-label">Manager</label>
                        <select class="form-control" id="manager_id" name="manager_id">
                            <option value="">Select a Manager</option>
                            <?php foreach ($managers as $manager): ?>
                                <option value="<?= $manager->id ?>" <?= $emp['manager_id'] == $manager->id ? 'selected' : '' ?>><?= $manager->name ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-primary">Update <i class="fa fa-paper-plane"></i></button>
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