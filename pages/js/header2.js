document.addEventListener('DOMContentLoaded', function () {
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
    document.body.insertAdjacentHTML('afterbegin', header);
    const dropbtns = document.querySelectorAll('.dropbtn');
    dropbtns.forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            const currentDropdown = this.nextElementSibling;
            document.querySelectorAll('.dropdown-content').forEach(content => {
                if (content !== currentDropdown) {
                    content.classList.remove('show');
                }
            });
            currentDropdown.classList.toggle('show');
        });
    });
    window.addEventListener('click', function() {
        document.querySelectorAll('.dropdown-content').forEach(content => {
            content.classList.remove('show');
        });
    });
});