<!DOCTYPE html>
<html lang="id">

<head>
  <meta charset="UTF-8">
  <title>Login - CafePOS</title>
  <link rel="stylesheet" href="css/login.css" />
</head>

<body onload="auth_check()">
  <<?php
    require_once "../api/Product.php";
    require_once "../api/db.php";

    $d = [
      "name" => null,
      "price" => "10000",
      "stock" => "15"
    ];

    $product = new Product(
      0,
      $d["name"],
      $d["price"],
      $d["stock"]
    );
    $response = Product::addProduct($product);

    echo("HELLO");
    echo($response);
    ?>
    </body>

</html>