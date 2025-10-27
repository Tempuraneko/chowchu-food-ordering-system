<?php
include 'component/connect.php';

$name = $_POST['name'];
$email = $_POST['email'];
$password = password_hash($_POST['password'], PASSWORD_DEFAULT); // Encrypt password
$phoneNo = $_POST['phoneNo'];
$address = $_POST['address'];
$status = $_POST['status'];

// Insert into database without birth and profilePicture
$sql = "INSERT INTO `user` (`name`, `email`, `password`, `phoneNo`, `address`, `status`, `created_at`, `updated_at`)
        VALUES (?, ?, ?, ?, ?, ?, NOW(), NOW())";

$stmt = $conn->prepare($sql);
$stmt->bind_param("ssssss", $name, $email, $password, $phoneNo, $address, $status);

if ($stmt->execute()) {
    echo "<script>alert('User registered successfully!'); window.location.href='adminMember.php';</script>";
} else {
    echo "Error: " . $stmt->error;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>User Information Form</title>
</head>
    <body>
    <?php
        require 'component/header.php';
        
    ?>

    <div class="table-data">
        <div class="order">
            <div class="head">
                <h3>Create Member</h3>
            </div>

            <div class="box">
                <form action="" method="post">
                    <fieldset style="border: none;">

                        <div class="inputBox">
                            <label for="name">Name:</label>
                            <?= html_text('name', 'maxlength="100"') ?>
                            <?= err('name') ?>
                        </div>

                        <div class="inputBox">
                            <label for="email">Email:</label>
                            <?= html_email('email') ?>
                            <?= err('email') ?>
                        </div>

                        <div class="inputBox">
                            <label for="phoneNo">Phone:</label>
                            <?= html_tel('phoneNo', 'pattern="[0-9]{10}"') ?>
                            <?= err('phoneNo') ?>
                        </div>

                        <div class="inputBox">
                            <label for="address">Address:</label>
                            <?= html_textarea('address', 'rows="4"') ?>
                            <?= err('address') ?>
                        </div>

                        <div class="inputBox">
                            <label for="password">Password:</label>
                            <?= html_password('password') ?>
                            <small>Password must be at least 8 characters long and include one letter, one number, and one special character.</small>
                            <?= err('password') ?>
                        </div>

                        <div class="inputBox">
                            <label for="confirmedPassword">Confirm Password:</label>
                            <?= html_password('confirmedPassword') ?>
                            <?= err('confirmedPassword') ?>
                        </div>

                        <div class="inputBox">
                            <label for="status">Status:</label>
                            <select name="status" required>
                                <option value="Active">Active</option>
                                <option value="Inactive">Inactive</option>
                            </select>
                        </div>

                        <div class="form-buttons">
                            <button type="submit" name="submit" class="submit-btn">Submit</button>
                            <button type="reset" class="reset-btn">Reset</button>
                            <button type="button" class="cancel-btn" onclick="window.location.href='adminMember.php';">Cancel</button>
                        </div>

                    </fieldset>
                </form>
            </div>
        </div>
    </div>

    <script>
        // Confirm reset
        document.querySelector('[type=reset]').addEventListener('click', function (e) {
            e.preventDefault();
            if (confirm("Are you sure you want to reset the form?")) {
                this.closest('form').reset();
            }
        });
    </script>
<?php include '../component/footer.php'; ?>

</body>
</html>