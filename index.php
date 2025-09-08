<?php

require "connection.php";

if(isset($_GET['delete'])){
    $stmt = $pdo->prepare("DELETE FROM users WHERE id = :id");
    $stmt->execute(['id' => $_GET['delete']]);
    header("Location: " . $_SERVER["PHP_SELF"]);
    exit;
};



$editUser = null;

if (isset($_GET["edit"])){
    $id = (int)$_GET["edit"];
    $stmt = $pdo->prepare("SELECT * FROM users where id = :id");
    $stmt->execute(["id" => $id]);
    $editUser = $stmt->fetch(PDO::FETCH_ASSOC);
}



if ($_SERVER["REQUEST_METHOD"] == "POST"){

    $name = trim($_POST["name"]);
    $email = trim($_POST["email"]);
    $id = isset($_POST["id"]) ? (int)$_POST["id"] : null;

    if ($name && $email){
        if ($id){
            $stmt = $pdo->prepare("UPDATE users SET name = :name, email = :email WHERE id = :id");
            $stmt->execute(["name" => $name, "email" => $email, "id" => $id]);
        }else{
            $stmt = $pdo->prepare("INSERT INTO users (name, email) VALUES (:name, :email)");
            $stmt->execute(["name" => $name, "email" => $email]);
        }
        header("Location: " . $_SERVER["PHP_SELF"]);
        exit;
    } else{
        echo "<p style='color:red;text-align:center'>Ism va emailni to'ldiring!</p>";
    }
}

$stmt = $pdo->query("SELECT * FROM users");
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>



<!doctype html>
<html lang="en">
<head>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Post to phpMyadmin</title>
</head>
<body>

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-lg-8 col-md-10 col-sm-11">

            <h3 class="text-center">
                <?= $editUser ? "Foydalanuvchini tahrirlash" : "Yangi Foydalanuvchi qo'shish" ?>
            </h3>
            <form action="" method="post" class="mb-5">
                <?php if ($editUser): ?>
                    <input type="hidden" name="id" value="<?= $editUser["id"] ?>">
                <?php endif; ?>
                <div class="mb-3">
                    <input type="text" name="name" class="form-control" placeholder="Ism"
                           value="<?= htmlspecialchars($editUser['name'] ?? '') ?>">
                </div>
                <div class="mb-3">
                    <input type="email" name="email" class="form-control"
                           placeholder="Email" value="<?= htmlspecialchars($editUser['email'] ?? '') ?>">
                </div>
                <button class="btn btn-primary" type="submit">
                    <?= $editUser ? "yangilash" : "Yuborish"?>
                </button>
                <?php if ($editUser): ?>
                    <a href="<?= $_SERVER["PHP_SELF"] ?>" class="btn btn-secondary">
                        &times; Bekor qilish
                    </a>
                <?php endif;?>
            </form>

            <table class="table table-striped">
                <thead>
                <tr>
                    <th>#</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Controls</th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($users as $index => $user): ?>
                    <tr>
                        <td><?=$index + 1 ?></td>
                        <td><?= $user["name"] ?></td>
                        <td><?= $user["email"] ?></td>
                        <td>
                            <a href="?edit=<?= $user["id"] ?>" class="btn btn-success btn-sm">Edit</a>
                            <a href="?delete=<?=$user['id'] ?>"
                               class="btn btn-danger btn-sm mx-1"
                               onclick="return confirm('Haqiqatdanham o‘chirmoqchimisiz?')"
                            >
                                &times;
                            </a>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

</body>
</html>