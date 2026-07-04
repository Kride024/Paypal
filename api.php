<?php
header('Content-Type: application/json');
require __DIR__ . '/includes/db.php';

$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {

    case 'GET':
        // READ - list all products, or a single one via ?id=
        if (isset($_GET['id'])) {
            $stmt = $pdo->prepare('SELECT * FROM products WHERE id = ?');
            $stmt->execute([$_GET['id']]);
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            echo json_encode($row ?: []);
        } else {
            $stmt = $pdo->query('SELECT * FROM products ORDER BY id DESC');
            echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
        }
        break;

    case 'POST':
        // CREATE
        $data = json_decode(file_get_contents('php://input'), true);
        if (empty($data['name']) || !isset($data['price'])) {
            http_response_code(422);
            echo json_encode(['error' => 'name and price are required']);
            break;
        }
        $stmt = $pdo->prepare('INSERT INTO products (name, description, price) VALUES (?, ?, ?)');
        $stmt->execute([$data['name'], $data['description'] ?? '', $data['price']]);
        echo json_encode(['success' => true, 'id' => $pdo->lastInsertId()]);
        break;

    case 'PUT':
        // UPDATE
        $data = json_decode(file_get_contents('php://input'), true);
        if (empty($data['id'])) {
            http_response_code(422);
            echo json_encode(['error' => 'id is required']);
            break;
        }
        $stmt = $pdo->prepare('UPDATE products SET name = ?, description = ?, price = ? WHERE id = ?');
        $stmt->execute([$data['name'], $data['description'] ?? '', $data['price'], $data['id']]);
        echo json_encode(['success' => true]);
        break;

    case 'DELETE':
        // DELETE
        parse_str(file_get_contents('php://input'), $data);
        $id = $_GET['id'] ?? ($data['id'] ?? null);
        if (!$id) {
            http_response_code(422);
            echo json_encode(['error' => 'id is required']);
            break;
        }
        $stmt = $pdo->prepare('DELETE FROM products WHERE id = ?');
        $stmt->execute([$id]);
        echo json_encode(['success' => true]);
        break;

    default:
        http_response_code(405);
        echo json_encode(['error' => 'Method not allowed']);
}
