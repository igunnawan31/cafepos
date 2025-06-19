<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");
require_once 'Product.php';
require_once 'Order.php';
require_once 'User.php';
require_once 'Auth.php';
$action = $_GET['action'] ?? '';
switch ($action) {
    case 'login':
        $d = json_decode(file_get_contents('php://input'), true);
        echo json_encode(Auth::login($d['username'], $d['password']));
        break;
    case 'getProducts': echo json_encode(Product::all()); break;
    case 'addProduct':
        $d = json_decode(file_get_contents('php://input'), true);
        $product = new Product(0, $d['name'], $d['price'], $d['stock']);
        Product::addProduct($product);
        echo "Produk berhasil ditambahkan";
        break;
    case 'editProduct':
        $d = json_decode(file_get_contents('php://input'), true);
        $product = new Product($d['id'], $d['name'], $d['price'], $d['stock']);
        Product::updateProduct($product);
        echo "Produk berhasil diperbarui.";
        break;
    case 'deleteProduct':
        $d = json_decode(file_get_contents('php://input'), true);
        Product::deleteProduct($d['id']);
        echo "Produk berhasil dihapus";
        break;
    case 'getUsers': echo json_encode(User::all()); break;
    case 'addUser':
        $d = json_decode(file_get_contents('php://input'), true);
        $user = new User(0, $d['username'], $d['password']);
        User::addUser($user);
        echo "Akun berhasil ditambahkan";
        break;
    case 'editUser':
        $d = json_decode(file_get_contents('php://input'), true);
        $user = new User($d['id'], $d['username'], $d['password']);
        User::updateUser($user);
        echo "Akun berhasil diperbarui.";
        break;
    case 'deleteUser':
        $d = json_decode(file_get_contents('php://input'), true);
        User::deleteUser($d['id']);
        echo "Akun berhasil dihapus";
        break;
    case 'getOrders': 
        $d = json_decode(file_get_contents('php://input'), true);
        echo json_encode(Order::all($d['user_id']));
        break;
    case 'addToOrder':
        $d = json_decode(file_get_contents('php://input'), true);
        Order::addItem($d['items'], $d['user_id']);
        break;
    case 'getAllOrders': 
        $d = json_decode(file_get_contents('php://input'), true);
        echo json_encode(Order::getAll());
        break;
    case 'deleteOrder':
        $d = json_decode(file_get_contents('php://input'), true);
        Order::deleteOrder($d['id']);
        echo "Transaksi berhasil dihapus";
        break;
}
?>