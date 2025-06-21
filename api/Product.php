<?php require_once 'db.php';
class Product
{
    private $id;
    private $name;
    private $price;
    private $stock;

    function __construct($id, $name, $price, $stock) {
        $this->id = $id;
        $this->name = $name;
        $this->price = $price;
        $this->stock = $stock;
    }

    public static function all()
    {
        $stmt = DB::connect()->query("SELECT * FROM products");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function getProduct($id)
    {
        $db = DB::connect();
        $stmt = $db->prepare("SELECT * FROM products WHERE id = ?");
        $stmt->execute([$id]);
        $data = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($data) {
            return new Product($data['id'], $data['name'], $data['price'], $data['stock']);
        } else {
            return null;
        }
    }

    public static function addProduct(Product $product)
    {
        $db = DB::connect();
        $stmt = $db->prepare("INSERT INTO products (name, price, stock) VALUES (?, ?, ?)");
        return $stmt->execute([$product->name, $product->price, $product->stock]);
    }

    public static function updateProduct(Product $product)
    {
        $db = DB::connect();
        $stmt = $db->prepare("UPDATE products SET name = ?, price = ?, stock = ? WHERE id = ?");
        return $stmt->execute([$product->name, $product->price, $product->stock, $product->id]);
    }

    public static function deleteProduct($id)
    {
        $db = DB::connect();
        $stmt = $db->prepare("DELETE FROM products WHERE id = ?");
        return $stmt->execute([$id]);
    }

    public function getName() {
        return $this->name;
    }
}