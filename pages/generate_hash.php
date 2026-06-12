<?php
// Instead, we use "Hashing".
// 1. WHAT IS HASHING?
// As of now, this is "BCRYPT". Bcrypt is designed to be slow, which is good!
echo password_hash("Mbuki123$", PASSWORD_DEFAULT);
?>
<?php
?>