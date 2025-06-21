<?php

require_once "api/User.php";
require_once "api/routes.php";
require_once "database/Migration.php";
require_once "api/db.php";

use PHPUnit\Framework\TestCase;

final class AddUserTest extends TestCase {
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
}