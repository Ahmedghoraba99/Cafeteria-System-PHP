<?php
require_once '../db.php';

try {
    $db = new DB();
} catch (Exception $e) {
    // Handle the database connection error gracefully by redirecting to the login page
    header("Location: login.php?error=Invalid_dbConnection");
    exit();
}

// Retrieve user data from the database along with their orders count
$usersResult = $db->getConnection()->query("SELECT u.id, u.name , o.date
                                            FROM user u
                                            JOIN orders o ON u.id = o.user_id
                                          ");
if (!$usersResult) {
    die("Error fetching users: " . $db->getConnection()->error);
}

// Fetch all user names and store them in an array for later use
$userNames = [];
while ($user = $usersResult->fetch_assoc()) {
    $userNames[$user['id']] = $user['name'];
}

// Initialize variables to hold selected user's total price
$selectedUserId = isset($_POST['user']) ? $_POST['user'] : null;
$userTotalPrice = 0;

// Fetch total price for the selected user if one is selected
if (!empty($selectedUserId)) { // Check if a user is selected /I select one user
    if ($selectedUserId !== 'all') { // Check if the selected user is not "Show All"
        // Fetch total price
        $dateFrom = isset($_POST['selected_date_from']) ? $_POST['selected_date_from'] : null;
        $dateTo = isset($_POST['selected_date_to']) ? $_POST['selected_date_to'] : null;
        var_dump($dateFrom);
        var_dump($dateTo);

        // Construct the SQL query with date filters
        $query = "SELECT u.id,
                         u.name,
                         SUM(op.quantity * p.price) AS total_price
                  FROM `user` u
                  JOIN `orders` o ON u.id = o.user_id
                  JOIN `orders_product` op ON o.id = op.order_id
                  JOIN `product` p ON op.product_id = p.id
                  WHERE u.id = $selectedUserId";

        // Add date filters if they are provided
        if ($dateFrom && $dateTo) {
            $query .= " AND o.date BETWEEN '$dateFrom' AND '$dateTo'";
        }

        // Execute the query
        $totalPriceResult = $db->getConnection()->query($query);

        if ($totalPriceResult) {
            $totalPrice = $totalPriceResult->fetch_assoc()['total_price'];
            $userTotalPrice = $totalPrice ? $totalPrice : 0;
        }
    }
}
?>