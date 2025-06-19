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
  
  public function testDapatMenambahkanProdukDenganDataLengkap(): void {
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

  public function testTidakDapatMenambahkanProdukJikaNamaEmptyString() {
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
    $this->expectException(PDOException::class);
    $response = Product::addProduct($product);
    $this->assertEquals(0, $response, "addProduct seharusnya tidak menyimpan ke db ketika nama string kosong");
  }

  public function testTidakDapatMenambahkanProdukJikaHargaEmptyString(): void {
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

  public function testTidakDapatMenambahkanProdukJikaStockEmptyString(): void {
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

  public function testTidakDapatMenambahkanProdukJikaNameNull(): void {
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

  public function testTidakDapatMenambahkanProdukJikaPriceNull(): void {
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

  public function testTidakDapatMenambahkanProdukJikaStockNull(): void {
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

  public function testDapatMenambahkanProdukDenganPriceNol(): void {
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
    $this->assertEquals(0, $response, "addProduct seharusnya menyimpan ke db ketika harga 0");

    $stmt = $this->db->prepare("SELECT COUNT(*) as total FROM products WHERE name = :name AND price = :price AND stock = :stock");
    $stmt->execute([
      ':name' => $d['name'],
      ':price' => $d['price'],
      ':stock' => $d['stock']
    ]);
    $result = $stmt->fetchColumn();

    $this->assertEquals(1, $result, "addProduct seharusnya dapat menyimpan produk dengan harga 0");
  }

  public function testDapatMenambahkanProdukDenganStockNol(): void {
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
    $this->assertEquals(0, $response, "addProduct seharusnya menyimpan ke db ketika harga 0");

    $stmt = $this->db->prepare("SELECT COUNT(*) as total FROM products WHERE name = :name AND price = :price AND stock = :stock");
    $stmt->execute([
      ':name' => $d['name'],
      ':price' => $d['price'],
      ':stock' => $d['stock']
    ]);
    $result = $stmt->fetchColumn();

    $this->assertEquals(1, $result, "addProduct seharusnya dapat menyimpan produk dengan stock 0");
  }
}