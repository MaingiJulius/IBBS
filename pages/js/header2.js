// ==========================================================================
// HEADER2.JS (Dashboard Navigation Loader)
// ==========================================================================
// Purpose: This script manages the navigation bar for the internal "Customer/Staff Portal" (Dashboards, Booking, etc).
// Role: It provides deep links to internal logic pages where session-specific data is active.
// ==========================================================================

// document.addEventListener: Monitors the state of the webpage.
// 'DOMContentLoaded': Fires when the initial HTML document has been completely loaded and parsed.
document.addEventListener('DOMContentLoaded', function () {

  // const header: Stores the HTML code for the complex dashboard navigation bar.
  // We utilize a standard div-based structure to ensure consistent layout across PHP-powered pages.
  const header = `
      <!-- Banner Section with Company Name - Branding persistence -->
      <div id="banner"> <!-- Div ID used for global typography and background purple color application. -->
        <h1>Wema Travellers</h1> <!-- Site-wide header title. -->
      </div> <!-- End of banner div. -->
  
      <!-- Navigation Links Container - Functional menu for authenticated users -->
      <div id="nav-links"> <!-- Flexbox-driven bar for layout management. -->
        
        <!-- Standard Links (Centered Group) - Primary portal destinations -->
        <div style="display: flex; gap: 20px;"> <!-- Flexbox container with a fixed 20px gutter between links. -->
            <a href="home.php">Home</a> <!-- Link to the landing dashboard. -->
            <a href="dashboard.php">Dashboard</a> <!-- Link to the main user/admin control panel. -->
            <a href="profile.php">Profile</a> <!-- Link to view/edit personal account details. -->
            <a href="feedback.php">Feedback</a> <!-- Link for submitting user experience ratings. -->
        </div> <!-- Closes the flex link-group. -->
        
        <!-- SignOut Link (Absolute Right) - Safety exit specifically positioned for UX clarity -->
        <a href="logout.php" style="position: absolute; right: 20px;">SignOut</a> <!-- Absolute positioning pins this specifically to the right edge. -->
      </div> <!-- Closes the navigation bar. -->
    `; // Ends the header definition.

  // insertAdjacentHTML: Integrates the string directly into the browser's memory of the page content.
  // 'afterbegin': Places it at the top of the body, before any other dashboard content.
  document.body.insertAdjacentHTML('afterbegin', header);
}); // Ends the script.
