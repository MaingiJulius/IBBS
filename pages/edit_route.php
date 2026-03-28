<?php                                                                // [1] Open PHP script tag to begin server-side processing logic.
/**                                                                  // [2] Open multi-line documentation block for system meta-data.
 * ================================================================= // [3] Visual separator for clean administrative documentation.
 * ADMINISTRATION: ROUTE ARCHITECTURE EDITOR (edit_route.php)        // [4] Title identifying the core functionality of this specific script.
 * ================================================================= // [5] Visual separator for clean administrative documentation.
 * Purpose: This script allows Administrators to modify existing     // [6] Description of the script's primary objective: modification.
 * travel routes, including locations, dates, times, and costs.      // [7] List of specific route attributes that can be adjusted.
 * Implementation: Session-guarded, POST-driven MySQL updates.       // [8] Technical summary of the security and data handling methods.
 * ================================================================= // [9] Visual separator for clean administrative documentation.
 */                                                                  // [10] Close multi-line documentation block.

session_start();                                                    // [11] Initialize or resume the user session for role verification.
require_once 'db_connection.php';                                    // [12] Import the external database connection bridge object ($conn).

/* --- [A] SECURITY GATEWAY: ROLE VALIDATION --- */                 // [13] Marker for the primary security access control layer.
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'ADMIN') { // [14] Check if user is logged in AND possesses the 'ADMIN' role.
    die("Access denied. Authorized Admins only.");                   // [15] Terminate execution and display error if check fails.
}                                                                    // [16] Close the conditional security boundary.

/* --- [B] DATA ACQUISITION: FETCH TARGET IDENTIFIER --- */         // [17] Marker for identifying which record is being modified.
$route_id = $_GET['id'] ?? null;                                     // [18] Retrieve the 'id' from the URL query string using Null Coalescing.

if (!$route_id) {                                                    // [19] Verify that a valid route identifier was actually provided.
    die("Error: No route ID was provided for editing.");             // [20] Terminate if the identifier is missing from the request.
}                                                                    // [21] Close the identifier verification boundary.

/* --- [C] TRANSACTIONAL LOGIC: UPDATE HANDLER --- */               // [22] Marker for processing the form submission (POST).
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['edit_route'])) { // [23] Check if the request is POST and the 'Update' button was clicked.
    
    // DATA EXTRACTION: Assign incoming form values to localized variables. // [24] Narrative comment for data mapping section.
    $from = $_POST['from_location'];                                 // [25] Capture the updated starting location string from inputs.
    $to = $_POST['to_location'];                                     // [26] Capture the updated destination location string from inputs.
    $date = $_POST['departure_date'];                                // [27] Capture the updated calendar date for travels.
    $time = $_POST['departure_time'];                                // [28] Capture the updated travel time from the time picker.
    $cost = $_POST['cost'];                                          // [29] Capture the updated fiscal pricing for this segment.
    $bus_id = $_POST['bus_id'];                                      // [30] Capture the updated fleet vehicle ID association.

    // SQL ARCHITECTURE: Define the data synchronization command.    // [31] Narrative comment for the database write section.
    $sql = "UPDATE routes SET from_location=?, to_location=?, departure_date=?, departure_time=?, cost=?, bus_id=? WHERE route_id=?"; // [32] Define the SQL UPDATE query with '?' placeholders.
    $stmt = $conn->prepare($sql);                                   // [33] Prepare the SQL command to protect against injection attacks.
    
    // TYPE BINDING: Mapping variables to the query placeholders.    // [34] Narrative comment for parameter mapping.
    // "ssssdii" = 4 Strings, 1 Double/Decimal, 2 Integers.          // [35] Explaining the type definition string for bind_param.
    $stmt->bind_param("ssssdii", $from, $to, $date, $time, $cost, $bus_id, $route_id); // [36] Sanitize and bind the inputs to the statement object.
    $stmt->execute();                                                // [37] Transmit the finalized update command to the MySQL engine.
    $stmt->close();                                                  // [38] Release the server-side statement resource immediately.

    header("Location: admin_routes.php");                           // [39] Trigger an HTTP redirect back to the routes ledger page.
    exit();                                                          // [40] Halt further script execution to ensure redirect behavior.
}                                                                    // [41] Close the POST handling logic block.

/* --- [D] INITIAL STATE: PRE-FILL ENGINE --- */                    // [42] Marker for loading the current database record for UI display.
$stmt = $conn->prepare("SELECT * FROM routes WHERE route_id = ?");   // [43] Prepare a query to fetch the existing route details by ID.
$stmt->bind_param("i", $route_id);                                   // [44] Bind the target identifier as an integer filter.
$stmt->execute();                                                    // [45] Execute the read operation on the database server.
$result = $stmt->get_result();                                       // [46] Retrieve the resulting data packet from the execution.
$route = $result->fetch_assoc();                                     // [47] Extract the results into an associative array for easy access.
$stmt->close();                                                      // [48] Close the read statement to free up memory resources.

