<?php

use PHPUnit\Framework\TestCase;

require_once __DIR__ . '/../api/User.php';
require_once "database/Migration.php";
require_once "api/db.php";

class UserTest extends TestCase {
    protected PDO $db;
    private static $testUserId;

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

    // public function testGetUser() {
    //     $user = User::getUser(self::$testUserId);
    //     $this->assertInstanceOf(User::class, $user);
    //     $this->assertEquals('testuser', $user->getUsername());
    // }

    // public function test_DUPL_03_01_Menambahkan_Akun_Kasir_Baru_Dengan_Isian_Data_Lengkap() {
    //     $user = new User(null, 'testuser', 'password');
    //     $result = User::addUser($user);
    //     $this->assertTrue($result);

    //     $users = User::all();
    //     foreach ($users as $u) {
    //         if ($u['username'] === 'testuser') {
    //             self::$testUserId = $u['id'];
    //             break;
    //         }
    //     }

    //     $this->assertNotNull(self::$testUserId);
    // }

    // public function test_DUPL_03_02_Menambahkan_Akun_Kasir_Baru_Dengan_Isian_Data_Username_Kosong() {
    //     $user = new User(null, null, 'password');
    //     $this->expectException(PDOException::class);
    //     $result = User::addUser($user);
    // }

    // public function test_DUPL_03_03_Menambahkan_Akun_Kasir_Baru_Dengan_Isian_Data_Password_Kosong() {
    //     $user = new User(null, 'testTanpaPass', null);
    //     $this->expectException(PDOException::class);
    //     $result = User::addUser($user);
    // }

    // public function test_DUPL_03_04_Menambahkan_Akun_Kasir_Baru_Tanpa_Isian_Data() {
    //     $user = new User(null, null, null);
    //     $this->expectException(PDOException::class);
    //     $result = User::addUser($user);
    // }

    // public function test_DUPL_04_01_Mengubah_Dan_Menyimpan_Data_Akun_Kasir_Dengan_Isian_Data_Lengkap() {
    //     $d = new User(null, 'kasir2', 'pass2');
    //     User::addUser($d);
        
    //     $stmt = $this->db->prepare(
    //         "SELECT id FROM users WHERE role = 'kasir' AND username = ?"
    //     );
    //     $stmt->execute(["kasir2"]);
    //     $user = $stmt->fetch(PDO::FETCH_ASSOC);

    //     $updated = new User(
    //         $user["id"], 
    //         'kasir2updated', 
    //         'pass2updated'
    //     );
    //     $result = User::updateUser($updated);
    //     $this->assertTrue($result);

    //     $userAfter = User::getUser($user["id"]);
    //     $this->assertEquals('kasir2updated', $userAfter->getUsername());
    // }

    // public function test_DUPL_04_02_Mengubah_Dan_Menyimpan_Data_Akun_Kasir_Dengan_Isian_Data_Username_Kosong() {
    //     $d = [
    //         "username" => "kasir2",
    //         "password" => "pass2"
    //     ];

    //     $user = new User(null, $d["username"], $d["password"]);
    //     User::addUser($user);

    //     $id = null;
    //     foreach (User::all() as $u) {
    //         if ($u['username'] === $d["username"]) {
    //             $id = $u['id'];
    //             break;
    //         }
    //     }

    //     $updated = new User($id, '', 'pass2updated');
    //     $result = User::updateUser($updated);

    //     $stmt = $this->db->prepare(
    //         "SELECT * FROM users WHERE role = 'kasir' AND id = ?"
    //     );
    //     $stmt->execute([$id]);
    //     $user = $stmt->fetch(PDO::FETCH_ASSOC);
        
    //     $this->assertTrue($result);
    //     $this->assertEquals(
    //         $d["username"], 
    //         $user["username"],
    //         "username seharusnya tidak terganti jika isian username kosong"
    //     );
    // }

    // public function test_DUPL_04_03_Mengubah_Dan_Menyimpan_Data_Akun_Kasir_Dengan_Isian_Data_Password_Kosong() {
    //     $d = [
    //         "username" => "kasir2",
    //         "password" => "pass2"
    //     ];
    //     $user = new User(null, $d["username"], $d["password"]);
    //     User::addUser($user);

    //     $id = null;
    //     foreach (User::all() as $u) {
    //         if ($u['username'] === $d["username"]) {
    //             $id = $u['id'];
    //             break;
    //         }
    //     }

    //     $updated = new User($id, 'kasir2updated', '');
    //     $result = User::updateUser($updated);

    //     $stmt = $this->db->prepare(
    //         "SELECT * FROM users WHERE role = 'kasir' AND id = ?"
    //     );
    //     $stmt->execute([$id]);
    //     $user = $stmt->fetch(PDO::FETCH_ASSOC);

    //     $this->assertTrue($result);
    //     $this->assertEquals(
    //         $d["password"], 
    //         $user["password"],
    //         "password seharusnya tidak terganti jika isian password kosong"
    //     );
    // }

    // public function test_DUPL_04_04_Mengubah_Dan_Menyimpan_Data_Akun_Kasir_Dengan_Mengosongkan_Isian_Data_Username_Dan_Password() {
    //     $d = [
    //         "username" => "kasir2",
    //         "password" => "pass2"
    //     ];
    //     $user = new User(null, $d["username"], $d["password"]);
    //     User::addUser($user);

    //     $id = null;
    //     foreach (User::all() as $u) {
    //         if ($u['username'] === $d["username"]) {
    //             $id = $u['id'];
    //             break;
    //         }
    //     }

    //     $updated = new User($id, '', '');
    //     $result = User::updateUser($updated);

    //     $stmt = $this->db->prepare(
    //         "SELECT * FROM users WHERE role = 'kasir' AND id = ?"
    //     );
    //     $stmt->execute([$id]);
    //     $user = $stmt->fetch(PDO::FETCH_ASSOC);

    //     $this->assertTrue($result);
    //     $this->assertEquals(
    //         ["password" => $d["password"], "username" => $d["username"]], 
    //         ["password" => $user["password"], "username" => $user["username"]], 
    //         "username dan password seharusnya tidak terganti jika isian kosong"
    //     );
    // }

    public function test_DUPL_05_01_Menghapus_Data_Kasir_Yang_Telah_Terdaftar() {
        $user = new User(null, 'kasir5', 'pass5');
        User::addUser($user);

        $id = null;
        foreach (User::all() as $u) {
            if ($u['username'] === 'kasir5') {
                $id = $u['id'];
                break;
            }
        }

        $this->assertNotNull($id);

        $result = User::deleteUser($id);
        $this->assertTrue($result);

        $deletedUser = User::getUser($id);
        $this->assertNull($deletedUser);
    }
}
