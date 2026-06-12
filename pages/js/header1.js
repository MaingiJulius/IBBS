document.addEventListener('DOMContentLoaded', function () {
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
  document.body.insertAdjacentHTML('afterbegin', header);
});
// }             .. Closes the anonymous function body.