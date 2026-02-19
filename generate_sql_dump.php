<?php
// Script to generate a SQL dump file for the IBBS_PROTOTYPE
// Outputs valid SQL commands to create DB, Tables, and Insert Data.

header('Content-Type: text/plain');

// Helper to sanitize strings for SQL
function sql_str($str) {
    return "'" . addslashes($str) . "'"; // Simple escaping for this dump
}

// Output file
$outputFile = 'db_schema_and_data.sql';
$handle = fopen($outputFile, 'w') or die('Cannot open file:  '.$outputFile);

function write_sql($handle, $text) {
    fwrite($handle, $text);
}

write_sql($handle, "-- =================================================================\n");
write_sql($handle, "-- IBBS PROTOTYPE DATABASE DUMP\n");
write_sql($handle, "-- IBBS_PROTOTYPE");
write_sql($handle, "-- =================================================================\n\n");

write_sql($handle, "DROP DATABASE IF EXISTS IBBS_PROTOTYPE;\n");
write_sql($handle, "CREATE DATABASE IBBS_PROTOTYPE;\n");
write_sql($handle, "USE IBBS_PROTOTYPE;\n\n");

// --- TABLES ---

write_sql($handle, "-- 1. USERS TABLE\n");
write_sql($handle, "CREATE TABLE users (
    user_id INT(11) NOT NULL AUTO_INCREMENT,
    first_name VARCHAR(50) NOT NULL,
    last_name VARCHAR(50) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    phone_number VARCHAR(15) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role ENUM('PASSENGER','ADMIN','AGENT') DEFAULT 'PASSENGER',
    created_at TIMESTAMP NOT NULL DEFAULT current_timestamp(),
    PRIMARY KEY (user_id)
);\n\n");

write_sql($handle, "-- 2. DRIVERS TABLE\n");
write_sql($handle, "CREATE TABLE drivers (
    driver_id INT(11) NOT NULL AUTO_INCREMENT,
    national_id VARCHAR(20) NOT NULL UNIQUE,
    full_name VARCHAR(100) NOT NULL,
    phone VARCHAR(15) NOT NULL UNIQUE,
    email VARCHAR(100) NOT NULL UNIQUE,
    PRIMARY KEY (driver_id)
);\n\n");

write_sql($handle, "-- 3. BUSES TABLE\n");
write_sql($handle, "CREATE TABLE buses (
    bus_id INT(11) NOT NULL AUTO_INCREMENT,
    reg_no VARCHAR(20) NOT NULL UNIQUE,
    bus_name VARCHAR(50) DEFAULT NULL,
    max_passengers INT(11) NOT NULL,
    seat_layout VARCHAR(20) NOT NULL,
    driver_id INT(11) DEFAULT NULL,
    PRIMARY KEY (bus_id),
    FOREIGN KEY (driver_id) REFERENCES drivers (driver_id) ON DELETE SET NULL
);\n\n");

write_sql($handle, "-- 4. ROUTES TABLE\n");
write_sql($handle, "CREATE TABLE routes (
    route_id INT(11) NOT NULL AUTO_INCREMENT,
    from_location VARCHAR(100) NOT NULL,
    to_location VARCHAR(100) NOT NULL,
    departure_date DATE NOT NULL,
    departure_time TIME NOT NULL,
    cost DECIMAL(10,2) NOT NULL,
    bus_id INT(11) NOT NULL,
    status ENUM('SCHEDULED','COMPLETED','CANCELLED') DEFAULT 'SCHEDULED',
    PRIMARY KEY (route_id),
    FOREIGN KEY (bus_id) REFERENCES buses (bus_id)
);\n\n");

write_sql($handle, "-- 5. BOOKINGS TABLE\n");
write_sql($handle, "CREATE TABLE bookings (
    booking_id INT(11) NOT NULL AUTO_INCREMENT,
    booking_time DATETIME NOT NULL DEFAULT current_timestamp(),
    seat_number VARCHAR(10) NOT NULL,
    booking_status ENUM('CONFIRMED','CANCELLED','PAID','CHECKED_IN') DEFAULT 'PAID',
    qr_token VARCHAR(255) DEFAULT NULL,
    user_id INT(11) NOT NULL,
    route_id INT(11) NOT NULL,
    bus_id INT(11) NOT NULL,
    passenger_name VARCHAR(255) NOT NULL DEFAULT '',
    passenger_age INT(11) NOT NULL DEFAULT 0,
    passenger_id_number VARCHAR(50) NOT NULL DEFAULT '',
    PRIMARY KEY (booking_id),
    FOREIGN KEY (user_id) REFERENCES users (user_id),
    FOREIGN KEY (route_id) REFERENCES routes (route_id) ON DELETE CASCADE,
    FOREIGN KEY (bus_id) REFERENCES buses (bus_id),
    UNIQUE KEY unique_booking (route_id, seat_number)
);\n\n");

