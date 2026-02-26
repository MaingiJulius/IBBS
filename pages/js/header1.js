// ==========================================================================
// HEADER1.JS (Simple Navigation Loader)
// ==========================================================================
// Purpose: This script is specifically customized for "Entry Pages" like landing pages, logins, and signup forms.
// UX Logic: It provides a minimal, distraction-free menu tailored for visitors who are not yet logged in.
// ==========================================================================

// document.addEventListener: Adds an event trigger to the browser window.
// 'DOMContentLoaded': Ensures the browser has ready the page skeleton before we try to modify it.
document.addEventListener('DOMContentLoaded', function () {

  // const header: Declares the variable that stores our simplified HTML markup.
  const header = `
      <!-- Company Branding Banner - Standardized visual identity -->
      <div id="banner"> <!-- Uses global 'banner' ID for consistent purple styling. -->
        <h1>Wema Travellers</h1> <!-- The main branding title. -->
      </div> <!-- Closes the banner section. -->
  
      <!-- Simple Navigation Links - Limited options for non-authenticated guests -->
      <div id="nav-links"> <!-- The flexbox navigation bar. -->
        <a href="index.html">Home</a> <!-- Link back to the primary landing page. -->
        <a href="login.html">Login</a> <!-- Link to the user authentication portal. -->
        <a href="signup.html">SignUp</a> <!-- Link to the new user registration form. -->
      </div> <!-- Closes the navigation container. -->
    `; // Ends the template string.

  // insertAdjacentHTML: Seamlessly merges the 'header' string into the page's live HTML.
  // 'afterbegin': Places the header as the FIRST child of the <body> tag (True header utility).
  document.body.insertAdjacentHTML('afterbegin', header);
}); // Ends the initialization logic.