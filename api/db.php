<?php class DB
{
    private static $conn;
    public static function connect()
    {
        if (!self::$conn) {
            self::$conn = new PDO('sqlite:../database/pos.sqlite');
            self::$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        }
        return self::$conn;
    }
}
