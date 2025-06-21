<?php

require_once "api/Product.php";
require_once "api/routes.php";
require_once "database/Migration.php";
require_once "api/db.php";

use PHPUnit\Framework\TestCase;

final class OrderDeleteProductTest extends TestCase {
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

  public function test_DUPL_08_01_Dapat_Menghapus_Data_Produk_Dengan_Id_Valid(): void {
    $testProduct = $this->getTestProduct();
    $response = Product::deleteProduct($testProduct["id"]);

    $result = Product::getProduct($testProduct["id"]);
    $this->assertEquals(
      null, 
      $result, 
      "deleteProduct seharusnya dapat menghapus produk jika id valid dan ada di dalam database"
    );
  }
  
  public function test_DUPL_08_02_Tidak_Dapat_Menghapus_Data_Produk_Jika_Isian_Id_Kosong(): void {
    $testProduct = $this->getTestProduct();
    
    $stmt = $this->db->prepare(
      "SELECT COUNT(*) FROM products"
    );
    $stmt->execute();
    $expected = $stmt->fetchColumn();
    
    $response = Product::deleteProduct("");

    $stmt->execute();
    $result = $stmt->fetchColumn();

    $this->assertEquals(
      $expected, 
      $result, 
      "deleteProduct seharusnya tidak dapat menghapus produk jika id tidak valid"
    );
  }
  
  public function test_DUPL_08_03_Tidak_Dapat_Menghapus_Data_Produk_Jika_Isian_Id_Tidak_Ada_Di_Database(): void {
    $stmt = $this->db->prepare(
      "SELECT COUNT(*) FROM products"
    );
    $stmt->execute();
    $expected = $stmt->fetchColumn();
    
    $response = Product::deleteProduct(7);

    $stmt->execute();
    $result = $stmt->fetchColumn();

    $this->assertEquals(
      $expected, 
      $result, 
      "deleteProduct seharusnya tidak dapat menghapus produk jika id tidak ada di dalam database"
    );
  }
}
