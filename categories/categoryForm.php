<?php
// Include the DB class file
include_once '../db.php';
// Create an instance of the DB class
$db = new DB();

session_start();

if (isset($_SESSION['role']) && $_SESSION['role'] == 'admin') {
    $name = $_SESSION['name'];
    $user_id = $_SESSION['id'];
} else {
    // Redirect to login page if user is not logged in
    setcookie("msg", "You are not logged in, please login first");
    header("Location: ../errors/err.php?err=403");
    exit(); // Stop further execution
}

$result = $db->getDataSpec("*", "category");
?>

<!doctype html>
<html lang="en">

<head>
    <title>Add category</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="../style/nav.css">
    <style>
        body {
            background-color: #F4EAE0 !important;
        }

        .category-container {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
        }

        .category-item {
            position: relative;
            padding: 15px;
            margin: 5px;
            border: 1px solid #ccc;
            border-radius: 5px;
            background-color: #f2f2f2;
        }

        .delete-category {
            position: absolute;
            top: -5px;
            right: 2px;
            color: red;
            text-decoration: none;
            font-size: 20px;
        }

        .delete-category:hover {
            color: darkred;
            /* Change color on hover */
            text-decoration: none;
            /* Remove underline on hover */
        }
    </style>
</head>

<body>
    <?php include_once '../components/nav.php'; ?>

    <section>
        <div class="text-center text-danger ">
            <h1>Available Categories</h1>
        </div>

        <div class="mx-5 my-2 text-center category-container">
            <?php
            if ($result->num_rows == 0) {
                echo "<p>There are no categories available.</p>";
            } else {
                while ($row = $result->fetch_assoc()) {
                    // echo "<div class='category-item'>" . ucwords($row['name']) . "</div>";
                    echo "<div class='category-item'>" . ucwords($row['name']) . "</div>";
                }
            }
            ?>
        </div>
    </section>

    <section>
        <div class="text-center my-5">
            <h1>Add New Category</h1>
        </div>
        <div>
            <form action="addCategory.php" method="post" class="needs-validation" novalidate>
                <div>
                    <label class="col-12 form-label text-center ">Category</label>
                    <div class="col-6 offset-3 ">
                        <input type="text" name="category" class="form-control" required>
                        <div></div>
                        <?php
                        if (isset($_COOKIE['errMsg'])) {
                            $errorMessage = $_COOKIE['errMsg'];
                            echo "<p class='text-danger my-0 text-center'>$errorMessage</p>";
                        }
                        ?>

                        <p class="invalid-feedback text-danger my-0 text-center" style="font-size:16px">
                            Please add a category!
                        </p>
                    </div>
                    <div class="col-12 text-center">
                        <button class="btn btn-primary my-3" type="submit">Add Category</button>
                    </div>
            </form>
        </div>
    </section>

    <script>
        (() => {
            'use strict'
            const forms = document.querySelectorAll('.needs-validation')
            Array.from(forms).forEach(form => {
                form.addEventListener('submit', event => {
                    if (!form.checkValidity()) {
                        event.preventDefault()
                        event.stopPropagation()
                    }

                    form.classList.add('was-validated')
                }, false)
            })
        })()
    </script>
</body>

</html>