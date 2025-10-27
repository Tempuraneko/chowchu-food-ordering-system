<?php
require '../component/connect.php';
require '../component/header.php';

if (isset($_SESSION['studentID'])) {
    $_user = $_SESSION; 
} else {
    echo "User is not logged in.";
    exit;
}

$_title = 'Review and Rating';

$reviewId = $_GET['reviewId'] ?? null;

// Define sensitive words globally
$sensitiveWords = [
    'idiot', 'stupid', 'bullshit', 'fuck', 'fuck you', 'sohai', 'cibai', 'lan jiao', 'woc', 
    'ni ma', 'chaonima', '你妈', '尼玛', 'jibai', 'pokai', 
    'bitch', 'asshole', 'dick', 'pussy', 'motherfucker', 'bastard', 'slut', 'whore', 
    'cock', 'fag', 'faggot', 'nigger', 'chink', 'spic', 'kike', 'gypsy', 'retard', 'rape', 
    'cunt', 'twat', 'douchebag', 'shithead', 'fuckface', 'wanker', 'piss off', 'shut up', 
    'fucking', 'suck my dick', 'eat shit', 'asswipe', 'son of a bitch'
];

// Function to filter sensitive words
function filterSensitiveWords($comment, $sensitiveWords) {
    foreach ($sensitiveWords as $word) {
        $comment = str_ireplace($word, '***', $comment); // Replace sensitive words with ***
    }
    return $comment;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $comment = $_POST['comment'] ?? '';
    
    $filteredComment = filterSensitiveWords($comment, $sensitiveWords);

    $stmt = $pdo->prepare("INSERT INTO review_comment (reviewId, studentId, comment) VALUES (?, ?, ?)");
    $stmt->execute([$reviewId, $_SESSION['studentID'], $filteredComment]);

    header("Location: reviewPage.php?reviewId={$reviewId}");
    exit;
}

if ($reviewId) {
    $stmt = $pdo->prepare("SELECT * FROM review WHERE reviewId = ?");
    $stmt->execute([$reviewId]);
    $review = $stmt->fetch();

    if (!$review) {
        echo "Review not found.";
        exit;
    }
} else {
    echo "No review ID provided.";
    exit;
}

$orderId = $review['orderId'] ?? 'N/A'; // Assuming orderId is a column in your review table

// Fetch food ID from order_detail table
$stmtFoodId = $pdo->prepare("SELECT foodId FROM order_detail WHERE orderId = ?");
$stmtFoodId->execute([$orderId]);
$orderDetail = $stmtFoodId->fetch();

$foodName = 'N/A';
if ($orderDetail) {
    // Fetch food name from fooddetail table
    $foodId = $orderDetail['foodId'];
    $stmtFoodName = $pdo->prepare("SELECT foodName FROM fooddetail WHERE id = ?");
    $stmtFoodName->execute([$foodId]);
    $food = $stmtFoodName->fetch();
    if ($food) {
        $foodName = $food['foodName']; // Get the food name
    }
}