if (!$route) {                                                       // [49] Check if the database actually returned a valid record.
    die("Route not found in our records.");                          // [50] Terminate if the provided ID doesn't exist in the table.
}                                                                    // [51] Close the record existence check.
?>                                                                   <!-- [52] Close PHP logic block and prepare for HTML rendering. -->

<!DOCTYPE html>                                                         <!-- [53] Define the document type as standard HTML5. -->
<html lang="en">                                                     <!-- [54] Root element defining the content language as English. -->
<head>                                                               <!-- [55] Head section containing non-visible document metadata. -->
    <meta charset="UTF-8">                                           <!-- [56] Declare UTF-8 character encoding for global data support. -->
    <title>Edit Route</title>                                        <!-- [57] Website title displayed in the browser tab. -->
    <link rel="stylesheet" href="css/style.css">                     <!-- [58] Load the global external stylesheet for project branding. -->
    <style>                                                          <!-- [59] Open internal CSS block for page-specific UI refinements. -->
        body {                                                       /* [60] Target the main body element for typography and layout. */
            font-family: 'Segoe UI', Tahoma, sans-serif;             /* [61] Set the primary font family for professional readability. */
            margin: 0;                                               /* [62] Remove default browser margins for a flush layout. */
            padding: 0;                                              /* [63] Remove default browser padding for a flush layout. */
        }                                                            /* [64] Close body style definition. */

        .container {                                                 /* [65] Styles for the central content wrapper card. */
            width: 90%;                                              /* [66] Set fluid width for responsive tablet behavior. */
            margin: 50px auto;                                       /* [67] Vertical gap and horizontal centering within the viewport. */
            max-width: 800px;                                        /* [68] Limit expansion on large monitors for readability. */
        }                                                            /* [69] Close container style definition. */

        h2 {                                                         /* [70] Styles for the primary section header. */
            text-align: center;                                      /* [71] Center the text for administrative focus. */
            margin-bottom: 30px;                                     /* [72] Create space below the title. */
        }                                                            /* [73] Close header style definition. */

        form {                                                       /* [74] Styles for the management form surface. */
            background: #f4f4f4;                                     /* [75] Light grey background to distinguish from page body. */
            padding: 30px;                                           /* [76] Internal cushioning around form inputs. */
            border-radius: 10px;                                     /* [77] Modern soft-rounded corners for the card. */
        }                                                            /* [78] Close form style definition. */

        .form-group {                                                /* [79] Styles for individual label/input pairs. */
            margin-bottom: 20px;                                     /* [80] Create vertical breathing room between fields. */
        }                                                            /* [81] Close form-group style definition. */

        label {                                                      /* [82] Styles for the input identification labels. */
            font-weight: bold;                                       /* [83] Thick text for better visual scanning. */
            display: block;                                          /* [84] Force labels onto their own line above inputs. */
            margin-bottom: 6px;                                      /* [85] Small gap between label and corresponding input. */
        }                                                            /* [86] Close label style definition. */

        input {                                                      /* [87] Styles for the interactive input fields. */
            width: 100%;                                             /* [88] Stretch inputs to fill the full container width. */
            padding: 10px;                                           /* [89] Ergonomic padding inside the input for easier typing. */
            font-size: 1rem;                                         /* [90] Set readable text size for input content. */
            border-radius: 6px;                                      /* [91] Rounded corners for modern aesthetics. */
            border: 1px solid #ccc;                                  /* [92] Subtle border to define the input boundary. */
            box-sizing: border-box;                                  /* [93] Include padding in width calculation to prevent overflow. */
        }                                                            /* [94] Close input style definition. */

        .btn-submit {                                                /* [95] Styles for the primary 'Update' action button. */
            background-color: #2196F3;                               /* [96] Solid blue brand color for primary actions. */
            border: none;                                            /* [97] Remove default browser button border. */
            color: white;                                            /* [98] High-contrast white text for readability. */
            padding: 12px 20px;                                      /* [99] Larger padding for an ergonomic click target. */
            font-size: 1rem;                                         /* [100] Clear typography for button labels. */
            border-radius: 6px;                                      /* [101] Matched corner radius for UI consistency. */
            cursor: pointer;                                         /* [102] Change mouse cursor to pointer to indicate clickability. */
            display: block;                                          /* [103] Treat as block element for centering logic. */
            margin: 0 auto;                                          /* [104] Center the button horizontally within the form. */
            transition: background 0.3s;                             /* [105] Smooth color transition for hover interaction. */
        }                                                            /* [106] Close submit-button style definition. */

        .btn-submit:hover {                                          /* [107] Interactive hover state for the update button. */
            background-color: #1565C0;                               /* [108] Branded dark blue to provide user feedback. */
        }                                                            /* [109] Close hover style definition. */

        .back-btn {                                                  /* [110] Styles for the return navigation link. */
            margin: 30px auto;                                       /* [111] Center the button with space above and below. */
            display: block;                                          /* [112] Block display to enforce horizontal centering. */
            padding: 10px 20px;                                      /* [113] Smaller padding for secondary navigation. */
            text-align: center;                                      /* [114] Center the link text. */
            background-color: #4CAF50;                               /* [115] Green theme for safe navigation actions. */
            color: white;                                            /* [116] White text for contrast against green. */
            border-radius: 6px;                                      /* [117] Consistent corner aesthetics. */
            text-decoration: none;                                   /* [118] Remove default link underlining. */
            width: fit-content;                                      /* [119] Only wrap logic around the text content. */
            transition: background 0.3s;                             /* [120] Smooth hover transition. */
        }                                                            /* [121] Close back-button style definition. */

        .back-btn:hover {                                            /* [122] Interactive state for the return link. */
            background-color: #388e3c;                               /* [123] Dark green feedback for navigation check. */
        }                                                            /* [124] Close hover style definition. */
    </style>                                                         <!-- [125] Terminate internal CSS block. -->
