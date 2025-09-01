
// Database connection for all includes
try {
	$db = new PDO('mysql:host=localhost;dbname=clinic_management_system;charset=utf8', 'root', '');
	$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (Exception $e) {
	die('Database connection error: ' . $e->getMessage());
}
