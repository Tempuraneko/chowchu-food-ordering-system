<?php  
session_start();
include '../component/connect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submitReview'])) {
    $studentId = $_POST['studentId'];
    $foodId = $_POST['foodId']; 
    $rating = $_POST['rating'];
    $comment = $_POST['comment'];
    $orderId = $_POST['orderId']; 

    // Check if orderId is valid
    if (empty($orderId)) {
        echo "Order ID is required.";
        exit;
    }

    // Initialize empty array for uploaded media paths
    $uploadedMediaPaths = [];
    $uploadDirectory = '../uploads/'; // Directory to save uploaded files

    try {
        // Check if the directory exists, if not, create it
        if (!is_dir($uploadDirectory) && !mkdir($uploadDirectory, 0777, true)) {
            throw new Exception('Failed to create upload directory.');
        }

        // Handle media file uploads (images/videos)
        if (isset($_FILES['media']) && $_FILES['media']['error'][0] === UPLOAD_ERR_OK) {
            // Process media files
            foreach ($_FILES['media']['name'] as $key => $fileName) {
                $fileTmpName = $_FILES['media']['tmp_name'][$key];
                $fileSize = $_FILES['media']['size'][$key];
                $fileExtension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
                
                // Valid extensions for uploaded files
                $validExtensions = ['jpg', 'jpeg', 'png', 'gif', 'svg', 'webp', 'pdf'];
                
                // Validate the file extension
                if (!in_array($fileExtension, $validExtensions)) {
                    throw new Exception("Invalid file type: Only JPG, JPEG, PNG, GIF, SVG, WebP, PDF are allowed.");
                }

                // Validate file size (example: max 10MB)
                if ($fileSize > 10000000) { 
                    throw new Exception("File size exceeds the limit (max 10MB).");
                }

                // Sanitize file name and generate unique name
                $uniqueName = uniqid() . '_' . basename($fileName);
                $uniqueName = preg_replace('/[^a-zA-Z0-9\._-]/', '_', $uniqueName);
                $filePath = $uploadDirectory . $uniqueName;

                // Move the uploaded file to the uploads directory
                if (move_uploaded_file($fileTmpName, $filePath)) {
                    $uploadedMediaPaths[] = $filePath; 
                } else {
                    throw new Exception('Failed to upload file: ' . $fileName);
                }
            }
        }

        // Handle webcam images (base64 encoded)
        if (!empty($_POST['capturedImages'])) {
            $capturedImages = json_decode($_POST['capturedImages'], true); // Decode captured images array
            if (is_array($capturedImages)) {
                foreach ($capturedImages as $imageData) {
                    $imageParts = explode(',', $imageData);
                    $decodedImage = base64_decode($imageParts[1]);
                    $filePath = $uploadDirectory . uniqid() . '_webcam.png';
                    
                    // Save the decoded image to the uploads folder
                    if (file_put_contents($filePath, $decodedImage)) {
                        $uploadedMediaPaths[] = $filePath;
                    } else {
                        throw new Exception('Failed to upload webcam image.');
                    }
                }
            }
        }

        // Convert the uploaded media paths to a comma-separated string for database storage
        $reviewPhotos = !empty($uploadedMediaPaths) ? implode(',', $uploadedMediaPaths) : null; // If no files uploaded, set null

        // Insert the review into the database
        $insertReviewStmt = $pdo->prepare('INSERT INTO review (foodId, orderId, studentId, rating, comment, reviewDate, reviewMedia) 
                                           VALUES (?, ?, ?, ?, ?, NOW(), ?)');
        $insertReviewStmt->execute([ 
            $foodId,
            $orderId,
            $_SESSION['studentID'], 
            $rating,
            $comment,
            $reviewPhotos
        ]);

        // Set the session success message
        $_SESSION['reviewSuccess'] = "Your review has been submitted successfully!";
        
        // Redirect to avoid form resubmission
        header("Location: order_detail.php?orderId=" . urlencode($orderId));
        exit();
    } catch (PDOException $e) {
        error_log('Database Error: ' . $e->getMessage());
        echo '<p class="error">An error occurred while submitting your review. Please try again.</p>';
    } catch (Exception $e) {
        error_log($e->getMessage());
        echo '<p class="error">' . htmlspecialchars($e->getMessage()) . '</p>';
    }
}
?>
