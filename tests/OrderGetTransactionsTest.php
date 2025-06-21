<?php

require_once "api/Order.php";
require_once "api/User.php";
require_once "api/routes.php";
require_once "database/Migration.php";
require_once "api/db.php";

use PHPUnit\Framework\TestCase;

final class OrderGetTransactionsTest extends TestCase {
  protected PDO $db;

  protected function setUp(): void {
    parent::setUp();
    $this->db = DB::connect();
    $this->migrateDatabase();
  }

  protected function migrateDatabase(): void {
    $migration = new DatabaseMigration;
    $this->db->exec(
      $migration->migrate()
    );
  }

  protected function populateProducts() {
    $items = [
      ["name" => "Susu UHT", "price" => 7000, "stock" => 150],
      ["name" => "Mi Instan", "price" => 3000, "stock" => 500],
      ["name" => "Roti Tawar", "price" => 12000, "stock" => 200],
      ["name" => "Air Mineral 600ml", "price" => 3500, "stock" => 400],
      ["name" => "Teh Botol", "price" => 4500, "stock" => 250],
      ["name" => "Sabun Mandi", "price" => 8000, "stock" => 300],
      ["name" => "Shampoo Sachet", "price" => 2000, "stock" => 600],
      ["name" => "Telur Ayam (1 butir)", "price" => 2500, "stock" => 100],
      ["name" => "Minyak Goreng 1L", "price" => 18000, "stock" => 100],
      ["name" => "Tissue Gulung", "price" => 10000, "stock" => 150],
    ];

    foreach ($items as $item) {
      $product = new Product(
        0,
        $item["name"],
        $item["price"],
        $item["stock"]
      );
      Product::addProduct($product);
    }
  }

  protected function getProductAmount() {
    $stmt = $this->db->prepare(
      "SELECT COUNT(*) FROM products"
    );
    $stmt->execute();
    return $stmt->fetchColumn();
  }

  protected function populateOrders($user_id) {
    $amount = $this->getProductAmount();
    $itemsOrder = [];
    for ($i = 0; $i < random_int(4, 7); $i++) {
      $itemsOrder[$i] = [
        "product_id" => random_int(1, $amount - 1),
        "quantity" => random_int(1, 10)
      ];
    }

    Order::addItem($itemsOrder, $user_id);
  }

  public function test_DUPL_13_01_Mengambil_Semua_Data_Transaksi_Dari_User_Kasir_Produk_Jumlah_Harga_Total() {
    $this->populateProducts();

    $user = new User(
      0,
      "kasir2",
      "pass2",
    );
    User::addUser($user);
    $userId = $this->db->lastInsertId();
    $this->populateOrders($userId);

    $orderFetched = count(Order::all($userId));

    $stmt = $this->db->prepare(
      "SELECT 
        COUNT(*)
      FROM orders o
      JOIN order_details od ON o.id = od.order_id
      JOIN products p ON od.product_id = p.id
      JOIN users u ON o.user_id = u.id
      WHERE o.user_id = ?"
    );
    $stmt->execute([$userId]);
    $result = $stmt->fetchColumn();

    $this->assertEquals(
      $orderFetched, 
      $result,
      "fungsi all pada order seharusnya mengambil semua data transaksi oleh kasir tertentu."
    );
  }
}
