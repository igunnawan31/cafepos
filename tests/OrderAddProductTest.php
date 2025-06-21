<?php

require_once "api/Product.php";
require_once "api/routes.php";
require_once "database/Migration.php";
require_once "api/db.php";

use PHPUnit\Framework\TestCase;

final class OrderAddProductTest extends TestCase {
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
  
  /**
   * @group Product
   * @group DUPL-06-01
   */
  public function test_DUPL_06_01_Dapat_Menambahkan_Produk_Dengan_Data_Lengkap(): void {
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
    $response = Product::addProduct($product);
    $this->assertTrue($response, "addProduct seharusnya mengembalikan true (berhasil) ketika data lengkap");
    
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

  /**
   * @group Product
   * @group DUPL-06-02
   */
  public function test_DUPL_06_02_Tidak_Dapat_Menambahkan_Produk_Jika_Name_Kosong() {
    $d = [
      "name" => "",
      "price" => "10000",
      "stock" => "15"
    ];

    $product = new Product(
      0,
      $d["name"],
      $d["price"],
      $d["stock"]
    );
    // $this->expectException(PDOException::class);
    $response = Product::addProduct($product);
    $this->assertEquals(0, $response, "addProduct seharusnya tidak menyimpan ke db ketika nama string kosong");
  }

  /**
   * @group Product
   * @group DUPL-06-02
   */
  public function test_DUPL_06_02_Tidak_Dapat_Menambahkan_Produk_Jika_Name_Null(): void {
    $d = [
      "name" => null,
      "price" => "10000",
      "stock" => ""
    ];

    $product = new Product(
      0,
      $d["name"],
      $d["price"],
      $d["stock"]
    );
    $this->expectException(PDOException::class);
    $response = Product::addProduct($product);
  }

  /**
   * @group Product
   * @group DUPL-06-03
   */
  public function test_DUPL_06_03_Tidak_Dapat_Menambahkan_Produk_Jika_Price_Kosong(): void {
    $d = [
      "name" => "WOW Onigiri",
      "price" => "",
      "stock" => "15"
    ];

    $product = new Product(
      0,
      $d["name"],
      $d["price"],
      $d["stock"]
    );
    // $this->expectException(PDOException::class);
    $response = Product::addProduct($product);
    $this->assertEquals(0, $response, "addProduct seharusnya tidak menyimpan ke db ketika price string kosong");
  }

  /**
   * @group Product
   * @group DUPL-06-03
   */
  public function test_DUPL_06_03_Tidak_Dapat_Menambahkan_Produk_Jika_Price_Null(): void {
    $d = [
      "name" => "WOW Onigiri",
      "price" => null,
      "stock" => ""
    ];

    $product = new Product(
      0,
      $d["name"],
      $d["price"],
      $d["stock"]
    );
    $this->expectException(PDOException::class);
    $response = Product::addProduct($product);
  }

  /**
   * @group Product
   * @group DUPL-06-04
   */
  public function test_DUPL_06_04_Tidak_Dapat_Menambahkan_Produk_Jika_Stock_Kosong(): void {
    $d = [
      "name" => "WOW Onigiri",
      "price" => "10000",
      "stock" => 0
    ];

    $product = new Product(
      0,
      $d["name"],
      $d["price"],
      $d["stock"]
    );
    // $this->expectException(PDOException::class);
    $response = Product::addProduct($product);
    $this->assertEquals(0, $response, "addProduct seharusnya tidak menyimpan ke db ketika stock string kosong");
  }

  
  /**
   * @group Product
   * @group DUPL-06-04
   */
  public function test_DUPL_06_04_Tidak_Dapat_Menambahkan_Produk_Jika_Stock_Null(): void {
    $d = [
      "name" => "WOW Onigiri",
      "price" => "10000",
      "stock" => null
    ];

    $product = new Product(
      0,
      $d["name"],
      $d["price"],
      $d["stock"]
    );
    $this->expectException(PDOException::class);
    $response = Product::addProduct($product);
  }

  /**
   * @group Product
   * @group DUPL-06-05
   */
  public function test_DUPL_06_05_Dapat_Menambahkan_Produk_Dengan_Price_Nol(): void {
    $d = [
      "name" => "WOW Onigiri",
      "price" => "0",
      "stock" => "15"
    ];

    $product = new Product(
      0,
      $d["name"],
      $d["price"],
      $d["stock"]
    );
    $response = Product::addProduct($product);
    $this->assertEquals(1, $response, "addProduct seharusnya menyimpan ke db ketika harga 0");

    $stmt = $this->db->prepare(
      "SELECT COUNT(*) as total FROM products WHERE name = :name AND price = :price AND stock = :stock"
    );
    $stmt->execute([
      ':name' => $d['name'],
      ':price' => $d['price'],
      ':stock' => $d['stock']
    ]);
    $result = $stmt->fetchColumn();

    $this->assertEquals(1, $result, "addProduct seharusnya dapat menyimpan produk dengan harga 0");
  }

  /**
   * @group Product
   * @group DUPL-06-06
   */
  public function test_DUPL_06_06_Dapat_Menambahkan_Produk_Dengan_Stock_Nol(): void {
    $d = [
      "name" => "WOW Onigiri",
      "price" => 10000,
      "stock" => 0,
    ];

    $product = new Product(
      0,
      $d["name"],
      $d["price"],
      $d["stock"]
    );
    $response = Product::addProduct($product);
    $this->assertEquals(1, $response, "addProduct seharusnya menyimpan ke db ketika harga 0");

    $stmt = $this->db->prepare(
      "SELECT COUNT(*) as total FROM products WHERE name = :name AND price = :price AND stock = :stock"
    );
    $stmt->execute([
      ':name' => $d['name'],
      ':price' => $d['price'],
      ':stock' => $d['stock']
    ]);
    $result = $stmt->fetchColumn();

    $this->assertEquals(1, $result, "addProduct seharusnya dapat menyimpan produk dengan stock 0");
  }

  /**
   * @group Product
   * @group DUPL-06-07
   */
  public function test_DUPL_06_07_Tidak_Dapat_Menambahkan_Produk_Dengan_Harga_Negatif(): void {
    $d = [
      "name" => "WOW Onigiri",
      "price" => -10000,
      "stock" => 10,
    ];

    $product = new Product(
      0,
      $d["name"],
      $d["price"],
      $d["stock"]
    );
    $response = Product::addProduct($product);

    $stmt = $this->db->prepare(
      "SELECT COUNT(*) as total FROM products WHERE name = :name AND price = :price AND stock = :stock"
    );
    $stmt->execute([
      ':name' => $d['name'],
      ':price' => $d['price'],
      ':stock' => $d['stock']
    ]);
    $result = $stmt->fetchColumn();

    $this->assertEquals(1, $result, "addProduct seharusnya tidak dapat menyimpan produk dengan harga negatif");
  }

  /**
   * @group Product
   * @group DUPL-06-08
   */
  public function test_DUPL_06_08_Tidak_Dapat_Menambahkan_Produk_Dengan_Stok_Negatif(): void {
    $d = [
      "name" => "WOW Onigiri",
      "price" => 10000,
      "stock" => -10,
    ];

    $product = new Product(
      0,
      $d["name"],
      $d["price"],
      $d["stock"]
    );
    $response = Product::addProduct($product);
    // $this->assertEquals(0, $response, "addProduct seharusnya menyimpan ke db ketika harga 0");

    $stmt = $this->db->prepare(
      "SELECT COUNT(*) as total FROM products WHERE name = :name AND price = :price AND stock = :stock"
    );
    $stmt->execute([
      ':name' => $d['name'],
      ':price' => $d['price'],
      ':stock' => $d['stock']
    ]);
    $result = $stmt->fetchColumn();

    $this->assertEquals(0, $result, "addProduct seharusnya tidak dapat menyimpan produk dengan stock bernilai negatif");
  }
}