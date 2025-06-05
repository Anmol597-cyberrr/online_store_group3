<<<<<<< HEAD
=======

>>>>>>> 6f7830b0c05de77302014d183576bf5bc9a1133d
<?php
require 'config/db.php';

header('Content-Type: application/json');

// ✅ POST a new comment
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents("php://input"), true);

    if (!isset($data['user_id'], $data['product_id'], $data['rating'], $data['comment'])) {
        http_response_code(400);
        echo json_encode(['error' => 'Missing required fields']);
        exit;
    }

    $user_id = $data['user_id'];
    $product_id = $data['product_id'];
    $rating = $data['rating'];
    $comment = $data['comment'];
    $image_url = isset($data['image']) ? $data['image'] : null;

    try {
        $stmt = $pdo->prepare("INSERT INTO comments (user_id, product_id, rating, comment, image) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$user_id, $product_id, $rating, $comment, $image_url]);

        http_response_code(201);
        echo json_encode(['message' => 'Comment posted']);
        exit;

    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
        exit;
    }
}

// ✅ GET comments for a product
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['product_id'])) {
    $product_id = $_GET['product_id'];

    try {
        $stmt = $pdo->prepare("
            SELECT c.id, c.rating, c.comment, c.image, u.username, c.created_at
            FROM comments c
            JOIN users u ON c.user_id = u.id
            WHERE c.product_id = ?
            ORDER BY c.created_at DESC
        ");
        $stmt->execute([$product_id]);
        $comments = $stmt->fetchAll(PDO::FETCH_ASSOC);

        http_response_code(200);
        echo json_encode($comments);
        exit;

    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode(['error' => 'Failed to fetch comments']);
        exit;
    }
}

http_response_code(405);
echo json_encode(['error' => 'Method not allowed']);
exit;
