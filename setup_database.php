<?php
// =================================================================
// DATABASE SETUP & SEEDING SCRIPT
// =================================================================
// This script resets the database `IBBS_PROTOTYPE` and populates it
// with initial sample data for demonstration/defense purposes.
// =================================================================

error_reporting(E_ALL);
ini_set('display_errors', '1');

$server_name = "localhost";
$username = "root";
$password = "";
$port = 3306;

// Create connection
$conn = new mysqli($server_name, $username, $password, "", $port);
if ($conn->connect_error) {
    die("Connection Failed: " . $conn->connect_error);
}

// 1. DROP & CREATE DATABASE
echo "Dropping database if exists...<br>";
$conn->query("DROP DATABASE IF EXISTS IBBS_PROTOTYPE");
echo "Creating database IBBS_PROTOTYPE...<br>";
if ($conn->query("CREATE DATABASE IBBS_PROTOTYPE") === TRUE) {
    echo "Database created successfully.<br>";
} else {
    die("Error creating database: " . $conn->error);
}

// Select Database
$conn->select_db("IBBS_PROTOTYPE");

// =================================================================
// 2. CREATE TABLES
// =================================================================

// USERS
$sql_users = "CREATE TABLE users (
    user_id INT AUTO_INCREMENT PRIMARY KEY,
    first_name VARCHAR(50) NOT NULL,
    last_name VARCHAR(50) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    phone_number VARCHAR(15) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    role ENUM('PASSENGER','ADMIN','AGENT') NOT NULL DEFAULT 'PASSENGER'
)";

// DRIVERS
$sql_drivers = "CREATE TABLE drivers (
    driver_id INT AUTO_INCREMENT PRIMARY KEY,
    national_id VARCHAR(20) UNIQUE NOT NULL,
    full_name VARCHAR(100) NOT NULL,
    phone VARCHAR(15) UNIQUE NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL
)";

// BUSES
// Added ON DELETE SET NULL to driver_id foreign key as per user schema request
$sql_buses = "CREATE TABLE buses (
    bus_id INT AUTO_INCREMENT PRIMARY KEY,
    reg_no VARCHAR(20) UNIQUE NOT NULL,
    bus_name VARCHAR(50),
    max_passengers INT NOT NULL,
    seat_layout VARCHAR(20) NOT NULL,
    driver_id INT,
    FOREIGN KEY (driver_id) REFERENCES drivers(driver_id) ON DELETE SET NULL
)";

// ROUTES
// removed arrival_time as per user schema provided in step 26
$sql_routes = "CREATE TABLE routes (
    route_id INT AUTO_INCREMENT PRIMARY KEY,
    from_location VARCHAR(100) NOT NULL,
    to_location VARCHAR(100) NOT NULL,
    departure_date DATE NOT NULL,
    departure_time TIME NOT NULL,
    cost DECIMAL(10,2) NOT NULL,
    bus_id INT NOT NULL,
    FOREIGN KEY (bus_id) REFERENCES buses(bus_id)
)";

// BOOKINGS
// removed passenger info columns as per user schema provided in step 26
$sql_bookings = "CREATE TABLE bookings (
    booking_id INT AUTO_INCREMENT PRIMARY KEY,
    booking_time DATETIME NOT NULL,
    seat_number VARCHAR(5) NOT NULL,
    booking_status ENUM('PAID','CHECKED_IN','CANCELLED') DEFAULT 'PAID',
    qr_token VARCHAR(255) UNIQUE NOT NULL,
    user_id INT NOT NULL,
    route_id INT NOT NULL,
    bus_id INT NOT NULL,
    FOREIGN KEY (user_id) REFERENCES users(user_id),
    FOREIGN KEY (route_id) REFERENCES routes(route_id) ON DELETE CASCADE,
    FOREIGN KEY (bus_id) REFERENCES buses(bus_id),
    passenger_name VARCHAR(255) NOT NULL DEFAULT '',
    passenger_age INT NOT NULL DEFAULT 0,
    passenger_dob DATE NULL,
    passenger_id_number VARCHAR(50) NOT NULL DEFAULT '',
    UNIQUE (route_id, seat_number)
)";

// FEEDBACK
$sql_feedback = "CREATE TABLE feedback (
    feedback_id INT AUTO_INCREMENT PRIMARY KEY,
    rating INT CHECK (rating BETWEEN 1 AND 5),
    comments TEXT,
    feedback_date DATE NOT NULL,
    user_id INT NOT NULL,
    bus_id INT NOT NULL,
    route_id INT NOT NULL,
    FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE,
    FOREIGN KEY (bus_id) REFERENCES buses(bus_id),
    FOREIGN KEY (route_id) REFERENCES routes(route_id) ON DELETE CASCADE
)";