write_sql($handle, "-- 6. FEEDBACK TABLE\n");
write_sql($handle, "CREATE TABLE feedback (
    feedback_id INT(11) NOT NULL AUTO_INCREMENT,
    rating INT(11) DEFAULT 5,
    comments TEXT NOT NULL,
    feedback_date DATE NOT NULL,
    user_id INT(11) NOT NULL,
    bus_id INT(11) NOT NULL,
    route_id INT(11) NOT NULL,
    submitted_at TIMESTAMP NOT NULL DEFAULT current_timestamp(),
    PRIMARY KEY (feedback_id),
    FOREIGN KEY (user_id) REFERENCES users (user_id) ON DELETE CASCADE,
    FOREIGN KEY (bus_id) REFERENCES buses (bus_id),
    FOREIGN KEY (route_id) REFERENCES routes (route_id) ON DELETE CASCADE
);\n\n");

write_sql($handle, "-- =================================================================\n");
write_sql($handle, "-- DATA INSERTION\n");
write_sql($handle, "-- =================================================================\n\n");

// --- INSERT USERS ---
$admins = [
    ['Alice', 'Kamau'], ['Bob', 'Ochieng'], ['Charlie', 'Kipkorir'], 
    ['David', 'Maina'], ['Eve', 'Wanjiku'], ['Frank', 'Otieno'], 
    ['Grace', 'Achieng'], ['Hank', 'Musyoka'], ['Ivy', 'Njeri'], ['Jack', 'Mutua']
];

$agents = [
    ['Karen', 'Njoroge'], ['Leo', 'Mwangi'], ['Mia', 'Odhiambo'], 
    ['Noah', 'Kimani'], ['Olivia', 'Chebet'], ['Paul', 'Kariuki'], 
    ['Quinn', 'Awere'], ['Ryan', 'Omondi'], ['Sarah', 'Wambui'], ['Tom', 'Ndegwa']
];

$passengers = [
    ['Uma', 'Abdi'], ['Vin', 'Ndlovu'], ['Will', 'Chamele'], 
    ['Xena', 'Tesfaye'], ['Yara', 'Mensah'], ['Zac', 'Diallo'], 
    ['Adam', 'Keita'], ['Bella', 'Sow'], ['Chris', 'Traore'], ['Drake', 'Kone']
];

function print_inserts($handle, $users, $role) {
    write_sql($handle, "-- Inserting $role Users\n");
    $values = [];
    foreach ($users as $i => $u) {
        $first = $u[0];
        $last = $u[1];
        // Requirement: firstname@gmail.com
        $email = strtolower($first) . ($i + 1) . "@gmail.com"; 
        
        $phone = "07" . rand(10, 99) . rand(100000, 999999); 
        
        if ($role == 'ADMIN') $phone = "07000000"  . str_pad($i, 2, '0', STR_PAD_LEFT);
        if ($role == 'AGENT') $phone = "07110000"  . str_pad($i, 2, '0', STR_PAD_LEFT);
        if ($role == 'PASSENGER') $phone = "07220000"  . str_pad($i, 2, '0', STR_PAD_LEFT);

        $pass_plain = ucfirst($first) . "@2025$"; 
        $pass_hash = password_hash($pass_plain, PASSWORD_DEFAULT);
        
        $values[] = "('$first', '$last', '$email', '$phone', '$pass_hash', '$role')";
    }
    
    if (!empty($values)) {
        write_sql($handle, "INSERT INTO users (first_name, last_name, email, phone_number, password, role) VALUES \n" . implode(",\n", $values) . ";\n");
    }
    write_sql($handle, "\n");
}

print_inserts($handle, $admins, 'ADMIN');
print_inserts($handle, $agents, 'AGENT');
print_inserts($handle, $passengers, 'PASSENGER');


