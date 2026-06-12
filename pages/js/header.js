document.addEventListener('DOMContentLoaded', function () {
  const header = `
      <div id="banner" class="no-print">
        <h1>Wema Travelers</h1>
      </div>
      <div id="nav-links" class="no-print">
        <div style="display: flex; gap: 20px;">
            <a href="index(home).html">Home</a>
            <a href="book.php">Book</a>
            <a href="user_summary.php">Profile</a>
            <a href="user_history.php" target="_blank">View History</a>
            <a href="feedback.html">Feedback</a>
        </div>
        <a href="login.html" style="position: absolute; right: 20px;">SignOut</a>
      </div>
    `;
  document.body.insertAdjacentHTML('afterbegin', header);
});