<?php
session_start();
header('Content-Type: application/json');

try {
    $db = new PDO('mysql:host=localhost;dbname=clinic_management_system;charset=utf8mb4', 'root', '');
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Database connection failed: ' . $e->getMessage()]);
    exit;
}

// Add email column if not exists
try {
    $db->exec("ALTER TABLE users ADD COLUMN email VARCHAR(255) NULL AFTER username");
} catch (PDOException $e) {
    // Ignore if already exists
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['add_user'])) {
        $name = $_POST['name'];
        $username = $_POST['username'];
        $email = $_POST['email'];
        $role = $_POST['role'];
        $status = 'Active';
        $password = $_POST['password'];
        $password_hash = password_hash($password, PASSWORD_DEFAULT);
        try {
            $stmt = $db->prepare('INSERT INTO users (name, username, email, role, status, password) VALUES (?, ?, ?, ?, ?, ?)');
            $stmt->execute([$name, $username, $email, $role, $status, $password_hash]);
            $id = $db->lastInsertId();
            echo json_encode([
                'success' => true,
                'message' => 'User added successfully!',
                'user' => [
                    'id' => $id,
                    'name' => $name,
                    'username' => $username,
                    'email' => $email,
                    'role' => $role,
                    'status' => $status
                ]
            ]);
        } catch (PDOException $e) {
            echo json_encode(['success' => false, 'message' => 'Failed to add user: ' . $e->getMessage()]);
        }
        exit;
    }
    if (isset($_POST['edit_user'])) {
        $id = $_POST['edit_id'];
        $name = $_POST['edit_name'];
        $username = $_POST['edit_username'];
        $email = $_POST['edit_email'];
        $role = $_POST['edit_role'];
        $status = $_POST['edit_status'];
        $password = isset($_POST['edit_password']) ? $_POST['edit_password'] : '';
        try {
            if (!empty($password)) {
                $password_hash = password_hash($password, PASSWORD_DEFAULT);
                $stmt = $db->prepare('UPDATE users SET name=?, username=?, email=?, role=?, status=?, password=? WHERE id=?');
                $stmt->execute([$name, $username, $email, $role, $status, $password_hash, $id]);
            } else {
                $stmt = $db->prepare('UPDATE users SET name=?, username=?, email=?, role=?, status=? WHERE id=?');
                $stmt->execute([$name, $username, $email, $role, $status, $id]);
            }
            echo json_encode([
                'success' => true,
                'message' => 'User updated successfully!',
                'user' => [
                    'id' => $id,
                    'name' => $name,
                    'username' => $username,
                    'email' => $email,
                    'role' => $role,
                    'status' => $status
                ]
            ]);
        } catch (PDOException $e) {
            echo json_encode(['success' => false, 'message' => 'Failed to update user: ' . $e->getMessage()]);
        }
        exit;
    }
    if (isset($_POST['disable_user'])) {
        $id = $_POST['user_id'];
        try {
            $stmt = $db->prepare('UPDATE users SET status=? WHERE id=?');
            $stmt->execute(['Disabled', $id]);
            echo json_encode(['success' => true]);
        } catch (PDOException $e) {
            echo json_encode(['success' => false, 'message' => 'Failed to disable user: ' . $e->getMessage()]);
        }
        exit;
    }
}
echo json_encode(['success' => false, 'message' => 'Invalid request']);