write_sql($handle, "-- --- INSERT DRIVERS ---\n");
$driver_names = [
    "John Mwangi", "Samuel Okello", "David Mengistu", "Mohammed Hassan", "Peter Kamau",
    "James Omondi", "Benson Kiprop", "Charles Odhiambo", "Joseph Kariuki", "Thomas Njoroge",
    "Emmanuel Abebe", "Isaac Tesfaye", "Gabriel Selassie", "Michael Afewerki", "Daniel Tekle",
    "Richard Mwanza", "Patrick Mutua", "Stephen Musyoka", "George Otieno", "Edward Wanyama",
    "Brian Kibet", "Kevin Cheruiyot", "Dennis Kemboi", "Alex Rotich", "Felix Langat",
    "Victor Ochieng", "Collins Okeyo", "Fredrick Owino", "Walter Aketch", "Moses Odongo"
];

$values = [];
for ($i = 0; $i < 30; $i++) {
    $nat_id = "ID" . str_pad($i+1, 8, '0', STR_PAD_LEFT);
    $name = $driver_names[$i];
    $phone = "0733" . str_pad($i, 6, '0', STR_PAD_LEFT);
    $email = str_replace(' ', '.', strtolower($name)) . "@gmail.com";
    $values[] = "('$nat_id', '$name', '$phone', '$email')";
}
if (!empty($values)) {
    write_sql($handle, "INSERT INTO drivers (national_id, full_name, phone, email) VALUES \n" . implode(",\n", $values) . ";\n");
}
write_sql($handle, "\n");

write_sql($handle, "-- --- INSERT BUSES ---\n");
$countries = ['K', 'K', 'K', 'K', 'U', 'U', 'T', 'T', 'R', 'S']; // Weighted to Kenya
$values = [];
for ($i = 1; $i <= 30; $i++) {
    // Generate regional plates
    $c = $countries[($i-1) % count($countries)];
    if ($c == 'K') { // Kenya: KBA 123A
        $reg = "K" . chr(rand(65, 90)) . chr(rand(65, 90)) . " " . rand(100, 999) . chr(rand(65, 90));
    } elseif ($c == 'U') { // Uganda: UBA 123A
        $reg = "U" . chr(rand(65, 90)) . chr(rand(65, 90)) . " " . rand(100, 999) . chr(rand(65, 90));
    } elseif ($c == 'T') { // Tanzania: T 123 ABC
        $reg = "T " . rand(100, 999) . " " . chr(rand(65, 90)) . chr(rand(65, 90)) . chr(rand(65, 90));
    } elseif ($c == 'R') { // Rwanda: RAA 123 A
        $reg = "R" . chr(rand(65, 90)) . chr(rand(65, 90)) . " " . rand(100, 999) . " " . chr(rand(65, 90));
    } else { // SA: ABC 123 GP
        $reg = chr(rand(65, 90)) . chr(rand(65, 90)) . chr(rand(65, 90)) . " " . rand(100, 999) . " GP";
    }

    $bus_name = "Wema Executive " . $i;
    $capacity = 40;
    $layout = "2x2";
    $driver_id = $i; 
    $values[] = "('$reg', '$bus_name', $capacity, '$layout', $driver_id)";
}
if (!empty($values)) {
    write_sql($handle, "INSERT INTO buses (reg_no, bus_name, max_passengers, seat_layout, driver_id) VALUES \n" . implode(",\n", $values) . ";\n");
}
write_sql($handle, "\n");

write_sql($handle, "-- --- INSERT ROUTES (International) ---\n");
$locations = [
    ["Nairobi, Kenya", "Kampala, Uganda", 3500],
    ["Kampala, Uganda", "Nairobi, Kenya", 3500],
    ["Nairobi, Kenya", "Arusha, Tanzania", 2500],
    ["Arusha, Tanzania", "Nairobi, Kenya", 2500],
    ["Johannesburg, SA", "Asmara, Eritrea", 25000],
    ["Mombasa, Kenya", "Dar es Salaam, Tanzania", 4000],
    ["Kigali, Rwanda", "Kampala, Uganda", 2000],
    ["Bujumbura, Burundi", "Kigali, Rwanda", 1500],
    ["Addis Ababa, Ethiopia", "Nairobi, Kenya", 6000],
    ["Lusaka, Zambia", "Dar es Salaam, Tanzania", 8000]
];

