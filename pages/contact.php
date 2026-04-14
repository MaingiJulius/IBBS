<?php
/**
 * CONTACT.PHP
 * Purpose: Provides commuters with official contact channels for Wema Travellers.
 */
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Contact Us - Wema Travellers</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/main.css">
    <link rel="stylesheet" href="css/style.css">
    <style>
        .contact-container { max-width: 800px; margin: 60px auto; padding: 40px; background: white; border-radius: 20px; box-shadow: 0 20px 50px rgba(0,0,0,0.05); text-align: center; }
        .contact-methods { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 30px; margin-top: 40px; }
        .method-card { padding: 30px; border-radius: 15px; background: #f8fafc; border: 1px solid #e2e8f0; transition: transform 0.3s ease; }
        .method-card:hover { transform: translateY(-5px); border-color: var(--purple); }
        .method-card i { font-size: 2rem; color: var(--purple); margin-bottom: 15px; display: block; }
        .method-card h3 { margin-bottom: 10px; color: #1e293b; }
        .method-card p { color: #64748b; font-size: 0.95rem; }
    </style>
</head>
<body class="<?= strtolower($_SESSION['role'] ?? 'guest') ?>-role">
    <script src="js/header1.js"></script>
    <div style="height: 100px;"></div>

    <div class="contact-container">
        <h1 style="color: var(--purple); font-size: 2.5rem; margin-bottom: 10px;">Get In Touch</h1>
        <p style="color: #64748b; font-size: 1.1rem;">We are here to assist you with your travels across East Africa.</p>

        <div class="contact-methods">
            <div class="method-card">
                <h3>📞 Call Center</h3>
                <p>Main Office: +254 700 000 000</p>
                <p>Support: +254 711 111 111</p>
            </div>
            <div class="method-card">
                <h3>📧 Email Us</h3>
                <p>info@wematravellers.com</p>
                <p>support@wematravellers.com</p>
            </div>
            <div class="method-card">
                <h3>📍 Headquarters</h3>
                <p>Wema Plaza, 4th Floor</p>
                <p>Nairobi, Kenya</p>
            </div>
        </div>

        <div style="margin-top: 50px; border-top: 1px solid #eee; padding-top: 30px;">
            <h3>Operating Hours</h3>
            <p style="color: #64748b;">Monday - Sunday: 5:00 AM - 11:00 PM</p>
        </div>
    </div>

    <div style="height: 100px;"></div>
    <script src="js/footer.js"></script>
</body>
</html>
