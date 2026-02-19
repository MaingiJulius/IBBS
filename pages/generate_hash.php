<?php
// =================================================================
// PASSWORD ENCRYPTION EXPLANATION
// =================================================================
// In this project, we NEVER store passwords as plain text (e.g., "password123").
// If we did, a hacker who stole our database would see everyone's passwords.
// Instead, we use "Hashing".
//
// 1. WHAT IS HASHING?
//    Hashing converts a password into a scrambled string of characters.
//    It is a "one-way" function, meaning you can turn a password into a hash,
//    but you CANNOT turn a hash back into the password.
//
// 2. THE FUNCTION: password_hash($password, PASSWORD_DEFAULT)
//    - $password: The actual text the user typed.
//    - PASSWORD_DEFAULT: This tells PHP to use the current industry-standard algorithm.
//      As of now, this is "BCRYPT". Bcrypt is designed to be slow, which is good!
//      It prevents hackers from using fast computers to guess billions of passwords a second.
//
// 3. THE OUTPUT (The Hash):
//    It looks like this: $2y$10$abcdefghijklmnopqrstuv...
//    - $2y$: Identifies it as a Bcrypt hash.
//    - $10$: The "Cost Factor". It controls how hard the computer has to work.
//    - The rest: The salt (random data) + the hashed password.
//
// 4. VERIFICATION:
//    When a user logs in, we don't decrypt the database password (because we can't!).
//    Instead, we use password_verify($input_password, $stored_hash).
//    It hashes the input using the same logic and compares it to the stored hash.
// =================================================================

echo password_hash("Mbuki123$", PASSWORD_DEFAULT);
?>



<?php
//$raw = "Julius@123";
//$hash = '$2y$10$mDi2K3zK.XranrWm92A.kudpx53Co4IEfiihNqXMJ/.OGck0BlfJe';

//if (password_verify($raw, $hash)) {
 //   echo "Password is correct!";
//} else {
 //   echo "Password is incorrect!";
//}
?>
