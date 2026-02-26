// ==========================================================================
// HEADER.JS (Primary Navigation Loader)
// ==========================================================================
// Purpose: This script automatically injects the main navigation bar and branding banner into the very top of the webpage.
// Design Logic: Centralizing the header ensures that if the company name or menu changes, we only edit one file instead of many.
// ==========================================================================

// document.addEventListener: Listens for browser events.
// 'DOMContentLoaded': Specifically waits for the HTML to be downloaded and turned into a tree (DOM) before running.
// function (): The logic wrapper for the injection process.
document.addEventListener('DOMContentLoaded', function () {

  // const header: Defines a container for the HTML markup of the global header.
  const header = `
      <!-- TOP BANNER Section: Holds the primary branding and company title. -->
      <div id="banner"> <!-- Div with ID 'banner', styled in the CSS with a 1.5rem italicized font. -->
        <h1>Wema Travelers</h1> <!-- The main Level-1 heading for the site. -->
      </div> <!-- Closes the banner division. -->
  
      <!-- NAVIGATION LINKS Section: Holds the interactive menu for user navigation. -->
      <div id="nav-links"> <!-- Styled in main.css as a lavender flexbox container centered on the screen. -->
        
        <!-- Centered Links Group - Wraps the main internal navigation destinations. -->
        <div style="display: flex; gap: 20px;"> <!-- Inline Flexbox specifically to manage the 20-pixel gap between links. -->
            <a href="index(home).html">Home</a> <!-- Link to the public-facing landing page. -->
            <a href="book.php">Book</a> <!-- Link to the bus booking engine. -->
            <a href="user_summary.php">Profile</a> <!-- Link to the user's dashboard/summary. -->
            <a href="user_history.php" target="_blank">View History</a> <!-- Target='_blank' opens the travel history in a new browser tab. -->
            <a href="feedback.html">Feedback</a> <!-- Link to the star rating and commment form. -->
        </div> <!-- Closes the flex group. -->

        <!-- Right Aligned SignOut - A standalone link moved to the far right for UI balance. -->
        <a href="login.html" style="position: absolute; right: 20px;">SignOut</a> <!-- 'absolute' pulls it out of the center and 'right: 20px' pins it. -->
      </div> <!-- Closes the navigation container. -->
    `; // Ends the string.

  // document.body: Selects the main content body of the HTML document.
  // insertAdjacentHTML: Injects our string as raw HTML.
  // 'afterbegin': A positioning flag that means "place this at the very start of the <body> tag" (At the top).
  // header: The variable containing the banner and link code.
  document.body.insertAdjacentHTML('afterbegin', header);
}); // Ends the DOM listener.