$tables = [
    "Users" => $sql_users,
    "Drivers" => $sql_drivers,
    "Buses" => $sql_buses,
    "Routes" => $sql_routes,
    "Bookings" => $sql_bookings,
    "Feedback" => $sql_feedback
];

foreach ($tables as $name => $sql) {
    if ($conn->query($sql) === TRUE) {
        echo "Table '$name' created successfully.<br>";
    } else {
        die("Error creating table '$name': " . $conn->error);
    }
}

// =================================================================
// 3. SEED DATA
// =================================================================
echo "Seeding data...<br>";

// --- Seed Users ---
// 10 Admins, 10 Agents, 10 Passengers
// Password format: Firstname@2025$
$admins = [
    ['Alice', 'Admin', 'alice@wema.com', '0700000001'],
    ['Bob', 'Builder', 'bob@wema.com', '0700000002'],
    ['Charlie', 'Chaplin', 'charlie@wema.com', '0700000003'],
    ['David', 'Davids', 'david@wema.com', '0700000004'],
    ['Eve', 'Polastri', 'eve@wema.com', '0700000005'],
    ['Frank', 'Ocean', 'frank@wema.com', '0700000006'],
    ['Grace', 'Hopper', 'grace@wema.com', '0700000007'],
    ['Hank', 'Hill', 'hank@wema.com', '0700000008'],
    ['Ivy', 'League', 'ivy@wema.com', '0700000009'],
    ['Jack', 'Sparrow', 'jack@wema.com', '0700000010']
];

$agents = [
    ['Karen', 'Gillan', 'karen@wema.com', '0700000011'],
    ['Leo', 'DiCaprio', 'leo@wema.com', '0700000012'],
    ['Mia', 'Khalifa', 'mia@wema.com', '0700000013'],
    ['Noah', 'Centineo', 'noah@wema.com', '0700000014'],
    ['Olivia', 'Wilde', 'olivia@wema.com', '0700000015'],
    ['Paul', 'Rudd', 'paul@wema.com', '0700000016'],
    ['Quinn', 'Fabray', 'quinn@wema.com', '0700000017'],
    ['Ryan', 'Reynolds', 'ryan@wema.com', '0700000018'],
    ['Sarah', 'Connor', 'sarah@wema.com', '0700000019'],
    ['Tom', 'Hanks', 'tom@wema.com', '0700000020']
];

$passengers = [
    ['Uma', 'Thurman', 'uma@gmail.com', '0711111111'],
    ['Vin', 'Diesel', 'vin@yahoo.com', '0711111112'],
    ['Will', 'Smith', 'will@hotmail.com', '0711111113'],
    ['Xena', 'Warrior', 'xena@gmail.com', '0711111114'],
    ['Yara', 'Shahidi', 'yara@gmail.com', '0711111115'],
    ['Zac', 'Efron', 'zac@yahoo.com', '0711111116'],
    ['Adam', 'Levine', 'adam@gmail.com', '0711111117'],
    ['Bella', 'Hadid', 'bella@hotmail.com', '0711111118'],
    ['Chris', 'Evans', 'chris@gmail.com', '0711111119'],
    ['Drake', 'Graham', 'drake@gmail.com', '0711111120']
];

function seed_users($conn, $users, $role) {
    $stmt = $conn->prepare("INSERT INTO users (first_name, last_name, email, phone_number, password, role) VALUES (?, ?, ?, ?, ?, ?)");
    foreach ($users as $u) {
        $first = $u[0];
        $last = $u[1];
        $email = $u[2];
        $phone = $u[3];
        $pass_plain = ucfirst($first) . "@2025$"; // Format: Firstname@2025$
        
        // --- PASSWORD HASHING EXPLANATION ---
        // We use the PHP built-in function 'password_hash()'.
        // This function uses the BCRYPT algorithm (a strong cryptographic hash).
        // It automatically generates a "salt" for each password, meaning even if 
        // two users have the same password, their hashes in the database will look different.
        // It is a "one-way" function, meaning it's mathematically impossible to 
        // reverse the hash back to the original password.
        // -------------------------------------
        $pass_hash = password_hash($pass_plain, PASSWORD_DEFAULT);
        
        $stmt->bind_param("ssssss", $first, $last, $email, $phone, $pass_hash, $role);
        $stmt->execute();
    }
    $stmt->close();
}

seed_users($conn, $admins, 'ADMIN');
seed_users($conn, $agents, 'AGENT');
seed_users($conn, $passengers, 'PASSENGER');
echo "Users seeded.<br>";

