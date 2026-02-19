// ==========================================================================
// HEADER.JS (Primary Navigation Loader)
// ==========================================================================
// This script automatically injects the main navigation bar into the top of the webpage.
// It ensures that every page has a consistent look and feel without duplicating HTML code.
// ==========================================================================

// Wait for the HTML document to be fully loaded before running the script.
document.addEventListener('DOMContentLoaded', function () {

  // Create a string variable called 'header' that contains all the HTML for the menu.
  const header = `
      <!-- TOP BANNER Section: Holds the company logo/title -->
      <div id="banner">
        <h1>Wema Travelers</h1>
      </div>
  
      <!-- NAVIGATION LINKS Section: Holds the clickable menu buttons -->
      <div id="nav-links">
        
        <!-- Centered Links Group -->
        <div style="display: flex; gap: 20px;">
            <a href="index(home).html">Home</a>
            <a href="book.php">Book</a>
            <a href="user_summary.php">Profile</a>
            <a href="user_history.php" target="_blank">View History</a>
            <a href="feedback.html">Feedback</a>
        </div>

        <!-- Right Aligned SignOut -->
        <a href="login.html" style="position: absolute; right: 20px;">SignOut</a>
      </div>
    `;

  // Insert the 'header' HTML string into the webpage.
  // 'afterbegin' means it will be placed inside the <body> tag, at the very top.
  document.body.insertAdjacentHTML('afterbegin', header);
});