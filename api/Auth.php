<?php require_once 'db.php';
class Auth
{
    public static function login($username, $password)
    {
        $stmt = DB::connect()->prepare("SELECT * FROM users WHERE username = ? AND password = ?");
        $stmt->execute([$username, $password]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        return $user ? ['success' => true, 'user' => $user] : ['success' => false];
    }
}
