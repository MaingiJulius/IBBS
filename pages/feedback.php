<?php
/**
 * FEEDBACK.PHP
 * Purpose: This page allows registered passengers to submit ratings and reviews 
 * for specific trips they have completed. It uses a premium light-themed UI.
 * Accessible to: Logged-in Passengers.
 */

// --- SESSION AND DATABASE INITIALIZATION ---
// Start a PHP session to track the logged-in user's identity and state across pages.
session_start();

// Include the database connection file. Once included, the $conn object becomes available for SQL queries.
require_once 'db_connection.php';

// --- PART 1: IDENTITY CHECK ---
// Retrieve the user_id from the session. The ?? is the null coalescing operator, ensuring $user_id is null if not set.
$user_id = $_SESSION['user_id'] ?? null;

// Security Guard: If no user is logged in ($user_id is falsy), block access to the feedback form immediately.
if (!$user_id) {
    // Inform the user they need to be authenticated.
    echo "You must be logged in to provide feedback.";
    // exit() stops the script from continuing to load the rest of the page (critical for security).
    exit(); 
}

// --- PART 2: FETCH USER'S COMPLETED TRIPS ---
// Create an empty array which will later hold the list of trips the user has taken.
$trips = [];

// Prepare a SQL query to fetch successful bookings for this specific user.
// JOINs are used to pull data from 'routes' (locations/dates) and 'buses' (bus names) based on foreign keys.
$sql_fetch_trips = "SELECT b.booking_id, b.route_id, b.bus_id, r.from_location, r.to_location, r.departure_date, bu.bus_name, bu.reg_no 
                    FROM bookings b
                    JOIN routes r ON b.route_id = r.route_id
                    JOIN buses bu ON b.bus_id = bu.bus_id
                    WHERE b.user_id = ?
                    ORDER BY r.departure_date DESC";

// Create a prepared statement. This is a security best practice to prevent SQL Injection.
$stmt_trips = $conn->prepare($sql_fetch_trips);

// Link ('bind') the $user_id variable to the '?' placeholder in the SQL query. "i" means it's an integer.
$stmt_trips->bind_param("i", $user_id);

// Tell the database to run the query now.
$stmt_trips->execute();

// Extract the raw data results from the executed SQL statement.
$result_trips = $stmt_trips->get_result();

// Loop through the results one by one (row by row).
while ($row = $result_trips->fetch_assoc()) {
    // Append each individual trip row into our $trips array.
    $trips[] = $row;
}

