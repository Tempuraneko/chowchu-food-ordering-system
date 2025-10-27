<?php
require '../component/connect.php'; // Include the database connection

// Check if the reviewId is passed through the POST request
if (isset($_POST['reviewId']) && !empty($_POST['reviewId'])) {
    // Sanitize the reviewId to prevent SQL injection
    $reviewId = $_POST['reviewId'];

    // Prepare the SQL statement to delete the review
    $stmt = $pdo->prepare('DELETE FROM review WHERE reviewId = ?');
    
    // Execute the statement
    $stmt->execute([$reviewId]);

    // Check if the review was deleted
    if ($stmt->rowCount() > 0) {
        // Successfully deleted, redirect or show success message
        echo "success"; // Or redirect back with success message
    } else {
        // No rows deleted, show an error
        echo "error"; // Or redirect back with error message
    }
} else {
    // If reviewId is not set, show an error
    echo "error";
}
?>
