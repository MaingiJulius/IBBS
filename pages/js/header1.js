// ==========================================================================
// HEADER1.JS (Simple Navigation Loader)
// ==========================================================================
// / (forward slash) / (forward slash) starts a single-line documentation comment.
// HEADER1.JS is the module title. ( (bracket) Simple Navigation Loader 
// (bracket) describes the purpose of this script for entry pages.

document.addEventListener('DOMContentLoaded', function () {
// document (the webpage object) . (dot) addEventListener (listen for event) 
// is the tool that monitors the state of the web page. ( (opening bracket) 
// starts the input. 'DOMContentLoaded' (D O M Content Loaded) is the 
// specific event trigger that fires when the HTML structure is fully 
// ready. , (comma) separates the trigger from the action. function 
// (function) starts the logical instructions. ( ) (empty brackets) means 
// no external inputs. { (opening curly bracket) marks the start of the 
// simple navigation loading logic.

// const (constant) is a variable type that cannot be changed once assigned. 
// header (h e a d e r) is the unique label for the simple navigation HTML data. 
// = (equals sign) is the assignment operator. ` (backtick) starts a 
// multi-line template literal string block for HTML code.
  const header = `
      <div id="banner" class="no-print">
        <h1>Wema Travellers</h1>
      </div>
  
      <div id="nav-links" class="no-print">
        <a href="index.html">Home</a>
        <a href="login.html">Login</a>
        <a href="signup.html">SignUp</a>
        <a href="contact.php">Contact Us</a>
      </div>
    `;
// ` (backtick symbol) ends the multi-line string block. ; (semicolon) 
// terminates the instruction.

  document.body.insertAdjacentHTML('afterbegin', header);
// document (the page object) . (dot) body (the <body> tag) . 
// insertAdjacentHTML (insert adjacent H T M L) is the built-in function 
// that injects content into the page. ( (opening bracket) starts the 
// command. 'afterbegin' (after begin) is the precise position instruction 
// which means "place this at the very top of the page content". , 
// (comma) separates inputs. header (the label for our HTML data) ) 
// (closing bracket). ; (semicolon) terminates the instruction.

});
// } (closing curly bracket) ends the logical instructions block. 
// ) (closing bracket) ends the event listener input. ; (semicolon) 
// terminates the entire script.