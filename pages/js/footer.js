// ==========================================================================
// FOOTER.JS (Footer Section Loader)
// ==========================================================================
// / (forward slash) / (forward slash) starts a single-line comment.
// FOOTER.JS is the module title. ( (bracket) Footer Section Loader 
// (bracket) describes the function of this script.

document.addEventListener('DOMContentLoaded', function () {
// document (the webpage object) . (dot) addEventListener (listen for event) 
// is the tool that monitors the state of the web page. ( (opening bracket) 
// starts the input. 'DOMContentLoaded' (D O M Content Loaded) is the 
// specific event trigger that fires when the HTML structure is fully 
// ready. , (comma) separates the trigger from the action. function 
// (function) starts the logical instructions. ( ) (empty brackets) means 
// no external inputs. { (opening curly bracket) marks the start of the 
// footer loading logic.

// const (constant) is a variable type that cannot be changed once assigned. 
// footer (f o o t e r) is the unique label for the bottom container data. 
// = (equals sign) is the assignment operator. ` (backtick symbol) starts a 
// multi-line template literal string block for HTML code.
  const footer = `
      <div id="footer" class="no-print">
        <p>&copy; 2026 Wema Travellers. All rights reserved.</p>
      </div>
    `;
// ` (backtick symbol) ends the multi-line string block. ; (semicolon) 
// terminates the instruction.

  document.body.insertAdjacentHTML('beforeend', footer);
// document (the page object) . (dot) body (the <body> tag) . 
// insertAdjacentHTML (insert adjacent H T M L) is the built-in function 
// that injects content into the page. ( (opening bracket) starts the 
// command. 'beforeend' (before end) is the precise position instruction 
// which means "place this at the very bottom of the page content". , 
// (comma) separates inputs. footer (the label for our HTML data) ) 
// (closing bracket). ; (semicolon) terminates the instruction.

});
// } (closing curly bracket) ends the logical instructions block. 
// ) (closing bracket) ends the event listener input. ; (semicolon) 
// terminates the entire script.