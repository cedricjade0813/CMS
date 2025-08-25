<?php
// Test script to verify appointment system
try {
    $db = new PDO('mysql:host=localhost;dbname=clinic_management_system;charset=utf8', 'root', '');
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "<h2>Appointment System Test</h2>";
    
    // Check doctor_schedules table
    echo "<h3>1. Doctor Schedules:</h3>";
    $stmt = $db->query('SELECT * FROM doctor_schedules ORDER BY schedule_date, schedule_time');
    $schedules = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    if (empty($schedules)) {
        echo "<p>No doctor schedules found. Please add a schedule from staff panel.</p>";
    } else {
        echo "<table border='1' style='border-collapse: collapse;'>";
        echo "<tr><th>ID</th><th>Doctor</th><th>Date</th><th>Time</th></tr>";
        foreach ($schedules as $schedule) {
            echo "<tr>";
            echo "<td>" . $schedule['id'] . "</td>";
            echo "<td>" . $schedule['doctor_name'] . "</td>";
            echo "<td>" . $schedule['schedule_date'] . "</td>";
            echo "<td>" . $schedule['schedule_time'] . "</td>";
            echo "</tr>";
        }
        echo "</table>";
    }
    
    // Check appointments table
    echo "<h3>2. Appointments:</h3>";
    $stmt = $db->query('SELECT * FROM appointments ORDER BY date, time');
    $appointments = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    if (empty($appointments)) {
        echo "<p>No appointments found.</p>";
    } else {
        echo "<table border='1' style='border-collapse: collapse;'>";
        echo "<tr><th>ID</th><th>Student ID</th><th>Date</th><th>Time</th><th>Reason</th><th>Status</th></tr>";
        foreach ($appointments as $appt) {
            echo "<tr>";
            echo "<td>" . $appt['id'] . "</td>";
            echo "<td>" . $appt['student_id'] . "</td>";
            echo "<td>" . $appt['date'] . "</td>";
            echo "<td>" . $appt['time'] . "</td>";
            echo "<td>" . $appt['reason'] . "</td>";
            echo "<td>" . $appt['status'] . "</td>";
            echo "</tr>";
        }
        echo "</table>";
    }
    
    // Count slots per date
    echo "<h3>3. Slots per Date:</h3>";
    $stmt = $db->query('SELECT schedule_date, doctor_name, COUNT(*) as slot_count FROM doctor_schedules GROUP BY schedule_date, doctor_name ORDER BY schedule_date');
    $slot_counts = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "<table border='1' style='border-collapse: collapse;'>";
    echo "<tr><th>Date</th><th>Doctor</th><th>Total Slots</th></tr>";
    foreach ($slot_counts as $count) {
        echo "<tr>";
        echo "<td>" . $count['schedule_date'] . "</td>";
        echo "<td>" . $count['doctor_name'] . "</td>";
        echo "<td>" . $count['slot_count'] . "</td>";
        echo "</tr>";
    }
    echo "</table>";
    
    echo "<h3>4. System Status:</h3>";
    echo "<p>✅ Database connection: Working</p>";
    echo "<p>✅ Tables exist: Working</p>";
    echo "<p>✅ Doctor schedules: " . count($schedules) . " slots found</p>";
    echo "<p>✅ Appointments: " . count($appointments) . " appointments found</p>";
    
} catch (PDOException $e) {
    echo "<p style='color: red;'>Database Error: " . $e->getMessage() . "</p>";
}
?>