$route_ids = [];
$values = [];
for ($i = 0; $i < 30; $i++) {
    $loc = $locations[$i % count($locations)];
    $from = $loc[0];
    $to = $loc[1];
    $cost = $loc[2];
    
    // Random date in 2026
    $month = rand(1, 12);
    $day = rand(1, 28);
    $date = sprintf("2026-%02d-%02d", $month, $day);
    
    // Random time
    $hour = rand(6, 20); // 6 AM to 8 PM
    $minute = rand(0, 5) * 10;
    $time = sprintf("%02d:%02d:00", $hour, $minute);

    $bus_id = ($i % 30) + 1;
    $route_ids[] = $i + 1;
    
    $values[] = "('$from', '$to', '$date', '$time', $cost, $bus_id)";
}
if (!empty($values)) {
    write_sql($handle, "INSERT INTO routes (from_location, to_location, departure_date, departure_time, cost, bus_id) VALUES \n" . implode(",\n", $values) . ";\n");
}
write_sql($handle, "\n");

write_sql($handle, "-- --- INSERT BOOKINGS ---\n");
$values = [];
for ($i = 1; $i <= 30; $i++) {
    $user_id = 20 + ($i % 10) + 1; 
    $route_id = $route_ids[$i % 30]; 
    $bus_id = ($i % 30) + 1;
    $seat = ($i % 40) + 1; 
    $seat_str = (string)$seat . "A";
    $status = 'PAID';
    $token = bin2hex(random_bytes(16));
    
    // Random booking time in 2026 (before departure technically, but random 2026 is fine)
    $month = rand(1, 12);
    $day = rand(1, 28);
    $hour = rand(8, 20);
    $time = sprintf("2026-%02d-%02d %02d:00:00", $month, $day, $hour);

    $values[] = "('$time', '$seat_str', '$status', '$token', $user_id, $route_id, $bus_id)";
}
if (!empty($values)) {
    write_sql($handle, "INSERT INTO bookings (booking_time, seat_number, booking_status, qr_token, user_id, route_id, bus_id) VALUES \n" . implode(",\n", $values) . ";\n");
}
write_sql($handle, "\n");

write_sql($handle, "-- --- INSERT FEEDBACK ---\n");
$comments_list = [
    "Great service, very comfortable bus.",
    "The journey was smooth and the driver was professional.",
    "Bus was a bit late but the ride was okay.",
    "Excellent staff and safe driving. Will book again.",
    "The AC was too cold, otherwise a good trip.",
    "Very clean bus and punctual departure.",
    "Seats were comfortable but the legroom could be better.",
    "Enjoyed the free Wi-Fi, it was surprisingly fast.",
    "Too many stops along the way made the trip longer.",
    "Best travel experience I have had in a long time.",
    "The driver drove carefully, I felt safe throughout.",
    "Booking process was easy and the bus was on time.",
    "The snacks provided were a nice touch.",
    "Charging ports were not working, please fix.",
    "Arrived at the destination ahead of schedule!",
    "Customer service was helpful when I needed to change my seat.",
    "The bus was noisy, could not sleep well.",
    "Affordable prices for such a long international trip.",
    "Easy border crossing assistance from the crew.",
    "Love the new buses, very modern and fresh."
];

$values = [];
for ($i = 1; $i <= 30; $i++) {
    $rating = rand(3, 5);
    // Pick a random comment
    $msg = $comments_list[array_rand($comments_list)];
    $comments = sql_str($msg); // Use the helper to be safe, though array is safe enough here. Actually helper adds quotes, so just $msg
    // helper adds quotes, but we are building a string value list.
    // Let's just manually escape single quotes if any (none in my list) and wrap in quotes.
    $comments = "'" . addslashes($msg) . "'";

    // Random feedback date in 2026
    $month = rand(1, 12);
    $day = rand(1, 28);
    $date = sprintf("2026-%02d-%02d", $month, $day);

    $user_id = 20 + ($i % 10) + 1;
    $bus_id = ($i % 30) + 1;
    $route_id = $route_ids[$i % 30];
    
    $values[] = "($rating, $comments, '$date', $user_id, $bus_id, $route_id)";
}
if (!empty($values)) {
    write_sql($handle, "INSERT INTO feedback (rating, comments, feedback_date, user_id, bus_id, route_id) VALUES \n" . implode(",\n", $values) . ";\n");
}
write_sql($handle, "\n");

fclose($handle);
?>
