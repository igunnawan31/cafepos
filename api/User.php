<?php require_once 'db.php';
class User
{
    private $id;
    private $username;
    private $password;
    private $role;

    public function __construct($id, $username, $password, $role = 'kasir') {
        $this->id = $id;
        $this->username = $username;
        $this->password = $password;
        $this->role = $role;
    }

    public function getUsername() {
        return $this->username;
    }

    public function getId() {
        return $this->id;
    }

    public static function all()
    {
        $stmt = DB::connect()->query("SELECT * FROM users WHERE role = 'kasir'");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function getUser($id)
    {
        $db = DB::connect();
        $stmt = $db->prepare("SELECT * FROM users WHERE id = ? and role = 'kasir'");
        $stmt->execute([$id]);
        $data = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($data) {
            return new User($data['id'], $data['username'], $data['password'], $data['role']);
        } else {
            return null;
        }
    }

    public static function addUser(User $user)
    {
        $db = DB::connect();
        $stmt = $db->prepare("INSERT INTO users (username, password, role) VALUES (?, ?, 'kasir')");
        return $stmt->execute([$user->username, $user->password]);
    }

    public static function updateUser(User $user)
    {
        $db = DB::connect();
        $stmt = $db->prepare("UPDATE users SET username = ?, password = ? WHERE id = ? and role = 'kasir'");
        return $stmt->execute([$user->username, $user->password, $user->id]);
    }

    public static function deleteUser($id)
    {
        $db = DB::connect();
        $stmt = $db->prepare("DELETE FROM users WHERE id = ? and role = 'kasir'");
        return $stmt->execute([$id]);
    }
}
