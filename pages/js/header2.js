// ==========================================================================
// HEADER2.JS (Dashboard Navigation Loader)
// ==========================================================================
// This script injects the main application navigation bar.
// It is used on pages where the user is already logged in (Dashboard, Profile, etc.).
// ==========================================================================

// Listen for the 'DOMContentLoaded' event to ensure HTML is ready.
document.addEventListener('DOMContentLoaded', function () {

  // Define the HTML content for the dashboard header.
  // We remove inline styles and rely on main.css for the colors.
  const header = `
      <!-- Banner Section with Company Name -->
      <div id="banner">
        <h1>Wema Travellers</h1>
      </div>
  
      <!-- Navigation Links Container -->
      <div id="nav-links">
        
        <!-- Standard Links (Centered Group) -->
        <div style="display: flex; gap: 20px;">
            <a href="home.php">Home</a>
            <a href="dashboard.php">Dashboard</a>
            <a href="profile.php">Profile</a>
            <a href="feedback.php">Feedback</a>
        </div>
        
        <!-- SignOut Link (Absolute Right) -->
        <a href="logout.php" style="position: absolute; right: 20px;">SignOut</a> 
      </div>
    `;

  // Insert the constructed header HTML at the beginning of the document body.
  document.body.insertAdjacentHTML('afterbegin', header);
});