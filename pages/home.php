<?php 
// <?php (opening tag) tells the server to start interpreting the code as PHP.

/**
 * HOME.PHP
 */

// session_start (session start) starts the user session.
session_start(); 
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Wema Travellers - Home</title>
  <link rel="stylesheet" href="css/style.css">
  <link rel="stylesheet" href="css/main.css">

  <style>
    .hero {
      background-image: linear-gradient(rgba(0, 0, 0, 0.5), rgba(0, 0, 0, 0.5)), url('img/wema_bus_sunset.png');
      height: 60vh;
      background-position: center;
      background-size: cover;
      display: flex;
      justify-content: center;
      align-items: center;
      text-align: center;
      color: white;
      margin-bottom: 40px;
    }
    .hero h1 { font-size: 3.5rem; margin-bottom: 20px; }
    .hero p { font-size: 1.5rem; max-width: 800px; margin: 0 auto; opacity: 0.9; }
    .section-container { max-width: 1200px; margin: 0 auto; padding: 40px 20px; }
    h2.section-title { text-align: center; color: #333; font-size: 2.5rem; margin-bottom: 40px; }
    .vmg-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 30px; margin-bottom: 60px; }
    .vmg-card { background: white; padding: 30px; border-radius: 12px; box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1); text-align: center; border-top: 5px solid var(--purple); }
    .countries-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(150px, 1fr)); gap: 20px; text-align: center; }
    .country-pill { background-color: #f0f0f0; padding: 15px; border-radius: 50px; font-weight: 600; }
  </style>
</head>

<body class="<?= isset($_SESSION['role']) ? strtolower($_SESSION['role']) . '-role' : '' ?>">
  <script src="js/header2.js"></script>
  <div style="height: 100px;"></div>

  <div class="hero">
    <div class="hero-content">
      <?php if(isset($_SESSION['name'])): ?>
        <h1>Welcome, <?= htmlspecialchars($_SESSION['name']) ?>!</h1>
        <p>Premium International Travel for the Modern Explorer</p>
      <?php else: ?>
        <h1>Welcome to Wema Travellers</h1>
        <p>Your Trusted Partner for Cross-Border Bus Journeys</p>
      <?php endif; ?>
    </div>
  </div>

  <?php if(isset($_SESSION['name']) && $_SESSION['role'] === 'PASSENGER'): ?>
    <div class="section-container" style="padding-bottom: 0;">
        <div style="background: white; border: 2px solid var(--purple); color: var(--purple); padding: 30px; border-radius: 12px; text-align: center;">
            <h3>Are you ready for your next adventure?</h3>
            <p style="margin-top: 15px;">
                <a href="book.php" class="button regular-button pink-background" style="text-decoration:none; padding: 15px 40px; font-size: 1.2em;">Book Your Next Trip Now</a>
            </p>
        </div>
    </div>
  <?php endif; ?>

  <div class="section-container">
    <div style="text-align: center; font-size: 1.25rem; color: #555; max-width: 950px; margin: 0 auto 60px; line-height: 1.8;">
      <p>
        Wema Travellers is a premier international transport company dedicated to connecting East and South Africa.
        With a modern fleet of luxury buses and a commitment to punctuality, safety, and comfort, we bridge the gap between nations.
      </p>
    </div>

    <div class="vmg-grid">
      <div class="vmg-card"><h3>Our Vision</h3><p>To be the leading innovator in safe transport across Africa.</p></div>
      <div class="vmg-card"><h3>Our Mission</h3><p>To deliver world-class travel experiences via superior technology.</p></div>
      <div class="vmg-card"><h3>Our Promise</h3><p>Reliability. Comfort. Integrity. Every single mile.</p></div>
    </div>

    <h2 class="section-title">Nations We Connect</h2>
    <div class="countries-grid">
      <div class="country-pill">Kenya</div>
      <div class="country-pill">Uganda</div>
      <div class="country-pill">Tanzania</div>
      <div class="country-pill">Rwanda</div>
      <div class="country-pill">South Africa</div>
      <div class="country-pill">Ethiopia</div>
      <div class="country-pill">Zambia</div>
      <div class="country-pill">Malawi</div>
      <div class="country-pill">Zimbabwe</div>
      <div class="country-pill">Burundi</div>
    </div>
  </div>

  <div style="height: 50px;"></div>
  <script src="js/footer.js"></script>
</body>
</html>
