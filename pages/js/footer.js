// ==========================================================================
// FOOTER.JS (Footer Section Loader)
// ==========================================================================
// Purpose: This script dynamically injects a consistent copyright footer at the absolute bottom of every webpage it is included in.
// This design pattern avoids the need to manually type the footer code into dozens of different HTML files.
// ==========================================================================

// document.addEventListener: Adds an event listener to the entire HTML document.
// 'DOMContentLoaded': Tells the browser to wait until the basic HTML structure (DOM) is fully loaded and parsed.
// function (): The block of code that will execute as soon as the DOM is ready.
document.addEventListener('DOMContentLoaded', function () {

  // const footer: Declares a constant variable (it won't change) to hold the HTML template.
  // The ` (backtick) allows us to create a multi-line string in JavaScript (Template Literal).
  const footer = `
      <div id="footer"> <!-- A div container with ID 'footer', which is specifically styled in main.css for theme consistency. -->
        <!-- &copy;: This is a special HTML Entity that instructs the browser to render the circular Copyright $(\copyright)$ symbol. -->
        <p>&copy; 2026 Wema Travellers. All rights reserved.</p> <!-- The copyright notice text. -->
      </div> <!-- Closes the footer container. -->
    `; // Ends the string definition.

  // document.body: Selects the <body> element of the current page.
  // insertAdjacentHTML: A high-performance method to add HTML content into the document.
  // 'beforeend': A positioning flag that means "place this new HTML just before the </body> tag closes" (i.e., at the very bottom).
  // footer: The variable containing the HTML code we just defined above.
  document.body.insertAdjacentHTML('beforeend', footer);
}); // Closes the event listener function.