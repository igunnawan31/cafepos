<?php

use PHPUnit\Framework\TestCase;
require_once __DIR__ . '/../api/User.php';

class UserTest extends TestCase 
{

    private static $testUserId;

    public function testGetUser()
    {
        $user = User::getUser(self::$testUserId);
        $this->assertInstanceOf(User::class, $user);
        $this->assertEquals('testuser', $user->getUsername());
    }

    public function testAddUser()
    {
        $user = new User(null, 'testuser', 'password');
        $result = User::addUser($user);
        $this->assertTrue($result);
        
        $users = User::all();
        foreach ($users as $u) {
            if($u['username'] === 'testuser') {
                self::$testUserId = $u['id'];
                break;
            }
        }

        $this->assertNotNull(self::$testUserId);
    }

    public function testMenambahkanUserTanpaUsername()
    {
        $user = new User(null, null, 'password');
        $result = User::addUser($user);
        
        $this->assertTrue($result);
    }

    public function testMenambahkanUserTanpaPassword()
    {
        $user = new User(null, 'testTanpaPass', null);
        $result = User::addUser($user);

        $this->assertTrue($result);
    }

    public function testMenambahkanUserKosongan()
    {
        $user = new User(null, null, null);
        $result = User::addUser($user);

        $this->assertTrue($result);
    }

    public function testUpdateUserDenganDataLengkap()
    {
        $user = new User(null, 'kasir1', 'pass1');
        User::addUser($user);

        $id = null;
        foreach (User::all() as $u) {
            if ($u['username'] === 'kasir1') {
                $id = $u['id'];
                break;
            }
        }

        $updated = new User($id, 'kasir1updated', 'pass1updated');
        $result = User::updateUser($updated);
        $this->assertTrue($result);

        $userAfter = User::getUser($id);
        $this->assertEquals('kasir1updated', $userAfter->getUsername());
    }

    public function testUpdateUserDenganUsernameKosong()
    {
        $user = new User(null, 'kasir2', 'pass2');
        User::addUser($user);

        $id = null;
        foreach (User::all() as $u) {
            if ($u['username'] === 'kasir2') {
                $id = $u['id'];
                break;
            }
        }

        $updated = new User($id, '', 'pass2updated');
        $result = User::updateUser($updated);
        $this->assertTrue($result);
    }

    public function testUpdateUserDenganPasswordKosong()
    {
        $user = new User(null, 'kasir3', 'pass3');
        User::addUser($user);

        $id = null;
        foreach (User::all() as $u) {
            if ($u['username'] === 'kasir3') {
                $id = $u['id'];
                break;
            }
        }

        $updated = new User($id, 'kasir3updated', '');
        $result = User::updateUser($updated);
        $this->assertTrue($result);
    }

    public function testUpdateUserDenganUsernameDanPasswordKosong()
    {
        $user = new User(null, 'kasir4', 'pass4');
        User::addUser($user);

        $id = null;
        foreach (User::all() as $u) {
            if ($u['username'] === 'kasir4') {
                $id = $u['id'];
                break;
            }
        }

        $updated = new User($id, '', '');
        $result = User::updateUser($updated);
        $this->assertTrue($result);
    }

    public function testDeleteUser()
    {
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