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
      <div id="banner">
        <h1>Wema Travellers</h1>
      </div>
  
      <div id="nav-links">
        <div style="display: flex; gap: 20px; align-items: center;">
            <a href="home.php">Home</a>
            
            <!-- ADMIN PANEL DROPDOWN -->
            <div class="dropdown admin-only">
                <a href="admin_dashboard.php">Admin Panel ▾</a>
                <div class="dropdown-content">
                    <a href="admin_dashboard.php">📊 Admin Dashboard</a>
                    <a href="admin_verify_ticket.php">✅ Ticket Verification</a>
                    <a href="book.php">🎫 Walk-in Booking</a>
                    <a href="view_users_sorted.php">👥 User Accounts</a>
                    <a href="view_routes.php">🗺️ Trip Routes</a>
                    <a href="view_admin_bookings.php">📝 Global Bookings</a>
                    <a href="admin_drivers.php">👨‍✈️ Crew Management</a>
                    <a href="admin_buses_report.php">🚌 Fleet Assets</a>
                    <a href="view_feedback.php">💬 User Feedback</a>
                    <a href="view_revenue_report.php">💰 Revenue Insights</a>
                    <a href="view_bus_occupancy.php">💺 Vehicle Occupancy</a>
                    <a href="view_agent_sales.php">📈 Agent Sales</a>
                    <a href="admin_insights.php">🔍 Data Insights</a>
                    <a href="profile.php">👤 Admin Profile</a>
                </div>
            </div>

            <!-- AGENT PANEL DROPDOWN -->
            <div class="dropdown agent-only">
                <a href="agent_dashboard.php">Agent Panel ▾</a>
                <div class="dropdown-content">
                    <a href="agent_dashboard.php">📊 Agent Dashboard</a>
                    <a href="admin_verify_ticket.php">✅ Ticket Verification</a>
                    <a href="book.php">🎫 Instant Booking</a>
                    <a href="view_users_sorted.php">👥 Manage Users</a>
                    <a href="view_routes.php">🗺️ Route Information</a>
                    <a href="view_admin_bookings.php">📝 Manage Tickets</a>
                    <a href="view_feedback.php">💬 Service Feedback</a>
                    <a href="view_bus_occupancy.php">💺 Bus Occupancy</a>
                </div>
            </div>

            <!-- USER PANEL DROPDOWN -->
            <div class="dropdown user-only">
                <a href="dashboard.php">User Panel ▾</a>
                <div class="dropdown-content">
                    <a href="dashboard.php">🏠 Dashboard</a>
                    <a href="book.php">🎟️ Book Trip</a>
                    <a href="view_user_history.php">📜 My History</a>
                    <a href="view_tickets.php">🎫 My Tickets</a>
                    <a href="profile.php">👤 Profile</a>
                    <a href="feedback.php">💬 Give Feedback</a>
                </div>
            </div>

            <a href="profile.php">Profile</a>
            <a href="feedback.php">Feedback</a>
        </div>
        
        <a href="logout.php" style="position: absolute; right: 20px;">SignOut</a>
      </div>
    `;

  // insertAdjacentHTML: Integrates the string directly into the browser's memory of the page content.
  // 'afterbegin': Places it at the top of the body, before any other dashboard content.
  document.body.insertAdjacentHTML('afterbegin', header);
}); // Ends the script.
