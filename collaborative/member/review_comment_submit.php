<?php
require '../component/connect.php';

if (isset($_POST['comment'], $_POST['reviewId'], $_POST['studentId'])) {
    $comment = $_POST['comment'];
    $reviewId = $_POST['reviewId'];
    $studentId = $_POST['studentId'];
    $parentCommentId = $_POST['parentCommentId'] ?? null;  // This is optional for normal comments

    // Prepare the query to insert the comment or reply
    $stmt = $pdo->prepare("INSERT INTO review_comment (reviewId, studentId, comment, parentCommentId) 
                           VALUES (?, ?, ?, ?)");
    $stmt->execute([$reviewId, $studentId, $comment, $parentCommentId]);

    // Redirect back to the review page after submission
    header("Location: reviewPage.php?reviewId=" . $reviewId);
    exit();
} else {
    echo "Invalid submission.";
}
?>
