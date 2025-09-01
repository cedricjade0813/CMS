<?php
header('Content-Type: application/json');
try {
    $db = new PDO('mysql:host=localhost;dbname=clinic_management_system;charset=utf8', 'root', '');
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'DB connect failed']);
    exit;
}

// Get form data
$full_name = isset($_POST['full_name']) ? trim($_POST['full_name']) : '';
$email = isset($_POST['email']) ? trim($_POST['email']) : '';
$password = isset($_POST['password']) ? trim($_POST['password']) : '';
$address = isset($_POST['address']) ? trim($_POST['address']) : '';
$contact = isset($_POST['contact']) ? trim($_POST['contact']) : '';
$emergency_contact = isset($_POST['emergency_contact']) ? trim($_POST['emergency_contact']) : '';
$age = isset($_POST['age']) ? (int)$_POST['age'] : 0;
$gender = isset($_POST['gender']) ? trim($_POST['gender']) : '';
$department = isset($_POST['department']) ? trim($_POST['department']) : '';
$college_course = isset($_POST['college_course']) ? trim($_POST['college_course']) : '';
$civil_status = isset($_POST['civil_status']) ? trim($_POST['civil_status']) : '';
$citizenship = isset($_POST['citizenship']) ? trim($_POST['citizenship']) : '';

// Validate required fields
$missing_fields = [];
if ($full_name === '') $missing_fields[] = 'full_name';
if ($email === '') $missing_fields[] = 'email';
if ($password === '') $missing_fields[] = 'password';
if ($address === '') $missing_fields[] = 'address';
if ($contact === '') $missing_fields[] = 'contact';
if ($emergency_contact === '') $missing_fields[] = 'emergency_contact';
if ($age <= 0) $missing_fields[] = 'age';
if ($gender === '') $missing_fields[] = 'gender';
if ($department === '') $missing_fields[] = 'department';
if ($civil_status === '') $missing_fields[] = 'civil_status';
if ($citizenship === '') $missing_fields[] = 'citizenship';

if (!empty($missing_fields)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Missing required fields: ' . implode(', ', $missing_fields)]);
    exit;
}

// Validate college course if department is College
if ($department === 'College' && $college_course === '') {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'College course is required when department is College']);
    exit;
}

// Validate email format
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Invalid email format']);
    exit;
}

// Validate password length
if (strlen($password) < 6) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Password must be at least 6 characters long']);
    exit;
}

// Check if email already exists
try {
    $checkStmt = $db->prepare('SELECT COUNT(*) FROM faculty WHERE email = ?');
    $checkStmt->execute([$email]);
    $exists = $checkStmt->fetchColumn() > 0;
    
    if ($exists) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Email already exists. Please use a different email.']);
        exit;
    }
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Error checking email']);
    exit;
}

try {
    // Get next faculty ID
    $stmt = $db->prepare('SELECT MAX(faculty_id) FROM faculty');
    $stmt->execute();
    $max_id = $stmt->fetchColumn();
    $next_id = $max_id ? $max_id + 1 : 1;

    // Hash the password
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // Insert faculty record
    $stmt = $db->prepare('INSERT INTO faculty (
        faculty_id, full_name, address, contact, emergency_contact, age, 
        department, college_course, gender, email, password, civil_status, citizenship
    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)');
    
    $stmt->execute([
        $next_id,
        $full_name,
        $address,
        $contact,
        $emergency_contact,
        $age,
        $department,
        $college_course,
        $gender,
        $email,
        $hashed_password,
        $civil_status,
        $citizenship
    ]);
    
    echo json_encode(['success' => true, 'message' => 'Faculty added successfully!', 'faculty_id' => $next_id]);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Save failed: ' . $e->getMessage()]);
}
?>
