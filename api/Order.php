<?php require_once 'db.php';
class Order
{
    public static function getAll()
    {
        $db = DB::connect();
        $stmt = $db->prepare("
            SELECT 
                o.id AS order_id,
                datetime(o.created_at, '+7 hours') AS created_at,
                u.username AS kasir,
                p.name AS product_name,
                od.quantity,
                od.price_at_time AS price
            FROM orders o
            JOIN order_details od ON o.id = od.order_id
            JOIN products p ON od.product_id = p.id
            JOIN users u ON o.user_id = u.id
            ORDER BY o.created_at DESC
        ");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function all($user_id)
    {
        $db = DB::connect();
        $stmt = $db->prepare("
            SELECT 
                o.id AS order_id,
                datetime(o.created_at, '+7 hours') AS created_at,
                u.username AS kasir,
                p.name AS product_name,
                od.quantity,
                od.price_at_time AS price
            FROM orders o
            JOIN order_details od ON o.id = od.order_id
            JOIN products p ON od.product_id = p.id
            JOIN users u ON o.user_id = u.id
            WHERE o.user_id = ?
            ORDER BY o.created_at DESC
        ");
        $stmt->execute([$user_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function addItem($items, $user_id)
    {
        $db = DB::connect();
        $db->beginTransaction();

        foreach ($items as $item) {
            $product_id = $item['product_id'];
            $quantity = $item['quantity'];

            $stmt = $db->prepare("SELECT stock FROM products WHERE id = ?");
            $stmt->execute([$product_id]);
            $stock = $stmt->fetchColumn();

            if ($stock === false || $stock < $quantity) {
                $db->rollBack();
                echo json_encode([
                    'error' => "Stok tidak cukup untuk produk ID $product_id"
                ]);
                return;
            }
        }

        $stmtOrder = $db->prepare("INSERT INTO orders (user_id) VALUES (?)");
        $stmtOrder->execute([$user_id]);
        $order_id = $db->lastInsertId();

        $stmtDetail = $db->prepare("INSERT INTO order_details (order_id, product_id, quantity, price_at_time) VALUES (?, ?, ?, ?)");
        $stmtUpdateStock = $db->prepare("UPDATE products SET stock = stock - ? WHERE id = ?");

        foreach ($items as $item) {
            $product_id = $item['product_id'];
            $quantity = $item['quantity'];

            $stmtPrice = $db->prepare("SELECT price FROM products WHERE id = ?");
            $stmtPrice->execute([$product_id]);
            $price = $stmtPrice->fetchColumn();

            $stmtUpdateStock->execute([$quantity, $product_id]);
            $stmtDetail->execute([$order_id, $product_id, $quantity, $price]);
        }

        $db->commit();

        echo json_encode([
            'status' => 'success',
            'order_id' => $order_id
        ]);
    }

    public static function deleteOrder($id)
    {
        $db = DB::connect();
        $db->beginTransaction();
        $stmt = $db->prepare("DELETE FROM order_details WHERE order_id = ?");
        $stmt->execute([$id]);
        $stmt = $db->prepare("DELETE FROM orders WHERE id = ?");
        $stmt->execute([$id]);
        $db->commit();
    }

}