// --- Seed Drivers ---
$drivers = [];
for ($i = 1; $i <= 30; $i++) {
    $drivers[] = [
        "ID" . str_pad($i, 8, '0', STR_PAD_LEFT), // National ID
        "Driver Name $i",
        "0722" . str_pad($i, 6, '0', STR_PAD_LEFT),
        "driver$i@wema.com"
    ];
}
$stmt = $conn->prepare("INSERT INTO drivers (national_id, full_name, phone, email) VALUES (?, ?, ?, ?)");
foreach ($drivers as $d) {
    $stmt->bind_param("ssss", $d[0], $d[1], $d[2], $d[3]);
    $stmt->execute();
}
$stmt->close();
echo "Drivers seeded.<br>";

// --- Seed Buses ---
$buses = [];
for ($i = 1; $i <= 30; $i++) {
    // pattern: KBA 123A
    $reg = "K" . chr(65 + ($i%26)) . chr(65 + (($i+1)%26)) . " " . (100 + $i) . chr(65 + (($i+2)%26));
    $bus_name = "Wema Executive " . $i;
    $capacity = 40;
    $layout = "2x2";
    $driver_id = $i; // One driver per bus roughly
    
    $stmt = $conn->prepare("INSERT INTO buses (reg_no, bus_name, max_passengers, seat_layout, driver_id) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("ssisi", $reg, $bus_name, $capacity, $layout, $driver_id);
    $stmt->execute();
}
echo "Buses seeded.<br>";

// --- Seed Routes (International) ---
// Nairobi-Kampala, Johannesburg-Eritrea, etc. 
$locations = [
    ["Nairobi, Kenya", "Kampala, Uganda", 3500],
    ["Kampala, Uganda", "Nairobi, Kenya", 3500],
    ["Nairobi, Kenya", "Arusha, Tanzania", 2500],
    ["Arusha, Tanzania", "Nairobi, Kenya", 2500],
    ["Johannesburg, SA", "Asmara, Eritrea", 25000], // Long trip
    ["Mombasa, Kenya", "Dar es Salaam, Tanzania", 4000],
    ["Kigali, Rwanda", "Kampala, Uganda", 2000],
    ["Bujumbura, Burundi", "Kigali, Rwanda", 1500],
    ["Addis Ababa, Ethiopia", "Nairobi, Kenya", 6000],
    ["Lusaka, Zambia", "Dar es Salaam, Tanzania", 8000]
];

$stmt = $conn->prepare("INSERT INTO routes (from_location, to_location, departure_date, departure_time, cost, bus_id) VALUES (?, ?, ?, ?, ?, ?)");
$route_ids = [];

// Create 30 routes (repeating locations)
for ($i = 0; $i < 30; $i++) {
    $loc = $locations[$i % count($locations)];
    $from = $loc[0];
    $to = $loc[1];
    $cost = $loc[2];
    
    // Date: next 30 days
    $date = date('Y-m-d', strtotime("+$i days"));
    $time = "08:00:00"; 
    
    $bus_id = ($i % 30) + 1;
    
    $stmt->bind_param("ssssdi", $from, $to, $date, $time, $cost, $bus_id);
    $stmt->execute();
    $route_ids[] = $stmt->insert_id;
}
$stmt->close();
echo "Routes seeded.<br>";

// --- Seed Bookings ---
// Random passengers booking random routes
$stmt = $conn->prepare("INSERT INTO bookings (booking_time, seat_number, booking_status, qr_token, user_id, route_id, bus_id) VALUES (?, ?, ?, ?, ?, ?, ?)");

for ($i = 1; $i <= 30; $i++) {
    $user_id = 20 + ($i % 10) + 1; // Pick from passengers (IDs 21-30)
    $route_id = $route_ids[$i % 30];
    // Need bus_id for that route
    // In our loop above: route i has bus_id (i%30)+1.
    $bus_id = ($i % 30) + 1;
    
    $seat = ($i % 40) + 1; 
    $seat_str = (string)$seat . "A";
    
    $status = 'PAID';
    $token = bin2hex(random_bytes(16));
    $time = date('Y-m-d H:i:s');
    
    $stmt->bind_param("ssssiii", $time, $seat_str, $status, $token, $user_id, $route_id, $bus_id);
    $stmt->execute();
}
$stmt->close();
echo "Bookings seeded.<br>";

// --- Seed Feedback ---
$stmt = $conn->prepare("INSERT INTO feedback (rating, comments, feedback_date, user_id, bus_id, route_id) VALUES (?, ?, ?, ?, ?, ?)");
for ($i = 1; $i <= 30; $i++) {
    $rating = rand(3, 5);
    $comments = "Feedback message number $i - Good service!";
    $date = date('Y-m-d');
    $user_id = 20 + ($i % 10) + 1;
    $bus_id = ($i % 30) + 1;
    $route_id = $route_ids[$i % 30];
    
    $stmt->bind_param("issiii", $rating, $comments, $date, $user_id, $bus_id, $route_id);
    $stmt->execute();
}
$stmt->close();
echo "Feedback seeded.<br>";

$conn->close();

echo "<h3>Database Setup Complete!</h3>";
?>
