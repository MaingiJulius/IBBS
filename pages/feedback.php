<?php
session_start();
require_once 'db_connection.php';
$user_id = $_SESSION['user_id'] ?? null;
if (!$user_id) {
    echo "You must be logged in to provide feedback.";
    exit();
}
$trips = [];
$sql_fetch_trips = "SELECT b.booking_id, b.route_id, b.bus_id, r.from_location, r.to_location, r.departure_date, bu.bus_name, bu.reg_no
                    FROM bookings b
                    JOIN routes r ON b.route_id = r.route_id
                    JOIN buses bu ON b.bus_id = bu.bus_id
                    WHERE b.user_id = $user_id
                    ORDER BY r.departure_date DESC";
$result_trips = mysqli_query($conn, $sql_fetch_trips);
while ($row = $result_trips->fetch_assoc()) {
    $trips[] = $row;
}
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $rating = isset($_POST['rating']) ? (int)$_POST['rating'] : 0;
    $comments = isset($_POST['comment']) ? trim($_POST['comment']) : '';
    $trip_data = isset($_POST['trip_select']) ? $_POST['trip_select'] : '';
    if (empty($trip_data)) {
        echo "Please select a trip to rate.";
        exit();
    }
    list($route_id, $bus_id) = explode(':', $trip_data);
    $rating = filter_var($rating, FILTER_SANITIZE_NUMBER_INT);
    $comments = htmlspecialchars($comments, ENT_QUOTES, 'UTF-8');
    if ($rating < 1 || $rating > 5 || empty($comments)) {
        echo "Invalid input. Please pick a star rating and write a comment.";
        exit();
    }
    $feedback_date = date('Y-m-d');
    $sql = "INSERT INTO feedback (user_id, bus_id, route_id, rating, comments, feedback_date) VALUES ('$user_id', '$bus_id', '$route_id', '$rating', '$comments', '$feedback_date')";
    if (mysqli_query($conn, $sql)) {
        header("Location: feedback_success.php");
        exit();
    } else {
        echo "Error saving feedback: " . $stmt->error;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
// Basic Meta Data
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rate Your Experience - Wema Travellers</title>
    <link rel="stylesheet" href="css/style.css"> <!-- General project styles -->
    <link rel="stylesheet" href="css/main.css">
    <link rel="stylesheet" href="Feedback.css">
// Typography
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
</head>
<body class="<?= strtolower($_SESSION['role'] ?? 'passenger') ?>-role">
    <script src="js/header2.js"></script>
    <div style="height: 120px;"></div>
    <div class="feedback-card">
        <?php if (empty($trips)): ?>
            <div class="header-text">
                It looks like you haven't taken any trips with us yet!
            </div>
            <p style="margin-bottom: 20px; color: #666;">Once you've completed a journey, you'll be able to rate your experience here.</p>
            <div class="btn">
                <a href="book.php" class="button" style="text-decoration: none; display: block; text-align: center;">Book Your First Trip</a>
            </div>
        <?php else: ?>
            <div class="header-text">
                Dear esteemed customer, kindly rate us
            </div>
            <div class="star-widget">
                <div class="stars-container">
                    <input type="radio" name="rating_radio" id="rate-5" value="5">
                    <label for="rate-5" title="Love it!">★</label>
                    <input type="radio" name="rating_radio" id="rate-4" value="4">
                    <label for="rate-4" title="Like it">★</label>
                    <input type="radio" name="rating_radio" id="rate-3" value="3">
                    <label for="rate-3" title="Awesome">★</label>
                    <input type="radio" name="rating_radio" id="rate-2" value="2">
                    <label for="rate-2" title="Not bad">★</label>
                    <input type="radio" name="rating_radio" id="rate-1" value="1">
                    <label for="rate-1" title="Hate it">★</label>
                </div>
                <div id="rating-label"></div>
// SUBMISSION FORM
                <form action="feedback.php" method="post" id="feedback-form" onsubmit="return validateForm()">
                    <input type="hidden" name="rating" id="rating-value" value="">
                    <div class="dropdown-container">
                        <select name="trip_select" id="trip-select">
                            <option value="" disabled selected>Select the trip you want to rate...</option>
                            <?php foreach ($trips as $trip): ?>
                                <option value="<?= $trip['route_id'] . ':' . $trip['bus_id'] ?>">
                                    <?= htmlspecialchars($trip['from_location']) ?> to <?= htmlspecialchars($trip['to_location']) ?>
                                    (<?= htmlspecialchars($trip['bus_name']) ?> - <?= htmlspecialchars($trip['departure_date']) ?>)
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="textarea">
                        <textarea name="comment" id="comment" id="comment" placeholder="Describe your experience..." onmouseout="validateComment()"></textarea>
                    </div>
// SUBMIT ACTION
                    <div class="btn">
                        <button type="submit">POST</button>
                    </div>
                </form>
            </div>
        <?php endif; ?>
    </div>
    <div style="height: 50px;"></div>
    <script src="js/footer.js"></script>
    <script>
        const ratingInputs = document.querySelectorAll('input[name="rating_radio"]');
        const ratingHidden = document.getElementById('rating-value');
        const ratingLabel = document.getElementById('rating-label');
        const tripSelect = document.getElementById('trip-select');
        const ratingTexts = {
            "5": "I just love it 😍",
            "4": "I just like it 😎",
            "3": "It is awesome 😄",
            "2": "I don't like it 😏",
            "1": "I just hate it 😠"
        };
        ratingInputs.forEach(input => {
            input.addEventListener('change', () => {
                ratingLabel.innerText = ratingTexts[input.value];
            });
        });
        function validateRating() {
            let selectedRating = "";
            ratingInputs.forEach(input => {
                if (input.checked) {
                    selectedRating = input.value;
                }
            });
            ratingHidden.value = selectedRating;
            if(!selectedRating) {
                alert("Please select a star rating first!");
                return false;
            }
            return true;
        }
        function validateTrip() {
            if(tripSelect && !tripSelect.value) {
                alert("Please select the trip you want to rate!");
                tripSelect.focus();
                return false;
            }
            return true;
        }
        function validateComment() {
            const comment = document.getElementById("comment").value.trim();
            if (comment.length < 5) {
                alert("Please provide a brief comment describing your experience (at least 5 characters).");
                document.getElementById("comment").focus();
                return false;
            }
            return true;
        }
        function validateForm() {
            if (!validateRating()) return false;
            if (!validateTrip()) return false;
            if (!validateComment()) return false;
            return true;
        }
    </script>
</body>
</html>