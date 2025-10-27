<?php
include '../component/connect.php';

$_title = 'Review and Rating';
include '../component/header.php';

$orderId = $_POST['orderId'] ?? null;
$products = [];

if ($orderId) {
    try {
        // Fetch distinct products in the order
        $productsStmt = $pdo->prepare('
            SELECT f.id AS foodId, f.foodName
            FROM orders o 
            LEFT JOIN order_detail oi ON o.orderId = oi.orderId
            LEFT JOIN fooddetail f ON oi.foodId = f.id 
            WHERE o.orderId = ? 
            GROUP BY f.id, f.foodName
        ');
        $productsStmt->execute([$orderId]);
        $products = $productsStmt->fetchAll();
    } catch (PDOException $e) {
        error_log('Database Error: ' . $e->getMessage());
    }
}
?>

<div class="container">
    <h1 class="page-title">Review and Rating</h1>

    <?php if ($orderId): ?>
        <a href="order_detail.php?orderId=<?= urlencode($orderId) ?>" class="back-button">&#8592; Back</a>
        <p></p>
        <p class="order-id">Order ID: <?= htmlspecialchars($orderId) ?></p>

        <?php if (!empty($products)): ?>
            <form method="POST" id="productSelectionForm">
                <label for="productDropdown" class="form-label">Select Product:</label>
                <select name="foodId" id="productDropdown" class="form-select" onchange="document.getElementById('productSelectionForm').submit()">
                    <option value="">-- Select a food for review --</option>
                    <?php foreach ($products as $product): ?>
                        <option value="<?= htmlspecialchars($product['foodId']) ?>" <?= isset($_POST['foodId']) && $_POST['foodId'] == $product['foodId'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($product['foodName']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <input type="hidden" name="orderId" value="<?= htmlspecialchars($orderId) ?>">
            </form>

            <?php if (!empty($_POST['foodId'])): ?>
                <?php
                $selectedFoodId = $_POST['foodId'];
                $reviewStmt = $pdo->prepare('
                    SELECT r.rating, r.comment, r.reviewMedia
                    FROM review r 
                    WHERE r.foodId = ? AND r.orderId = ? AND r.studentID = ?
                ');
                $reviewStmt->execute([$selectedFoodId, $orderId, $_SESSION['studentID']]);
                $existingReview = $reviewStmt->fetch();
                $showWebcam = false; // Flag for showing webcam
                if ($existingReview) {
                    $showWebcam = false; // Webcam should not be shown if there's an existing review
                } else {
                    $showWebcam = true; // Show webcam if there's no existing review
                }
                ?>

                <?php if ($existingReview): ?>
                    <h2 class="review-header">Existing Review</h2>
                    <p><strong>Rating:</strong> 
                        <?= str_repeat("&#9733;", $existingReview['rating']) . str_repeat("&#9734;", 5 - $existingReview['rating']) ?>
                    </p>
                    <p><strong>Comment:</strong> <?= nl2br(htmlspecialchars($existingReview['comment'])) ?></p>
                    <?php if (!empty($existingReview['reviewMedia'])): ?>
                        <h3>Review Media:</h3>
                        <?php
                        $mediaFiles = explode(',', $existingReview['reviewMedia']);
                        foreach ($mediaFiles as $index => $file): 
                            $filePath = htmlspecialchars($file);
                            if (file_exists($filePath)):
                        ?>
                            <!-- Wrap every 4 images into a div container for flex display -->
                            <?php if ($index % 4 == 0): ?>
                                <div class="review-media-row">
                            <?php endif; ?>

                            <div class="review-media">
                                <?php if (preg_match('/\.(jpg|jpeg|png|gif|webp)$/i', $file)): ?>
                                <!-- Dynamically load the image -->
                                <img 
                                    src="<?= $filePath ?>" 
                                    alt="Review Media" 
                                    class="media-image" 
                                    onclick="openModal1('<?= htmlspecialchars($filePath) ?>')"
                                >
                                

                            <?php elseif (preg_match('/\.(mp4|avi|mov)$/i', $file)): ?>
                                <!-- Video for Review Media -->
                                <video controls class="media-video">
                                    <source src="<?= $filePath ?>" type="video/mp4">
                                    Your browser does not support the video tag.
                                </video>
                            <?php endif; ?>
                        </div>
                            <?php if (($index + 1) % 4 == 0 || $index == count($mediaFiles) - 1): ?>
                                </div>
                            <?php endif; ?>
                        <?php endif; endforeach;
                    endif; 
                    ?>

                <?php else: ?>
                    <form method="POST" enctype="multipart/form-data" action="reviewSubmit.php" class="review-form" 
                        data-show-webcam="<?= $showWebcam ? 'true' : 'false' ?>">

                        <input type="hidden" name="foodId" value="<?= htmlspecialchars($selectedFoodId) ?>">
                        <input type="hidden" name="studentId" value="<?= htmlspecialchars($_SESSION['studentID']) ?>"> 
                        <input type="hidden" name="orderId" value="<?= htmlspecialchars($orderId) ?>">

                        <div class="form-group">
                            <label for="comment" class="form-label">Write your review:</label>
                            <textarea id="comment" name="comment" class="form-textarea"></textarea>
                        </div>

                        <div class="form-group">
                        <label for="media" class="form-label">Upload Photos</label>
                        <input type="file" name="media[]" id="media" accept="image/*,video/*" multiple class="form-input">
                        <div id="filePreviewContainer" style="margin-top: 10px;"></div>
                        </div>

                        <!-- Webcam functionality -->
                        <?php if ($showWebcam): ?>
                        <div class="form-group">
                            <label for="webcam" class="form-label">Capture Photos of the Food:</label>
                            <div id="webcam-container">
                                <video id="webcam" autoplay></video>
                                <button type="button" onclick="captureImage()" id="capture">Capture</button>
                            </div>

                            <div id="capturedImagesContainer">
                                <h3>Captured Images:</h3>
                                <div id="capturedImages"></div>
                                <input type="hidden" id="capturedImagesInput" name="capturedImages" />
                            </div>
                        </div>
                        <?php endif; ?>

                        <div class="form-group">
                            <label for="rating" class="form-label">Rate the food:</label>
                            <select id="rating" name="rating" required class="form-select">
                                <option value="">-- Select Rating --</option>
                                <?php for ($i = 1; $i <= 5; $i++): ?>
                                    <option value="<?= $i ?>"><?= $i ?> Star<?= $i > 1 ? 's' : '' ?></option>
                                <?php endfor; ?>
                            </select>
                        </div>

                       <button type="submit" name="submitReview" class="btn-submit">Submit Review</button>
                    </form>
                <?php endif; ?>
            <?php endif; ?>
        <?php else: ?>
            <p>No products found in this order.</p>
        <?php endif; ?>
    <?php else: ?>
        <p>No order selected.</p>
    <?php endif; ?>
</div>

<script>
    // JavaScript to handle webcam and image capturing
    let capturedImagesArray = [];

    function captureImage() {
        const video = document.getElementById('webcam');
        const canvas = document.createElement('canvas');
        canvas.width = video.videoWidth;
        canvas.height = video.videoHeight;
        canvas.getContext('2d').drawImage(video, 0, 0, canvas.width, canvas.height);

        // Convert canvas to base64 image
        const imageData = canvas.toDataURL('image/png');
        capturedImagesArray.push(imageData);

        // Display the captured image
        const imagesContainer = document.getElementById('capturedImages');
        const newImageDiv = document.createElement('div');
        newImageDiv.classList.add('captured-image-container');

        const newImage = document.createElement('img');
        newImage.src = imageData;
        newImage.classList.add('media-image');
        newImage.style.margin = '5px';

        const deleteButton = document.createElement('button');
        deleteButton.textContent = 'Delete';
        deleteButton.classList.add('delete-image-button');
        deleteButton.onclick = () => deleteCapturedImage(imageData, newImageDiv);

        newImageDiv.appendChild(newImage);
        newImageDiv.appendChild(deleteButton);
        imagesContainer.appendChild(newImageDiv);

        // Update hidden input with captured images
        document.getElementById('capturedImagesInput').value = JSON.stringify(capturedImagesArray);
    }

    function deleteCapturedImage(imageData, imageDiv) {
        // Remove image data from the array
        capturedImagesArray = capturedImagesArray.filter(image => image !== imageData);

        // Remove the image div from the DOM
        imageDiv.remove();

        // Update hidden input with remaining captured images
        document.getElementById('capturedImagesInput').value = JSON.stringify(capturedImagesArray);
    }

    // Access the user's webcam
    const reviewForm = document.querySelector('.review-form');
    const showWebcam = reviewForm ? reviewForm.getAttribute('data-show-webcam') === 'true' : false;

    if (showWebcam) {
        navigator.mediaDevices.getUserMedia({ video: true })
            .then(stream => {
                const webcam = document.getElementById('webcam');
                webcam.srcObject = stream;
                webcam.play();
            })
            .catch(err => {
                console.error('Error accessing webcam:', err);
                alert('Webcam access is required to capture images.');
            });
    }
</script>

<script>
    // Function to preview selected files
    function previewFiles() {
        const filePreviewContainer = document.getElementById('filePreviewContainer');
        filePreviewContainer.innerHTML = ''; // Clear previous previews

        const files = document.getElementById('media').files;
        
        for (let i = 0; i < files.length; i++) {
            const file = files[i];
            const fileReader = new FileReader();

            fileReader.onload = function (e) {
                const fileType = file.type.split('/')[0]; // Determine if it's an image or video

                const previewDiv = document.createElement('div');
                previewDiv.classList.add('file-preview');
                previewDiv.setAttribute('data-index', i); // Store index for deletion
                
                const previewContent = document.createElement(fileType === 'image' ? 'img' : 'video');
                previewContent.classList.add('preview-media');
                previewContent.src = e.target.result;

                previewDiv.appendChild(previewContent);

                // Create the delete button
                const deleteButton = document.createElement('button');
                deleteButton.textContent = 'Delete';
                deleteButton.classList.add('delete-file-button');
                deleteButton.onclick = function () {
                    deletePreview(i); // Delete the file by index
                };

                previewDiv.appendChild(deleteButton);
                filePreviewContainer.appendChild(previewDiv);
            };

            fileReader.readAsDataURL(file); // Read the selected file
        }
    }

    // Function to remove the selected file from the file input and the preview container
    function deletePreview(index) {
        const fileInput = document.getElementById('media');
        const fileList = fileInput.files;

        // Create a new FileList object to remove the selected file from the file input
        const newFileList = Array.from(fileList).filter((_, i) => i !== index);
        
        const dataTransfer = new DataTransfer();
        newFileList.forEach(file => dataTransfer.items.add(file));

        // Update the file input with the new file list
        fileInput.files = dataTransfer.files;

        // Remove the corresponding preview from the UI
        const previewDiv = document.querySelector(`.file-preview[data-index="${index}"]`);
        previewDiv.remove();
    }
</script>

<script>
  const mediaInput = document.getElementById('media');
  const previewContainer = document.getElementById('filePreviewContainer');
  let selectedFiles = [];

  mediaInput.addEventListener('change', function () {
    selectedFiles = Array.from(mediaInput.files);
    previewFiles();
  });

  function previewFiles() {
    previewContainer.innerHTML = ''; // Clear existing previews

    selectedFiles.forEach((file, index) => {
      const fileReader = new FileReader();
      fileReader.onload = function (e) {
        const wrapper = document.createElement('div');
        wrapper.style.marginBottom = '10px';

        if (file.type.startsWith('image/')) {
          const img = document.createElement('img');
          img.src = e.target.result;
          img.style.maxWidth = '200px';
          wrapper.appendChild(img);
        } else if (file.type.startsWith('video/')) {
          const video = document.createElement('video');
          video.src = e.target.result;
          video.controls = true;
          video.style.maxWidth = '200px';
          wrapper.appendChild(video);
        }

        const deleteBtn = document.createElement('button');
        deleteBtn.textContent = 'Delete';
        deleteBtn.style.marginLeft = '10px';
        deleteBtn.style.backgroundColor = 'red';
        deleteBtn.style.color = 'white';
        deleteBtn.onclick = () => {
          selectedFiles.splice(index, 1);
          updateInputFiles();
          previewFiles();
        };

        wrapper.appendChild(deleteBtn);
        previewContainer.appendChild(wrapper);
      };
      fileReader.readAsDataURL(file);
    });
  }

  function updateInputFiles() {
    const dataTransfer = new DataTransfer();
    selectedFiles.forEach(file => dataTransfer.items.add(file));
    mediaInput.files = dataTransfer.files;
  }
</script>

<style>
    .media-image {
        width: 300px;   /* Increase the width */
        height: 300px;  /* Increase the height */
        object-fit: cover; /* Ensure the aspect ratio is maintained */
    }

    #webcam-container {
        display: flex;
        flex-direction: column;
        align-items: center;
    }

    #webcam {
        width: 500px;   /* Set a larger size for webcam display */
        height: 375px;  /* Set a larger height */
        background-color: black;
    }

    .captured-image-container {
        display: inline-block;
        margin: 5px;
    }

    .delete-image-button {
        display: block;
        margin-top: 5px;
        padding: 5px;
        background-color: red;
        color: white;
        border: none;
        cursor: pointer;
    }

    .delete-image-button:hover {
        background-color: darkred;
    }
</style>


<?php include '../component/footer.php'; ?>
