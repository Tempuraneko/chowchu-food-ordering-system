<?php
require '../component/connect.php';
session_start();

if (isset($_GET['commentId']) && isset($_GET['reviewId']) && isset($_SESSION['studentID'])) {
    $commentId = $_GET['commentId'];
    $reviewId = $_GET['reviewId'];  // Get reviewId from the URL
    $studentId = $_SESSION['studentID'];

    // Ensure that only the user who made the comment can delete it
    $stmt = $pdo->prepare("SELECT studentId FROM review_comment WHERE commentId = ?");
    $stmt->execute([$commentId]);
    $comment = $stmt->fetch();

    if ($comment && $comment['studentId'] == $studentId) {
        // Delete the comment
        $deleteStmt = $pdo->prepare("DELETE FROM review_comment WHERE commentId = ?");
        $deleteStmt->execute([$commentId]);
        header("Location: reviewPage.php?reviewId=" . $reviewId); 
    } else {
        echo "You cannot delete this comment.";
    }
} else {
    echo "Invalid request.";
}
?>
