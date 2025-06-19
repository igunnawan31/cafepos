<?php

class TestPDOHelper {
  protected PDO $pdo;

  public function setUp(): void {
    $this->pdo = new PDO("sqlite:memory:");
    $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $this->migrateDatabase();
  }
}