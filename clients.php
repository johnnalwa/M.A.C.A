<?php
$selectedType = '';

// Check if a type filter is selected
if (isset($_GET['filter'])) {
    $selectedType = $_GET['filter'];
}

// Establish a database connection (update with your database credentials)
$link = mysqli_connect("localhost", "root", "", "acapp");

if (!$link) {
    die("Connection failed: " . mysqli_connect_error());
}

// Query to retrieve clients based on the selected type
$typeCondition = "";
if (!empty($selectedType)) {
    $typeCondition = "WHERE type = '$selectedType'";
}

$query = "SELECT * FROM clients $typeCondition";

$result = mysqli_query($link, $query);

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Client List</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        .card {
            margin: 10px;
            width: 300px;
            height: 420px;
            float: left; /* Ensure cards are displayed in a grid */
        }
        .card-body {
            padding: 5px;
        }
        .card-text {
            font-size: 9px; /* Adjust the font size as needed */
        }
        .card-container {
            width: 80%; /* Set the width of the container */
            overflow-x: auto; /* Enable horizontal scrolling if needed */
            white-space: nowrap; /* Prevent cards from wrapping to the next line */
        }
    </style>
</head>
<body>
    <div class="container">
        <h2 class="text-center">Client List</h2>
        <div>
            <form method="get">
                <label>Filter by Type:</label>
                <input type="radio" name="filter" value="prospects" <?php if ($selectedType === 'prospects') echo "checked"; ?>> Prospects
                <input type="radio" name="filter" value="lead" <?php if ($selectedType === 'lead') echo "checked"; ?>> Lead
                <input type="radio" name="filter" value="conversion" <?php if ($selectedType === 'conversion') echo "checked"; ?>> Conversion
                <input type="submit" value="Apply Filter">
            </form>
        </div>
        <div class="card-container">
            <?php
            while ($row = mysqli_fetch_assoc($result)) {
                echo '<div class="card">';
                echo '<div class="card-body">';
                echo '<h5 class="card-title">' . $row['client_fullname'] . '</h5>';
                echo '<p class="card-text">ID: ' . $row['id_number'] . '</p>';
                echo '<p class="card-text">Phone: ' . $row['phone_number'] . '</p>';
                echo '<p class="card-text">Ministry: ' . $row['ministry'] . '</p>';
                echo '<p class="card-text">Type: ' . $row['type'] . '</p>';
                echo '<p class="card-text">PF Number (Lead): ' . $row['pf_number'] . '</p>';
                echo '<p class="card-text">Amount (Lead): ' . $row['amount'] . '</p>';
                echo '<p class="card-text">Comment (Lead): ' . $row['comment'] . '</p>';
                echo '<p class="card-text">PF Number (Conversion): ' . $row['pf_number_conversion'] . '</p>';
                echo '<p class="card-text">Password (Conversion): ' . $row['password'] . '</p>';
                echo '<p class="card-text">Amount Applied (Conversion): ' . $row['amount_applied'] . '</p>';
                echo '<p class="card-text">Date (Conversion): ' . $row['date_field'] . '</p>';
                echo '<p class="card-text">Type of Loan Qualify: ' . $row['type_loan_qualify'] . '</p>';
                echo '<p class="card-text">Comment (Conversion): ' . $row['comment_conversion'] . '</p>';
                echo '</div>';
                echo '</div>';
            }
            ?>
        </div>
    </div>
</body>
</html>


<?php
// Close the database connection
mysqli_close($link);
?>