</head>                                                              <!-- [126] Close document head. -->
<body>                                                               <!-- [127] Begin the visible body section of the webpage. -->

<script src="js/header2.js"></script>                                <!-- [128] Inject the dynamic site-wide navigation header. -->

<div class="container">                                              <!-- [129] Open main content wrapper for layout alignment. -->
    <h2>Edit Route</h2>                                              <!-- [130] Visual title identifying the current administrative task. -->
    <form method="POST">                                             <!-- [131] Open the data submission form using the POST method. -->
        <div class="form-group">                                     <!-- [132] Group container for the 'Starting Location' input. -->
            <label>From Location</label>                             <!-- [133] Textual label identifying the starting city field. -->
            <input type="text" name="from_location" value="<?= htmlspecialchars($route['from_location']) ?>" required> <!-- [134] Text input pre-filled with existing data (XSS protected). -->
        </div>                                                       <!-- [135] Close starting location group. -->
        <div class="form-group">                                     <!-- [136] Group container for the 'Destination' input. -->
            <label>To Location</label>                               <!-- [137] Textual label identifying the arrival city field. -->
            <input type="text" name="to_location" value="<?= htmlspecialchars($route['to_location']) ?>" required> <!-- [138] Text input pre-filled with existing arrival data (XSS protected). -->
        </div>                                                       <!-- [139] Close destination group. -->
        <div class="form-group">                                     <!-- [140] Group container for the 'Departure Date' input. -->
            <label>Departure Date</label>                            <!-- [141] Textual label for the calendar scheduling field. -->
            <input type="date" name="departure_date" value="<?= $route['departure_date'] ?>" required> <!-- [142] Date picker input pre-filled with current scheduled date. -->
        </div>                                                       <!-- [143] Close date group. -->
        <div class="form-group">                                     <!-- [144] Group container for the 'Departure Time' input. -->
            <label>Departure Time</label>                            <!-- [145] Textual label for the clock-time scheduling field. -->
            <input type="time" name="departure_time" value="<?= $route['departure_time'] ?>" required> <!-- [146] Time picker input pre-filled with the current departure hour. -->
        </div>                                                       <!-- [147] Close time group. -->
        <div class="form-group">                                     <!-- [148] Group container for the 'Ticket Cost' input. -->
            <label>Cost</label>                                      <!-- [149] Textual label for the pricing adjustment field. -->
            <input type="number" step="0.01" name="cost" value="<?= $route['cost'] ?>" required> <!-- [150] Numeric input for fare value, supports two decimal places. -->
        </div>                                                       <!-- [151] Close cost group. -->
        <div class="form-group">                                     <!-- [152] Group container for the 'Bus Assignment' input. -->
            <label>Bus ID</label>                                    <!-- [153] Textual label for the vehicle association field. -->
            <input type="number" name="bus_id" value="<?= $route['bus_id'] ?>" required> <!-- [154] Numeric input for the specific vehicle ID linked to this route. -->
        </div>                                                       <!-- [155] Close bus ID group. -->
        <button type="submit" name="edit_route" class="btn-submit">Update Route</button> <!-- [156] Finalize submission button to trigger the POST action. -->
    </form>                                                          <!-- [157] Close the data entry form. -->

    <a href="dashboard.php" class="back-btn">← Back to Dashboard</a> <!-- [158] Emergency exit link to return to the admin portal safely. -->
</div>                                                               <!-- [159] Close the central content wrapper. -->

<script src="js/footer.js"></script>                                 <!-- [160] Inject the site-wide global footer component. -->
</body>                                                              <!-- [161] Terminate the body section. -->
</html>                                                              <!-- [162] Terminate the HTML document. -->
