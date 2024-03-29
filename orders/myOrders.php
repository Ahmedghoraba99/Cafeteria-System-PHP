<?php
try {
    require_once '../db.php';

    $db = new DB();
    $table = 'order_details_view';

    // Check if session is started
    if (session_status() == PHP_SESSION_NONE) {
        session_start();
    }

    // Check if user is logged in
    if (isset($_SESSION['id'])) {
        $name = $_SESSION['name'];
        $user_id = $_SESSION['id'];
    } else {
        // Redirect to login page if user is not logged in
        setcookie("msg", "You are not logged in, please login first");
        header("Location: ../errors/err.php?err=403");
        exit(); // Stop further execution
    }

    // Retrieve orders based on user ID and date range
    $orders = $db->getData($table, "user_id = '$user_id'");
    $ordersCount = "";
    if (!$orders) {
        $ordersCount = 0;
    } else {
        $ordersCount = $orders->num_rows;
    }
} catch (Exception $e) {
    // Handle exceptions
    echo "Error: " . $e->getMessage();
}
?>

<!-- Table -->
<?php
$crrentDate = date("Y-m-d");
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- file imports -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.3/css/bootstrap.min.css" integrity="sha512-jnSuA4Ss2PkkikSOLtYs8BlYIeeIK1h99ty4YfvRPAlzr377vr3CXDb7sb7eEEBYjDtcYj+AjBH3FLv5uSJuXg==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <title>My orders</title>
    <link rel="stylesheet" href="../style/nav.css">
    <style>
        html {
            scroll-behavior: smooth;
        }

        .showing {
            display: flex !important;
        }

        /* Styles for order */
        .order {
            border: 1px solid #ccc;
            padding: 10px;
            margin-bottom: 20px;
        }

        /* Styles for order info */
        .order-info {
            display: flex;
            justify-content: space-between;
            align-items: baseline;
            margin-bottom: 10px;
        }

        /* Styles for product details */
        .product-details {
            display: none;
            flex-wrap: wrap;
            justify-content: space-evenly;
        }

        /* Styles for product */
        .product {
            flex: 0 0 30%;
            /* Adjust as needed */
            padding: 10px;
            margin: 5px;
            border: 1px solid #ddd;
            text-align: center;
        }

        /* Styles for actions */
        .actions {
            margin-top: 10px;
        }

        .red-border {
            border: 2px solid red;
        }

        body {
            background-color: #F4EAE0 !important;
        }
    </style>
</head>

<body class="">
    <?PHP include "../components/nav.php" ?>
    <main class="container">
        <form class="row">
            <div class=" form-group col">
                <label for="start_date">Start Date:</label>
                <input type="date" class="form-control" id="start_date" name="start_date">
            </div>
            <div class=" form-group col">
                <label for="end_date">End Date:</label>
                <input type="date" class="form-control" id="end_date" name="end_date" max="<?php echo $crrentDate ?>">
            </div>
        </form>

        <button onclick="filterOrders()" class="btn btn-primary my-2 " style="height: fit-content;">Filter</button>

        <h2 class="h2 my-5">My Orders</h2>


        <?php
        $totalPrice = 0;
        //check if there are any orders
        if ($ordersCount > 0) {
            foreach ($orders as $row) {
                // Splitting item quantities, prices, and images into arrays
                $quantityArr = explode(",", $row['item_quantities']);
                $priceArr = explode(",", $row['item_prices']);
                $imgArr = explode(",", $row['item_images']);

                // Initialize the cancel button HTML
                $cancelButton = ($row['status'] == 'processing') ? "<a href='../orders/deleteOrder.php?id={$row['order_id']}' class='btn btn-danger mx-auto'>Cancel</a>" : '';

                // Output each order using heredoc syntax
                echo <<<HTML
    <div class="product-container">
    <div class='order'>
        <div class='order-info'>
            <p class='date'>Date: {$row['order_date']}</p>
            <p>Status: {$row['status']}</p>
            <p>Total Amount: {$row['total_amount']}</p>
            <div class='actions'>
                $cancelButton
                <button class='show-details btn btn-primary' style='height: fit-content; margin: 10px;'>Details</button>
            </div>
        </div> <!-- Closing div for order-info -->

        <!-- Output product details (image, quantity, price) in a div -->
        <div class='product-details' class="" style=''>
HTML;
                foreach ($imgArr as $key => $image) {
                    $quantity = $quantityArr[$key];
                    $price = $priceArr[$key];

                    echo <<<HTML
            <div class='product'>
                <img src='../imgs/products/$image' alt='Product Image' style='width: 100px; height: auto;'>
                <p>Quantity: $quantity</p>
                <p>Price: $price</p>
            </div> <!-- Closing div for product -->
HTML;
                }
                echo "</div> <!-- Closing div for product-details -->";
                echo "</div> <!-- Closing div for order -->";
                echo "</div> <!-- closing for product-container -->";
                // Accumulate total price
                $totalPrice += $row['total_amount'];
            }

            echo "<hr>";
            echo "<div class='total-container order-info'>";
            echo "<h3>Total</h3>";
            echo "<div class='total-price'>$totalPrice</div>";
            echo "</div>";
        } else {
            //echo no orders styled div 
            echo "<span class='h1 text-center mx-auto d-block'>No Orders</span>";
        }
        ?>




    </main>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.3/js/bootstrap.min.js" integrity="sha512-ykZ1QQr0Jy/4ZkvKuqWn4iF3lqPZyij9iRv6sGqLRdTPkY69YX6+7wvVGmsdBbiIfN/8OdsI7HABjvEok6ZopQ==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>

    <script>
        document.querySelectorAll('.date').forEach(function(date) {
            var formattedDate = date.innerText.substring(0, date.innerText.length - 3);
            date.innerText = formattedDate;
        });

        function filterOrders() {
            let allOrdersDates = document.querySelectorAll('.date');
            let startDate = document.getElementById('start_date').valueAsDate;
            let endDate = document.getElementById('end_date').valueAsDate;
            console.log(startDate, endDate);
            allOrdersDates
                .forEach(function(date) {
                    let orderDate = date.innerText.split(" ")[1];
                    orderDate = new Date(orderDate);
                    console.log(orderDate, date);
                    if (orderDate >= startDate && orderDate <= endDate) {
                        date.closest('.order').style.display = 'block';
                        // date.closest('.order').classList.toggle('red-border');
                        console.log("found");
                    } else {
                        date.closest('.order').style.display = 'none';
                        if (date.closest('.order').classList.contains('red-border')) {
                            // date.closest('.order').classList.toggle('red-border');
                        }
                    }
                });
        }

        document.addEventListener('DOMContentLoaded', function() {
            const productsContainer = document.querySelector('.product-container');
            if (productsContainer) {
                document.querySelector("body").addEventListener('click', function(e) {
                    // Find the closest parent element with the 'order-info' class
                    const clickedOrder = e.target.closest('.order-info');
                    if (!clickedOrder) return; // Ignore clicks not on product items
                    const orderDetails = clickedOrder.nextElementSibling;
                    if (!orderDetails.classList.contains('product-details')) return; // Ensure it's a product details element
                    orderDetails.classList.toggle('showing');
                    orderDetails.parentElement.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });

                    // Close other product details if open
                    document.querySelectorAll('.product-details.showing').forEach(function(details) {
                        if (details !== orderDetails) {
                            details.classList.remove('showing');
                        }
                    });
                });
            }
        });
    </script>
</body>

</html>