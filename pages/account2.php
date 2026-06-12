<?php
error_reporting(E_ALL);
ini_set('display_errors', '1');
session_start();
require_once 'db_connection.php';
if (isset($_POST['save'])) {
    $first_name = trim($_POST['first_name']);
    $last_name = trim($_POST['last_name']);
    $email = trim($_POST['email']);
    $phone_number = trim($_POST['phone_number']);
    $password_raw = trim($_POST['password']);
    $sql_check = "SELECT user_id FROM users WHERE email = '$email' OR phone_number = '$phone_number'";
    $res_check = mysqli_query($conn, $sql_check);
    if (mysqli_num_rows($res_check) > 0) {
        die("Error: User already exists!");
    }
    $hashed_password = password_hash($password_raw, PASSWORD_DEFAULT);
    $role = "PASSENGER";
    $sql_insert = "INSERT INTO users (first_name, last_name, email, phone_number, password, role) VALUES ('$first_name', '$last_name', '$email', '$phone_number', '$hashed_password', '$role')";
    if (mysqli_query($conn, $sql_insert)) {
        $new_user_id = mysqli_insert_id($conn);
        $_SESSION['user_id'] = $new_user_id;
        $_SESSION['role'] = $role;
        $_SESSION['name'] = $first_name . " " . $last_name;
        echo "<script>alert('Welcome!'); window.location.href='dashboard.php';</script>";
        exit();
    } else {
        die("Signup failed!");
    }
}
if (isset($_POST['login'])) {
    $email = trim($_POST['email']);
    $password_input = trim($_POST['password']);
    $sql_login = "SELECT user_id, password, role, first_name, last_name FROM users WHERE email = '$email'";
    $res_login = mysqli_query($conn, $sql_login);
    $row=mysqli_fetch_assoc($res_login);
    $user_id=$row['user_id']; $stored_password=$row['password']; $role=$row['role']; $f_name=$row['first_name']; $l_name=$row['last_name'];
    if (mysqli_num_rows($res_login) > 0) {
        if (password_verify($password_input, $stored_password)) {
            $_SESSION['user_id'] = $user_id;
            $_SESSION['role'] = $role;
            $_SESSION['name'] = $f_name . " " . $l_name;
            if($role == 'ADMIN') { header("Location: admin_dashboard.php"); }
            elseif ($role == 'AGENT') { header("Location: agent_dashboard.php"); }
            else { header("Location: dashboard.php"); }
            exit();
        } else {
            echo "<script>alert('Wrong password!'); window.location.href='login.html';</script>";
            exit();
        }
    } else {
        echo "<script>alert('Account not found!'); window.location.href='login.html';</script>";
        exit();
    }
}
mysqli_close($conn);
?>