// Function to recursively display replies to replies
function displayReplies($pdo, $parentCommentId, $reviewId, $sensitiveWords) {
    $replyStmt = $pdo->prepare("SELECT c.*, s.name
                                FROM review_comment c
                                JOIN student s ON c.studentId = s.studentID
                                WHERE c.parentCommentId = ?");
    $replyStmt->execute([$parentCommentId]);
    $replies = $replyStmt->fetchAll();

    if (!empty($replies)) {
        echo "<div class='replies' style='margin-left: 20px;'>";
        foreach ($replies as $reply) {
            // Filter reply for sensitive words
            $filteredReply = filterSensitiveWords($reply['comment'], $sensitiveWords);
            
            echo "<div class='reply-box' id='reply-{$reply['commentId']}'>
                    <p onclick='toggleReplyForm({$reply['commentId']}, \"{$reply['name']}\")'>
                    <strong>{$reply['name']} (Reply):</strong> " . htmlspecialchars($filteredReply) . "</p>
                    <div id='reply-form-{$reply['commentId']}' class='reply-form' style='display:none;'>
                        <form action='review_comment_submit.php' method='post'>
                            <input type='text' name='comment' placeholder='Reply to @{$reply['name']}' required>
                            <input type='hidden' name='reviewId' value='{$reviewId}'>
                            <input type='hidden' name='parentCommentId' value='{$reply['commentId']}'>
                            <input type='hidden' name='studentId' value='{$_SESSION['studentID']}'>
                            <button type='submit'>Post</button>
                        </form>
                    </div>
                  </div>";
            if ($_SESSION['studentID'] == $reply['studentId']) { 
                echo "<a href='javascript:void(0)' class='delete-link' onclick='confirmDelete({$reply['commentId']}, {$reviewId}, true)'>Delete</a>";
            }
            displayReplies($pdo, $reply['commentId'], $reviewId, $sensitiveWords);
        }
        echo "</div>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <link rel="stylesheet" href="../css/review.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

</head>
<body>
    <div class="review-container">
        <h2>Review Details</h2>

        <!-- Display Order ID and Food Name -->
        <p><strong>Order ID:</strong> <?php echo htmlspecialchars($orderId); ?></p>
        <p><strong>Food Name:</strong> <?php echo htmlspecialchars($foodName); ?></p>

        <p><strong>Rating:</strong> <?php echo str_repeat('★', $review['rating']); ?></p>
        <p><strong>Comment:</strong> <?php echo htmlspecialchars($review['comment']); ?></p>

        <?php if (!empty($review['reviewMedia'])): ?>
            <div class="review-media">
                <div class="carousel-images">
                    <?php
                    $mediaFiles = explode(',', $review['reviewMedia']); 
                    $totalImages = count($mediaFiles); 
                    foreach ($mediaFiles as $media) {
                        echo "<img src='" . htmlspecialchars($media) . "' alt='Review media' />";
                    }
                    ?>

                    <?php if ($totalImages > 1): ?>
                        <button class="prev" onclick="moveSlide(-1)">&#10094;</button>
                        <button class="next" onclick="moveSlide(1)">&#10095;</button>
                    <?php endif; ?>
                </div>
            </div>
        <?php endif; ?>


        <h3>Comments:</h3>
        <?php
        // Fetch top-level comments (without parentCommentId)
        $commentStmt = $pdo->prepare("SELECT c.*, s.name
                                      FROM review_comment c
                                      JOIN student s ON c.studentId = s.studentID
                                      WHERE c.reviewId = ? AND c.parentCommentId IS NULL");
        $commentStmt->execute([$reviewId]);
        $comments = $commentStmt->fetchAll();

        if (!empty($comments)) {
            foreach ($comments as $comment) {
                // Filter comment for sensitive words
                $filteredComment = filterSensitiveWords($comment['comment'], $sensitiveWords);
                
                echo "<div class='comment-box' id='comment-{$comment['commentId']}'>";
                echo "<p onclick='toggleReplyForm({$comment['commentId']}, \"{$comment['name']}\")'>
                          <strong>{$comment['name']}:</strong> " . htmlspecialchars($filteredComment) . "</p>";
                
                if ($_SESSION['studentID'] == $comment['studentId']) { 
                    echo "<a href='javascript:void(0)' class='delete-link' onclick='confirmDelete({$comment['commentId']}, {$reviewId})'>Delete</a>";
                }

                // Display replies to this comment
                displayReplies($pdo, $comment['commentId'], $reviewId, $sensitiveWords);

                // Reply form, initially hidden
                echo "<div id='reply-form-{$comment['commentId']}' class='reply-form' style='display:none;'>
                          <form action='review_comment_submit.php' method='post'>
                              <input type='text' name='comment' placeholder='Reply to @{$comment['name']}' required>
                              <input type='hidden' name='reviewId' value='{$reviewId}'>
                              <input type='hidden' name='parentCommentId' value='{$comment['commentId']}'>
                              <input type='hidden' name='studentId' value='{$_SESSION['studentID']}'>
                              <button type='submit'>Post</button>
                          </form>
                      </div>";
                echo "</div>";
            }
        } else {
            echo "<p>No comments yet.</p>";
        }
        ?>

        <form action="review_comment_submit.php" method="post">
            <input type="text" name="comment" required placeholder="Add your comment">
            <input type="hidden" name="reviewId" value="<?php echo htmlspecialchars($reviewId); ?>">
            <input type="hidden" name="studentId" value="<?php echo htmlspecialchars($_SESSION['studentID']); ?>"> <!-- student ID from session -->
            <button type="submit">Post</button>
        </form>
    </div>

<script>
    // Toggle reply form visibility when comment or reply is clicked
    function toggleReplyForm(commentId, studentName) {
        const form = document.getElementById('reply-form-' + commentId);
        const replyInput = form.querySelector('input[name="comment"]');
        replyInput.value = '@' + studentName + ' '; 
        form.style.display = (form.style.display === 'none' || form.style.display === '') ? 'block' : 'none';
    }
</script>

<script>
    let currentIndex = 0;

    function moveSlide(direction) {
        const images = document.querySelectorAll('.carousel-images img');
        const totalImages = images.length;

        currentIndex += direction;
        if (currentIndex < 0) currentIndex = totalImages - 1;
        if (currentIndex >= totalImages) currentIndex = 0;

        for (let i = 0; i < totalImages; i++) {
            images[i].style.display = i === currentIndex ? 'block' : 'none';
        }
    }

    // Initialize the carousel
    moveSlide(0);
</script>

<script>
 // Confirm delete using SweetAlert
function confirmDelete(commentId, reviewId, isReply = false) {
    Swal.fire({
        title: 'Are you sure?',
        text: 'You won\'t be able to revert this!',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Yes, delete it!',
        cancelButtonText: 'No, cancel!',
    }).then((result) => {
        if (result.isConfirmed) {
            // Proceed with the deletion
            if (isReply) {
                window.location.href = 'review_delete.php?commentId=' + commentId + '&reviewId=' + reviewId + '&isReply=true';
            } else {
                window.location.href = 'review_delete.php?commentId=' + commentId + '&reviewId=' + reviewId + '&isReply=false';
            }
        }
    });
}

</script>
<?php include '../component/footer.php'; ?>
</body>
</html>
