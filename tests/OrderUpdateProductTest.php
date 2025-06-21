<?php

require_once "api/Product.php";
require_once "api/routes.php";
require_once "database/Migration.php";
require_once "api/db.php";

use PHPUnit\Framework\TestCase;

final class OrderUpdateProductTest extends TestCase {
  protected PDO $db;

  protected function setUp(): void {
    parent::setUp();
    $this->db = DB::connect();
    $this->migrateDatabase();
    $this->addTestProduct();
  }

  protected function migrateDatabase(): void {
    $migration = new DatabaseMigration;
    $this->db->exec(
      $migration->migrate()
    );
  }

  protected function addTestProduct(): void {
    $d = [
      "name" => "WOW Onigiri",
      "price" => "10000",
      "stock" => "15"
    ];

    $product = new Product(
      0,
      $d["name"],
      $d["price"],
      $d["stock"]
    );
    Product::addProduct($product);
  }

  protected function getTestProduct(): array {
    $products = Product::all();
    $testProduct = $products[count($products) - 1];
    return $testProduct;
  }

  public function test_DUPL_07_01_Dapat_Mengubah_Data_Produk_Dengan_Data_Lengkap(): void {
    $testProduct = $this->getTestProduct();
    $d = [
      "name" => "Susu UHT",
      "price" => 7000,
      "stock" => 10
    ];

    $updatedProduct = new Product(
      $testProduct["id"],
      $d["name"],
      $d["price"],
      $d["stock"]
    );
    $response = Product::updateProduct($updatedProduct);

    $stmt = $this->db->prepare("SELECT * FROM products WHERE name = :name AND price = :price AND stock = :stock");
    $stmt->execute([
      ':name' => $d['name'],
      ':price' => $d['price'],
      ':stock' => $d['stock']
    ]);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    $this->assertEquals($d['name'], $result['name']);
    $this->assertEquals($d['price'], $result['price']);
    $this->assertEquals($d['stock'], $result['stock']);
  }

  public function test_DUPL_07_02_Tidak_Dapat_Mengubah_Data_Nama_Produk_Dengan_Isian_Kosong(): void {
    $testProduct = $this->getTestProduct();
    $d = [
      "name" => "",
      "price" => 7000,
      "stock" => 10
    ];

    $updatedProduct = new Product(
      $testProduct["id"],
      $d["name"],
      $d["price"],
      $d["stock"]
    );
    $response = Product::updateProduct($updatedProduct);

    $stmt = $this->db->prepare(
      "SELECT * FROM products WHERE id = :id"
    );
    $stmt->execute([
      ':id' => $testProduct['id'],
    ]);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    $this->assertEquals(
      $testProduct['name'], 
      $result['name'], 
      "updateProduct seharusnya tidak dapat mengubah data produk jika isian nama kosong"
    );
  }

  public function test_DUPL_07_03_Tidak_Dapat_Mengubah_Data_Harga_Produk_Dengan_Isian_Kosong(): void {
    $testProduct = $this->getTestProduct();
    $d = [
      "name" => "Susu UHT",
      "price" => "",
      "stock" => 10
    ];

    $updatedProduct = new Product(
      $testProduct["id"],
      $d["name"],
      $d["price"],
      $d["stock"]
    );
    $response = Product::updateProduct($updatedProduct);

    $stmt = $this->db->prepare(
      "SELECT * FROM products WHERE id = :id"
    );
    $stmt->execute([
      ':id' => $testProduct['id'],
    ]);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    $this->assertEquals(
      $testProduct['price'], 
      $result['price'], 
      "updateProduct seharusnya tidak dapat mengubah data produk jika isian harga kosong"
    );
  }