// --- PART 3: SAVING THE FEEDBACK (POST HANDLER) ---
// This code block only runs if the user has submitted the form (via HTTP POST).
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    // Validate and capture the 'rating' from the form. Cast to (int) for safety.
    $rating = isset($_POST['rating']) ? (int)$_POST['rating'] : 0;
    
    // Capture comments and trim extra whitespace.
    $comments = isset($_POST['comment']) ? trim($_POST['comment']) : '';
    
    // Capture the selected trip value (expected format "route_id:bus_id").
    $trip_data = isset($_POST['trip_select']) ? $_POST['trip_select'] : '';

    // If the user didn't pick a trip, stop and show a message.
    if (empty($trip_data)) {
        echo "Please select a trip to rate.";
        exit();
    }

    // Split the combined "route_id:bus_id" string into two separate variables using explode().
    list($route_id, $bus_id) = explode(':', $trip_data);

    // Sanitize the inputs: remove non-numeric chars from rating, and encode HTML in comments to prevent XSS.
    $rating = filter_var($rating, FILTER_SANITIZE_NUMBER_INT);
    $comments = htmlspecialchars($comments, ENT_QUOTES, 'UTF-8');

    // Content Validation: Ensure rating is between 1-5 and comments aren't empty.
    if ($rating < 1 || $rating > 5 || empty($comments)) {
        echo "Invalid input. Please pick a star rating and write a comment.";
        exit();
    }

    // Capture the current date in YYYY-MM-DD format for record keeping.
    $feedback_date = date('Y-m-d');

    // Define the INSERT query to store the feedback in the 'feedback' table.
    $sql = "INSERT INTO feedback (user_id, bus_id, route_id, rating, comments, feedback_date) VALUES (?, ?, ?, ?, ?, ?)";
    
    // Prepare the insertion statement.
    $stmt = $conn->prepare($sql);
    
    // Bind all the variables to the SQL query. "iiii" = 4 integers, "ss" = 2 strings.
    $stmt->bind_param("iiiiss", $user_id, $bus_id, $route_id, $rating, $comments, $feedback_date);

    // Try to execute the insert. If successful, redirect the user.
    if ($stmt->execute()) {
        // Close statement and connection to clean up server memory.
        $stmt->close();
        $conn->close();
        // Send the user to a success page so they don't resubmit by refreshing.
        header("Location: feedback_success.php");
        exit();
    } else {
        // If something went wrong at the database level, display the error.
        echo "Error saving feedback: " . $stmt->error;
        $stmt->close();
        $conn->close();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <!-- Basic Meta Data -->
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rate Your Experience - Wema Travellers</title>
    
    <!-- CSS Dependencies -->
    <link rel="stylesheet" href="css/style.css"> <!-- General project styles -->
    <link rel="stylesheet" href="css/main.css">  <!-- Core layout styles -->
    <link rel="stylesheet" href="Feedback.css"> <!-- Specific styles for this feedback form -->
    
    <!-- Icons and Typography -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
</head>
<body>

    <!-- Header / Navbar: Injected via JavaScript for consistency across the site -->
    <script src="js/header2.js"></script>
    
    <!-- UI Layout Spacer: Adjusts for the fixed height of the navbar -->
    <div style="height: 120px;"></div>

    <!-- MAIN FEEDBACK CONTAINER -->
    <div class="feedback-card">
        
        <?php if (empty($trips)): ?>
            <!-- EMPTY STATE: If the user has zero completed bookings -->
            <div class="header-text">
                It looks like you haven't taken any trips with us yet!
            </div>
            <p style="margin-bottom: 20px; color: #666;">Once you've completed a journey, you'll be able to rate your experience here.</p>
            <div class="btn">
                <!-- Direct the user to the booking page -->
                <a href="book.php" class="button" style="text-decoration: none; display: block; text-align: center;">Book Your First Trip</a>
            </div>
            
        <?php else: ?>
            <!-- ACTIVE STATE: If there are trips to rate -->
            <div class="header-text">
                Dear esteemed customer, kindly rate us
            </div>

            <div class="star-widget">
                <!-- STAR RATING SYSTEM -->
                <div class="stars-container">
                    <!-- Rating 5: Love it -->
                    <input type="radio" name="rating_radio" id="rate-5" value="5">
                    <label for="rate-5" class="fas fa-star" title="Love it!"></label>

                    <!-- Rating 4: Like it -->
                    <input type="radio" name="rating_radio" id="rate-4" value="4">
                    <label for="rate-4" class="fas fa-star" title="Like it"></label>

                    <!-- Rating 3: Awesome -->
                    <input type="radio" name="rating_radio" id="rate-3" value="3">
                    <label for="rate-3" class="fas fa-star" title="Awesome"></label>

                    <!-- Rating 2: Not bad -->
                    <input type="radio" name="rating_radio" id="rate-2" value="2">
                    <label for="rate-2" class="fas fa-star" title="Not bad"></label>

                    <!-- Rating 1: Hate it -->
                    <input type="radio" name="rating_radio" id="rate-1" value="1">
                    <label for="rate-1" class="fas fa-star" title="Hate it"></label>
                </div>

                <!-- Text display that changes based on selected star (e.g. "It is awesome �") -->
                <div id="rating-label"></div>

                <!-- SUBMISSION FORM -->
                <form action="feedback.php" method="post" id="feedback-form">
                    
                    <!-- Hidden input to store chosen rating number (1-5) for submission -->
                    <input type="hidden" name="rating" id="rating-value" value="">

                    <!-- DROP-DOWN: TRIP SELECTION -->
                    <div class="dropdown-container">
                        <select name="trip_select" id="trip-select" required>
                            <option value="" disabled selected>Select the trip you want to rate...</option>
                            <?php foreach ($trips as $trip): ?>
                                <!-- Each option value is formatted as "route_id:bus_id" -->
                                <option value="<?= $trip['route_id'] . ':' . $trip['bus_id'] ?>">
                                    <?= htmlspecialchars($trip['from_location']) ?> to <?= htmlspecialchars($trip['to_location']) ?> 
                                    (<?= htmlspecialchars($trip['bus_name']) ?> - <?= htmlspecialchars($trip['departure_date']) ?>)
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <!-- TEXTAREA: COMMENTS -->
                    <div class="textarea">
                        <textarea name="comment" placeholder="Describe your experience..." required></textarea>
                    </div>

                    <!-- SUBMIT ACTION -->
                    <div class="btn">
                        <button type="submit">POST</button>
                    </div>
                </form>
            </div>
        <?php endif; ?>
    </div>

    <!-- UI FOOTER -->
    <div style="height: 50px;"></div>
    <script src="js/footer.js"></script>

    <!-- CLIENT-SIDE VALIDATION & INTERACTIVITY -->
    <script>
        // --- OBJECT REFERENCES ---
        const btn = document.querySelector("button"); 
        const ratingInputs = document.querySelectorAll('input[name="rating_radio"]'); 
        const ratingHidden = document.getElementById('rating-value'); 
        const ratingLabel = document.getElementById('rating-label'); 
        const tripSelect = document.getElementById('trip-select'); 

        // --- RATING FEEDBACK MAPPING ---
        // Maps the numeric value to a human-readable string and emoji.
        const ratingTexts = {
            "5": "I just love it 😍",
            "4": "I just like it 😎",
            "3": "It is awesome 😄",
            "2": "I don't like it 😏",
            "1": "I just hate it 😠"
        };

        // --- STAR INTERACTION HANDLER ---
        // Adds an event listener to every star radio button. 
        // When clicked, it updates the text label below the stars.
        ratingInputs.forEach(input => {
            input.addEventListener('change', () => {
                ratingLabel.innerText = ratingTexts[input.value];
            });
        });

        // --- FORM SUBMISSION VALIDATION ---
        // Runs immediately when the user clicks 'POST'.
        btn.onclick = (e) => {
            let selectedRating = "";
            
            // Loop through the stars to find the checked one.
            ratingInputs.forEach(input => {
                if (input.checked) {
                    selectedRating = input.value;
                }
            });
            
            // Set the value of the hidden input so PHP can read the chosen rating.
            ratingHidden.value = selectedRating;

            // Stop the form if no rating (star) was selected.
            if(!selectedRating) {
                alert("Please select a star rating first!");
                e.preventDefault(); // Stop the form submission
                return false;
            }

            // Stop the form if no trip was picked from the list.
            if(tripSelect && !tripSelect.value) {
                alert("Please select the trip you want to rate!");
                e.preventDefault(); // Stop the form submission
                return false;
            }

            // If all checks pass, the form submits normally.
            return true;
        };
    </script>

</body>
</html>
