// ==========================================================================
// HEADER1.JS (Simple Navigation Loader)
// ==========================================================================
// This script is used for "Entry Pages" like Landing, Login, and Signup.
// It provides a simpler menu with fewer options than the main app header.
// ==========================================================================

// Ensure the page structure is ready before adding elements.
document.addEventListener('DOMContentLoaded', function () {

  // Define the HTML structure for the simple header.
  const header = `
      <!-- Company Banner -->
      <div id="banner">
        <h1>Wema Travellers</h1>
      </div>
  
      <!-- Simple Navigation Links -->
      <div id="nav-links">
        <a href="index.html">Home</a>
        <a href="login.html">Login</a>
        <a href="signup.html">SignUp</a>
      </div>
    `;

  // Inject this simple header at the start of the <body> tag.
  document.body.insertAdjacentHTML('afterbegin', header);
});