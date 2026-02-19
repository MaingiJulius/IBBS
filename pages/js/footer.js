// ==========================================================================
// FOOTER.JS (Footer Section Loader)
// ==========================================================================
// This script injects the copyright footer into the bottom of every page.
// ==========================================================================

// Wait for the DOM to be fully loaded.
document.addEventListener('DOMContentLoaded', function () {

  // Define the HTML for the footer section.
  // Note: We use the ID 'footer' which is styled in main.css to have the purple background.
  const footer = `
      <div id="footer">
        <!-- &copy; creates the copyright symbol (c) -->
        <p>&copy; 2026 Wema Travellers. All rights reserved.</p>
      </div>
    `;

  // Insert the footer HTML at the very end of the <body> tag.
  // 'beforeend' places it as the last child of the body element.
  document.body.insertAdjacentHTML('beforeend', footer);
});