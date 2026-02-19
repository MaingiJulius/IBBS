<?php 
// This is the starting point for everyone who visits the Wema Travellers website. 
// It's like the front door of our project!

// We start the session so we can see if anyone is already logged in.
// If they are, we can greet them by name.
session_start(); 
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Wema Travellers - Home</title>

  <!-- We link our CSS files to style the page layout, colors, and fonts -->
  <link rel="stylesheet" href="css/style.css">
  <link rel="stylesheet" href="css/main.css">

  <style>
    /* -----------------------------------------------------------------
       CUSTOM CSS FOR THE HOME PAGE EXPERIENCE
    ----------------------------------------------------------------- */

    /* The Hero Section is the big, beautiful banner at the top of the page.
       It uses an image of a bus and a dark gradient to make the text pop! */
    .hero {
      background-image: linear-gradient(rgba(0, 0, 0, 0.5), rgba(0, 0, 0, 0.5)), url('img/wema_bus_sunset.png');
      height: 60vh; /* Takes up 60% of the screen height */
      background-position: center;
      background-size: cover;
      display: flex;
      justify-content: center;
      align-items: center;
      text-align: center;
      color: white;
      margin-bottom: 40px;
    }

    /* Styling for the large headline ("Welcome...") */
    .hero h1 {
      font-size: 3.5rem;
      margin-bottom: 20px;
      text-shadow: 2px 2px 10px rgba(0, 0, 0, 0.5); /* Adds a subtle shadow for readability */
    }

    /* Sub-text under the main headline */
    .hero p {
      font-size: 1.5rem;
      max-width: 800px;
      margin: 0 auto;
      opacity: 0.9;
    }

    /* Main content container with a maximum width to keep things tidy on large screens */
    .section-container {
      max-width: 1200px;
      margin: 0 auto;
      padding: 40px 20px;
    }

    /* Styled titles for each section like "Our Vision" */
    h2.section-title {
      text-align: center;
      color: #333;
      font-size: 2.5rem;
      margin-bottom: 40px;
    }

    /* The pink line under titles - using the ::after pseudo-element */
    h2.section-title::after {
      content: '';
      display: block;
      width: 80px;
      height: 4px;
      background-color: var(--pink);
      margin: 10px auto 0;
      border-radius: 2px;
    }

    /* GRID LAYOUT: Organizes Vision, Mission, and Promise into 3 columns */
    .vmg-grid {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
      gap: 30px;
      margin-bottom: 60px;
    }

    /* Card styling for the Vision/Mission items */
    .vmg-card {
      background: white;
      padding: 30px;
      border-radius: 12px;
      box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
      text-align: center;
      transition: transform 0.3s ease; /* Smooth hover animation */
      border-top: 5px solid var(--purple);
    }

    /* Lift the card up slightly when the mouse hovers over it */
    .vmg-card:hover {
      transform: translateY(-10px);
    }

    .vmg-card h3 {
      color: var(--purple);
      font-size: 1.8rem;
      margin-bottom: 15px;
    }

    /* GRID FOR COUNTRIES: Shows all the destinations we cover */
    .countries-grid {
      display: grid;
      grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
      gap: 20px;
      text-align: center;
    }

    /* Styling for the country names in pill-shaped boxes */
    .country-pill {
      background-color: #f0f0f0;
      padding: 15px;
      border-radius: 50px;
      font-weight: 600;
      color: #444;
      transition: all 0.3s ease;
    }

    /* Change color to dark purple when hovered */
    .country-pill:hover {
      background-color: #4a1a4a !important;
      color: white;
      cursor: default;
    }

    /* Intro paragraph styling */
    .intro-text {
      text-align: center;
      font-size: 1.25rem;
      color: #555;
      max-width: 950px;
      margin: 0 auto 60px;
      line-height: 1.8;
    }
  </style>
</head>

<body>

  <!-- Load the global navigation menu -->
  <script src="js/header2.js"></script>
  
  <!-- Add top padding so content doesn't get hidden under the fixed header -->
  <div style="height: 100px;"></div>

  <!-- HERO SECTION: The welcoming visual banner -->
  <div class="hero">
    <div class="hero-content">
      <?php 
      // DYNAMIC GREETING:
      // If the user is logged in, say hello to them by name!
      if(isset($_SESSION['name'])): 
      ?>
        <h1>Welcome, <?= htmlspecialchars($_SESSION['name']) ?>!</h1>
        <p>Premium International Travel for the Modern Explorer</p>
      <?php else: ?>
        <!-- If it's a guest visitor, show a general welcome -->
        <h1>Welcome to Wema Travellers</h1>
        <p>Your Trusted Partner for Cross-Border Bus Journeys</p>
      <?php endif; ?>
    </div>
  </div>

  <!-- PASSENGER CALL TO ACTION:
       If a passenger is logged in, show them a direct link to book a trip. -->
  <?php if(isset($_SESSION['name']) && $_SESSION['role'] === 'PASSENGER'): ?>
    <div class="section-container" style="padding-bottom: 0;">
        <div class="welcome-banner" style="background: white; border: 2px solid var(--purple); color: var(--purple); padding: 30px; border-radius: 12px; text-align: center; box-shadow: 0 4px 20px rgba(0,0,0,0.08);">
            <h3>Are you ready for your next adventure?</h3>
            <p style="margin-top: 15px;">
                <!-- Shortcut link to the booking page -->
                <a href="book.php" class="button regular-button pink-background" style="text-decoration:none; padding: 15px 40px; font-size: 1.2em;">Book Your Next Trip Now</a>
            </p>
        </div>
    </div>
  <?php endif; ?>

  <div class="section-container">

    <!-- ABOUT SECTION: A brief introduction to our company -->
    <div class="intro-text">
      <p>
        Wema Travellers is a premier international transport company dedicated to connecting East and South Africa.
        With a modern fleet of luxury buses and a commitment to punctuality, safety, and comfort, we bridge the gap
        between nations.
      </p>
    </div>

    <!-- CORE VALUES SECTION: Vision, Mission, and Promise -->
    <div class="vmg-grid">
      <!-- Card 1: Vision -->
      <div class="vmg-card">
        <h3>Our Vision</h3>
        <p>To be the leading innovator in safe, comfortable, and sustainable road transport across the African continent.</p>
      </div>
      <!-- Card 2: Mission -->
      <div class="vmg-card">
        <h3>Our Mission</h3>
        <p>To deliver world-class travel experiences by combining superior technology with well-trained, professional staff.</p>
      </div>
      <!-- Card 3: Promise -->
      <div class="vmg-card">
        <h3>Our Promise</h3>
        <p>Reliability. Comfort. Integrity. We promise a smooth journey from the moment you book until you reach your destination.</p>
      </div>
    </div>

    <!-- DESTINATIONS SECTION: Countries we travel to -->
    <h2 class="section-title">Nations We Connect</h2>
    <div class="countries-grid">
      <!-- List of destination countries -->
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

  <!-- Space and Footer section -->
  <div style="height: 50px;"></div>
  <script src="js/footer.js"></script>

</body>

</html>
