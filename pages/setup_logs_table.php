<?php
require_once 'db_connection.php';

$sql = "CREATE TABLE IF NOT EXISTS Logs (
    log_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    type VARCHAR(50) NOT NULL,
    description TEXT NOT NULL,
    name VARCHAR(255) NOT NULL,
    time TIME NOT NULL,
    date DATE NOT NULL,
    FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE
)";

if ($conn->query($sql) === TRUE) {
    echo "Table Logs created successfully";
} else {
    echo "Error creating table: " . $conn->error;
}

$conn->close();
?>