  public function test_DUPL_07_04_Tidak_Dapat_Mengubah_Data_Stok_Produk_Dengan_Isian_Kosong(): void {
    $testProduct = $this->getTestProduct();
    $d = [
      "name" => "Susu UHT",
      "price" => 7000,
      "stock" => ""
    ];

    $updatedProduct = new Product(
      $testProduct["id"],
      $d["name"],
      $d["price"],
      $d["stock"]
    );
    $response = Product::updateProduct($updatedProduct);

    $stmt = $this->db->prepare(
      "SELECT * FROM products WHERE id = :id"
    );
    $stmt->execute([
      ':id' => $testProduct['id'],
    ]);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    $this->assertEquals(
      $testProduct['stock'], 
      $result['stock'], 
      "updateProduct seharusnya tidak dapat mengubah data produk jika isian harga kosong"
    );
  }

  public function test_DUPL_07_05_Dapat_Mengubah_Data_Harga_Produk_Dengan_Nilai_Nol(): void {
    $testProduct = $this->getTestProduct();
    $d = [
      "name" => "Susu UHT",
      "price" => 0,
      "stock" => 10
    ];

    $updatedProduct = new Product(
      $testProduct["id"],
      $d["name"],
      $d["price"],
      $d["stock"]
    );
    $response = Product::updateProduct($updatedProduct);

    $stmt = $this->db->prepare(
      "SELECT * FROM products WHERE id = :id"
    );
    $stmt->execute([
      ':id' => $testProduct['id'],
    ]);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    $this->assertEquals(
      $d['price'], 
      $result['price'], 
      "updateProduct seharusnya dapat mengubah data produk jika isian harga bernilai nol"
    );
  }

  public function test_DUPL_07_06_Dapat_Mengubah_Data_Stok_Produk_Dengan_Nilai_Nol(): void {
    $testProduct = $this->getTestProduct();
    $d = [
      "name" => "Susu UHT",
      "price" => "7000",
      "stock" => 0
    ];

    $updatedProduct = new Product(
      $testProduct["id"],
      $d["name"],
      $d["price"],
      $d["stock"]
    );
    $response = Product::updateProduct($updatedProduct);

    $stmt = $this->db->prepare(
      "SELECT * FROM products WHERE id = :id"
    );
    $stmt->execute([
      ':id' => $testProduct['id'],
    ]);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    $this->assertEquals(
      $d['stock'], 
      $result['stock'], 
      "updateProduct seharusnya dapat mengubah data produk jika isian stok bernilai nol"
    );
  }

  public function test_DUPL_07_07_Tidak_Dapat_Mengubah_Data_Harga_Produk_Dengan_Nilai_Negatif(): void {
    $testProduct = $this->getTestProduct();
    $d = [
      "name" => "Susu UHT",
      "price" => -7000,
      "stock" => 10
    ];

    $updatedProduct = new Product(
      $testProduct["id"],
      $d["name"],
      $d["price"],
      $d["stock"]
    );
    $response = Product::updateProduct($updatedProduct);

    $stmt = $this->db->prepare(
      "SELECT * FROM products WHERE id = :id"
    );
    $stmt->execute([
      ':id' => $testProduct['id'],
    ]);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    $this->assertEquals(
      $testProduct['price'], 
      $result['price'], 
      "updateProduct seharusnya tidak dapat mengubah data produk jika isian harga bernilai negatif"
    );
  }

  public function test_DUPL_07_08_Tidak_Dapat_Mengubah_Data_Stok_Produk_Dengan_Nilai_Negatif(): void {
    $testProduct = $this->getTestProduct();
    $d = [
      "name" => "Susu UHT",
      "price" => 7000,
      "stock" => -10
    ];

    $updatedProduct = new Product(
      $testProduct["id"],
      $d["name"],
      $d["price"],
      $d["stock"]
    );
    $response = Product::updateProduct($updatedProduct);

    $stmt = $this->db->prepare(
      "SELECT * FROM products WHERE id = :id"
    );
    $stmt->execute([
      ':id' => $testProduct['id'],
    ]);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    $this->assertEquals(
      $testProduct['stock'], 
      $result['stock'], 
      "updateProduct seharusnya tidak dapat mengubah data produk jika isian stok bernilai negatif"
    );
  }
}
