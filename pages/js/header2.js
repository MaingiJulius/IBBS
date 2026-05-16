// ==========================================================================
// HEADER2.JS (Dashboard Navigation Loader)
// ==========================================================================
// / (forward slash) / (forward slash) starts a single-line comment.
// HEADER2.JS is the module title. ( (bracket) Dashboard Navigation Loader 
// (bracket) describes the function.

document.addEventListener('DOMContentLoaded', function () {
// document (the webpage object) . (dot) addEventListener (listen for event) 
// is the function that monitors the page status. ( (opening bracket) starts 
// the input. 'DOMContentLoaded' (D O M Content Loaded) is the specific 
// trigger that fires when the HTML structure is ready. , (comma) separates 
// the trigger from the action. function (function) starts the logic block. 
// ( ) (empty brackets) means no external inputs. { (opening curly bracket) 
// marks the start of the navigation loading logic.

// const (constant) is a variable type that cannot be changed later. header 
// (h e a d e r) is the unique label for the navigation HTML container. 
// = (equals sign) is the assignment operator. ` (backtick symbol) starts a 
// multi-line template literal string block for HTML code.
    const header = `
      <div id="banner" class="no-print">
        <h1>Wema Travellers</h1>
      </div>
  
      <div id="nav-links" class="no-print">
        <div style="display: flex; gap: 15px; align-items: center;">
            <div class="dropdown">
                <a href="javascript:void(0)" class="dropbtn">Home ▾</a>
                <div class="dropdown-content">
                    <a href="home.php">🏠 Home Page</a>
                    <a href="admin_dashboard.php" class="admin-only">📊 Admin Dashboard</a>
                    <a href="agent_dashboard.php" class="agent-only">📊 Agent Dashboard</a>
                    <a href="dashboard.php" class="user-only">🏠 My Dashboard</a>
                </div>
            </div>
            
            <div class="dropdown admin-only">
                <a href="javascript:void(0)" class="dropbtn">Manage Actors ▾</a>
                <div class="dropdown-content">
                    <a href="view_users_sorted.php">👥 Manage Users</a>
                    <a href="admin_drivers.php">👨‍✈️ Manage Drivers</a>
                    <a href="admin_buses_report.php">🚌 Manage Buses</a>
                </div>
            </div>

            <div class="dropdown admin-only">
                <a href="javascript:void(0)" class="dropbtn">Logistics ▾</a>
                <div class="dropdown-content">
                    <a href="view_routes.php">🗺️ Trip Routes</a>
                    <a href="book.php">🎫 Walk-in Booking</a>
                    <a href="admin_verify_ticket.php">✅ Verification Portal</a>
<!-- [new] < (less than) a (anchor) href (hypertext reference) = (equals) 
     "admin_verify_ticket.php" (the target page) > (greater than) ✅ (emoji) 
     Verification Portal (text) < (less than) / (slash) a (anchor) > (greater than). 
     This code creates a clickable link that opens the internal verification engine. 
     We used this specifically to allow Admins to check ID/Passport numbers 
     directly from the Logistics menu. -->
                </div>
            </div>

            <div class="dropdown admin-only">
                <a href="javascript:void(0)" class="dropbtn">Reports ▾</a>
                <div class="dropdown-content">
                    <a href="view_admin_bookings.php">📝 Global Bookings</a>
                    <a href="admin_manifests.php">📋 Passenger Manifests</a>
                    <a href="view_revenue_report.php">💰 Revenue Reports</a>
                    <a href="view_bus_occupancy.php">💺 Bus Occupancy</a>
                    <a href="view_agent_sales.php">📈 Agent Sales</a>
                    <a href="admin_insights.php">🔍 Data Insights</a>
                    <a href="view_feedback.php">💬 User Feedback</a>
                    <a href="admin_logs.php">📜 Activity Logs</a>
                </div>
            </div>

            <div class="dropdown agent-only">
                <a href="javascript:void(0)" class="dropbtn">Operations ▾</a>
                <div class="dropdown-content">
                    <a href="admin_verify_ticket.php">✅ Verification Portal</a>
<!-- [new] < (less than) a (anchor) href (hypertext reference) = (equals) 
     "admin_verify_ticket.php" (the target page) > (greater than) ✅ (emoji) 
     Verification Portal (text) < (less than) / (slash) a (anchor) > (greater than). 
     This link connects the Agent to the Passenger boarding verification tool. 
     We use this to ensure only travelers with valid ID/Birth Certs are 
     marked as 'Checked-In' on the manifests. -->
                    <a href="book.php">🎫 Instant Booking</a>
                </div>
            </div>

            <div class="dropdown agent-only">
                <a href="javascript:void(0)" class="dropbtn">Management ▾</a>
                <div class="dropdown-content">
                    <a href="view_users_sorted.php">👥 Manage Users</a>
                    <a href="view_routes.php">🗺️ Route Info</a>
                    <a href="view_admin_bookings.php">📝 Manage Tickets</a>
                </div>
            </div>

            <div class="dropdown agent-only">
                <a href="javascript:void(0)" class="dropbtn">Reports ▾</a>
                <div class="dropdown-content">
                    <a href="view_bus_occupancy.php">💺 Bus Occupancy</a>
                    <a href="view_feedback.php">💬 Service Feedback</a>
                </div>
            </div>

            <div class="dropdown user-only">
                <a href="javascript:void(0)" class="dropbtn">My Travel ▾</a>
                <div class="dropdown-content">
                    <a href="book.php">🎟️ Book Trip</a>
                    <a href="view_tickets.php">🎫 My Tickets</a>
                    <a href="view_user_history.php">📜 Travel History</a>
                </div>
            </div>

            <a href="profile.php">Profile</a>
            <a href="feedback.php">Feedback</a>
        </div>
        
        <a href="logout.php" style="position: absolute; right: 20px;">SignOut</a>
      </div>
    `;

// ` (backtick symbol) ends the multi-line string block. ; (semicolon) 
// terminates the instruction.

    document.body.insertAdjacentHTML('afterbegin', header);
// document (the page) . body (the <body> tag) . insertAdjacentHTML (insert 
// HTML at a specific position) is the command that injects the navigation. 
// ( (opening bracket) starts the input. 'afterbegin' (after begin) means 
// place it at the very top of the body. , (comma) separates inputs. 
// header (the HTML variable) ) (closing bracket). ; (semicolon).

    const dropbtns = document.querySelectorAll('.dropbtn');
// const (constant). dropbtns (d r o p b t n s) is the identifier for the 
// list of menu buttons. = (equals sign) assignment. document (the page) 
// . querySelectorAll (find all matches) searches for elements. ( (bracket) 
// '.dropbtn' (the CSS class label) ) (bracket). ; (semicolon).

    dropbtns.forEach(btn => {
// dropbtns (the list) . forEach (for each) is a loop tool that performs 
// an action on every single button found. ( (opening bracket) starts. 
// btn (b t n) is the temporary label for the current button in the loop. 
// => (arrow operator) starts the action. { (opening curly bracket) marks 
// the start of the button configuration.

        btn.addEventListener('click', function(e) {
// btn (the button) . addEventListener (listen) is the tool that waits for 
// user interaction. ( (bracket) 'click' , (comma) 
// function (function) starts the logic. ( e ) (e is the event object 
// captured by the browser). { (opening curly bracket) starts the click logic.

            e.preventDefault();
// e (the event) . preventDefault (prevent default) is a security tool that 
// stops the browser from jumping to a new page when the link is clicked. 
// ( ) (empty brackets). ; (semicolon).

            e.stopPropagation();
// e (the event) . stopPropagation (stop propagation) prevents the click 
// from "bubbling" up to other elements. ; (semicolon).

            const currentDropdown = this.nextElementSibling;
// const (constant). currentDropdown (c u r r e n t underscore d r o p d o w n) 
// is the label for the specific menu associated with the clicked button. 
// = (equals sign) assignment. this (the button clicked) . 
// nextElementSibling (the element immediately after it in HTML) retrieves 
// the menu box. ; (semicolon).

            document.querySelectorAll('.dropdown-content').forEach(content => {
// document . querySelectorAll (find all) finds every single menu on the page. 
// . forEach (for each) starts a loop to manage them. ( content => { 
// (content is the temporary label for each menu).

                if (content !== currentDropdown) {
// if (if) starts a logic check to close other menus. ( (bracket) content 
// (this menu in loop) !== (is NOT identical to) currentDropdown (the 
// menu we want to open) ) (bracket). { (bracket) starts the closing logic.

                    content.classList.remove('show');
// content (the menu) . classList (list of CSS classes) . remove (remove) 
// deletes the "show" instruction. ( (bracket) 'show' (the class) ) 
// (bracket). ; (semicolon).

                }
// } (closing curly bracket) ends the closing logic.

            });
// }); (closing bracket and semicolon) ends the menu management loop.

            currentDropdown.classList.toggle('show');
// currentDropdown (the clicked menu) . classList (list of CSS classes) . 
// toggle (toggle) is a smart tool that adds the class if it's missing or 
// removes it if it's already there. ( (bracket) 'show' ) (bracket). 
// ; (semicolon).

        });
// }); (closing bracket and semicolon) ends the button click logic.

    });
// }); (closing bracket and semicolon) ends the button list loop.

    window.addEventListener('click', function() {
// window (the entire browser) . addEventListener (listen) waits for any 
// click anywhere on the screen. ( (bracket) 'click' , function() { starts 
// the cleanup logic.

        document.querySelectorAll('.dropdown-content').forEach(content => {
// document . querySelectorAll (find all menus) . forEach (for each menu).

            content.classList.remove('show');
// content (the menu) . classList . remove (remove) deletes the 'show' 
// instruction to hide the menu when clicking outside. ( 'show' ). 
// ; (semicolon).

        });
// }); ends the cleanup loop.

    });
// }); ends the global window listener.

});
// }); (closing bracket and semicolon) ends the DOMContentLoaded event logic.
