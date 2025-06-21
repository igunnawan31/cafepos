<?php

require_once "api/User.php";
require_once "api/Auth.php";
require_once "api/routes.php";
require_once "database/Migration.php";
require_once "api/db.php";

use PHPUnit\Framework\TestCase;

final class AuthLoginTest extends TestCase {
  protected PDO $db;

  protected function setUp(): void {
    parent::setUp();
    $this->db = DB::connect();
    $this->migrateDatabase();
    $this->createTestUser();
  }

  protected function migrateDatabase(): void {
    $migration = new DatabaseMigration;
    $this->db->exec(
      $migration->migrate()
    );
  }

  protected function createTestUser(): void {
    $user = new User(
      0,
      "user",
      "password",
    );
    User::addUser($user);
  }

  protected function getTestUser() {
    $users = User::all();
    return $users[count($users) - 1];
  }

  public function test_DUPL_01_01_Pengujian_Login_Dengan_Input_Username_Benar_Dan_Password_Benar() {
    $user = $this->getTestUser();

    $response = Auth::login(
      $user["username"],
      $user["password"]
    );

    $this->assertTrue(
      $response["success"],
      "seharusnya dapat login dengan data valid"
    );
  }

  public function test_DUPL_01_02_Pengujian_Login_Dengan_Input_Username_Salah_Dan_Password_Benar() {
    $user = $this->getTestUser();

    $response = Auth::login(
      "users",
      $user["password"]
    );

    $this->assertFalse(
      $response["success"],
      "seharusnya tidak dapat login dengan data username salah"
    );
  }

  public function test_DUPL_01_03_Pengujian_Login_Dengan_Input_Username_Benar_Dan_Password_Salah() {
    $user = $this->getTestUser();

    $response = Auth::login(
      $user["username"],
      "12345"
    );

    $this->assertFalse(
      $response["success"],
      "seharusnya tidak dapat login dengan data password salah"
    );
  }

  public function test_DUPL_01_04_Pengujian_Login_Dengan_Input_Username_Salah_Dan_Password_Salah() {
    $user = $this->getTestUser();

    $response = Auth::login(
      "username_acak",
      "12345"
    );

    $this->assertFalse(
      $response["success"],
      "seharusnya tidak dapat login dengan data username dan password salah"
    );
  }

  public function test_DUPL_01_05_Pengujian_Login_Dengan_Input_Username_Dan_Password_Kosong() {
    $user = $this->getTestUser();

    $response = Auth::login(
      "",
      ""
    );

    $this->assertFalse(
      $response["success"],
      "seharusnya tidak dapat login jika username dan password kosong"
    );
  }
}
