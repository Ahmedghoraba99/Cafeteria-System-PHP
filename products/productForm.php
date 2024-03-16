 <?php
 include_once '../db.php'; // Include the DB class file
 $errors=[];
if(isset($_GET['errors'])){
    $errors=json_decode($_GET['errors'],true);
    
}
session_start();
// Check if admin is logged in
    if (isset($_SESSION['role']) && $_SESSION['role'] == 'admin') {
    $name = $_SESSION['name'];
    $user_id = $_SESSION['id'];
} else {
    // Redirect to login page if user is not logged in
    setcookie("msg", "You are not logged in, please login first");
    header("Location: ../login/login.php");
    exit(); // Stop further execution
}
$page=$_GET['page'];

//open connection
$db = new DB();
$db->__construct();

$result=$db->getData("category");

$categories = [];

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $categories[] = $row;
    }
}
?>


 <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">
<style>
    .btn-success {
        transition: opacity 0.5s ease-out;
    }
    .hidden {
        opacity: 0;
    }
    body {
        background-color: #F4EAE0 !important;
    }
</style>
    <div class="container">
        <div class="card-header w-50 mx-auto text-center"><h3>Add New Product</h3></div>
        <div class="btn-success w-50 border rounded text-center mx-auto my-2" id="successMsgDiv">
    <?php if (isset($_COOKIE['successMsg'])) {
        $successMsg = $_COOKIE['successMsg'];
        echo "$successMsg";
    } ?></div>
    <form action="addproduct.php?page=<?= $page; ?>" method="post" class="my-2 row g-3 needs-validation border rounded w-50 justify-content-center mx-auto" novalidate enctype="multipart/form-data" id="addProductForm">
            <div class="row my-4">
                <label for="validationCustom01" class="form-label">Product</label>
                <input type="text" class="form-control" id="validationCustom01" placeholder="Product" required name="productname">
                <p class="text-danger"><?php if (isset($errors['name'])) echo $errors['name']; ?></p>
                <div class="invalid-feedback" id="nameError">Please enter a product name.</div>
            </div>
            <div class="row">
                <label for="priceinput" class="form-label">Price</label>
                <input type="Number" name="price" class="form-control" id="priceinput" step="any" placeholder="Price" required>
                <p class="text-danger"><?php if (isset($errors['price'])) echo $errors['price']; ?></p>
            </div>
            <div class="row my-4">
                <label for="validationCustom04" class="form-label">Category</label>
                <select name="category" class="form-select form-control p-3" id="validationCustom04" required>
                    <option selected value="">Select Category</option>
                    <?php foreach ($categories as $category) : ?>
                        <option value="<?= $category['id']; ?>"><?= ucwords($category['name']); ?></option>
                    <?php endforeach; ?>
                </select>
                <div class="invalid-feedback">Please select a category.</div>
            </div>
            <div class="row justify-content-center">
                <a href="../categories/categoryForm.php" class="btn btn-primary w-25">Add Category</a>
              
            </div>

            <div class="row">
                <label class="form-label">Image</label>
                <input type="file" name="img" class="form-control" accept="image/*">
                <p class="text-danger"><?php if (isset($errors['img'])) echo $errors['img']; ?></p>
                <div class="invalid-feedback" id="imageError">Please select an image.</div>



            </div>
        <div class="row justify-content-center my-3">
            <button class="btn btn-primary w-auto" type="submit">Submit</button>
            <a href="productTable.php?page=<?= $page; ?>" class="btn btn-danger mx-3 w-auto">Cancel</a>

        </div>
    </form>
</div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p"
        crossorigin="anonymous"></script>
       <script>
        
    document.addEventListener("DOMContentLoaded", function () {
        var successMsgDiv = document.getElementById('successMsgDiv');

    // Check if the div exists
    if (successMsgDiv) {
        setTimeout(function() {
            successMsgDiv.classList.add('hidden');
        }, 2000); // 3000 milliseconds = 3 seconds
    }
        var form = document.getElementById('addProductForm');
        var productNameInput = document.getElementById('validationCustom01');
        var priceInput = document.getElementById('priceinput');
        var categorySelect = document.getElementById('validationCustom04');
        var imageInput = document.querySelector('input[type="file"]');

        form.addEventListener('submit', function (event) {
            if (!form.checkValidity()) {
                event.preventDefault();
                event.stopPropagation();
            }

            // Product Name validation: check if it contains special characters
            if (!/^[a-zA-Z\s]+$/.test(productNameInput.value.trim())) {
                productNameInput.classList.add('is-invalid');
                document.getElementById('nameError').textContent = 'Product name should not be empty or contains special characters.';
                event.preventDefault();
                event.stopPropagation();
            } else {
                productNameInput.classList.remove('is-invalid');
                document.getElementById('nameError').textContent = '';
            }

            // Price validation: check if it's a valid positive number
            var price = parseFloat(priceInput.value.trim());
            if (isNaN(price) || price <= 0) {
                priceInput.classList.add('is-invalid');
                priceInput.nextElementSibling.textContent = 'Please enter a valid positive price.';
                event.preventDefault();
                event.stopPropagation();
            } else {
                priceInput.classList.remove('is-invalid');
                priceInput.nextElementSibling.textContent = '';
            }

            // Category validation: check if a category is selected
            if (categorySelect.value === '') {
                categorySelect.classList.add('is-invalid');
                categorySelect.nextElementSibling.textContent = 'Please select a category.';
                event.preventDefault();
                event.stopPropagation();
            } else {
                categorySelect.classList.remove('is-invalid');
                categorySelect.nextElementSibling.textContent = '';
            }

            // Image validation: check if an image is selected
            if (!imageInput.value) {
                imageInput.classList.add('is-invalid');
                document.getElementById('imageError').textContent = 'Please select an image.';
                event.preventDefault();
                event.stopPropagation();
            } else {
                imageInput.classList.remove('is-invalid');
                document.getElementById('imageError').textContent = '';
            }

            form.classList.add('was-validated');
        });
    });
</script>

