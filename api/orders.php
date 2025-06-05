<?php
require 'config/db.php';

header('Content-Type: application/json');

// ✅ PLACE an Order
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents("php://input"), true);

    if (!isset($data['user_id'])) {
        http_response_code(400);
        echo json_encode(['error' => 'Missing user_id']);
        exit;
    }

    $user_id = $data['user_id'];

    try {
        // Fetch cart items
        $stmt = $pdo->prepare("
            SELECT c.product_id, c.quantity, p.price, p.shipping_cost 
            FROM cart c
            JOIN products p ON c.product_id = p.id
            WHERE c.user_id = ?
        ");
        $stmt->execute([$user_id]);
        $cartItems = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if (empty($cartItems)) {
            http_response_code(400);
            echo json_encode(['error' => 'Cart is empty']);
            exit;
        }

        // Calculate total
        $total = 0;
        foreach ($cartItems as $item) {
            $total += ($item['price'] * $item['quantity']) + $item['shipping_cost'];
        }

        // Insert order
        $pdo->beginTransaction();

        $stmt = $pdo->prepare("INSERT INTO orders (user_id, total_amount) VALUES (?, ?)");
        $stmt->execute([$user_id, $total]);
        $order_id = $pdo->lastInsertId();

        // Insert order items
        $stmt = $pdo->prepare("INSERT INTO order_items (order_id, product_id, quantity, price) VALUES (?, ?, ?, ?)");

        foreach ($cartItems as $item) {
            $stmt->execute([
                $order_id,
                $item['product_id'],
                $item['quantity'],
                $item['price']
            ]);
        }

        // Clear cart
        $stmt = $pdo->prepare("DELETE FROM cart WHERE user_id = ?");
        $stmt->execute([$user_id]);

        $pdo->commit();

        http_response_code(201);
        echo json_encode(['message' => 'Order placed successfully', 'order_id' => $order_id]);
        exit;

    } catch (PDOException $e) {
        $pdo->rollBack();
        http_response_code(500);
        echo json_encode(['error' => 'Order failed: ' . $e->getMessage()]);
        exit;
    }
}

// ✅ GET Orders for a user
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['user_id_]()]()_
