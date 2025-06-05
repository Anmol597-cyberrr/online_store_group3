<?php
require 'config/db.php';

header('Content-Type: application/json');

// Add item to cart
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents("php://input"), true);

    if (!isset($data['user_id'], $data['product_id'], $data['quantity'])) {
        http_response_code(400);
        echo json_encode(['error' => 'Missing required fields']);
        exit;
    }

    $user_id = $data['user_id'];
    $product_id = $data['product_id'];
    $quantity = $data['quantity'];

    try {
        // Check if item already in cart → update quantity
        $stmt = $pdo->prepare("SELECT * FROM cart WHERE user_id = ? AND product_id = ?");
        $stmt->execute([$user_id, $product_id]);

        if ($stmt->rowCount() > 0) {
            $pdo->prepare("UPDATE cart SET quantity = quantity + ? WHERE user_id = ? AND product_id = ?")
                ->execute([$quantity, $user_id, $product_id]);
        } else {
            $pdo->prepare("INSERT INTO cart (user_id, product_id, quantity) VALUES (?, ?, ?)")
                ->execute([$user_id, $product_id, $quantity]);
        }

        http_response_code(201);
        echo json_encode(['message' => 'Product added to cart']);
        exit;

    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
        exit;
    }
}

// Get cart items for a user
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['user_id'])) {
    $user_id = $_GET['user_id'];

    try {
        $stmt = $pdo->prepare("
            SELECT c.id, c.quantity, p.description, p.price, p.image 
            FROM cart c 
            JOIN products p ON c.product_id = p.id 
            WHERE c.user_id = ?
        ");
        $stmt->execute([$user_id]);
        $cart = $stmt->fetchAll(PDO::FETCH_ASSOC);

        http_response_code(200);
        echo json_encode($cart);
        exit;

    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode(['error' => 'Failed to fetch cart']);
        exit;
    }
}

http_response_code(405);
echo json_encode(['error' => 'Method not allowed']);
exit;
