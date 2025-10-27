<?php
include '../component/connect.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
  
  <link rel="stylesheet" href="../css/mood.css">
</head>
<body>
  <?php include '../component/header.php'; ?>

  <div class="container">
    <div class="pop-out">
      <a href="../member/menu.php" class="close-btn">❌</a>
      <h2 class="question">How are you feeling today? 🤔</h2>
      
      <div class="emoji-grid">
        <div class="emoji-row">
          <a href="moodSuggest.php?mood=veryhappy" class="emoji-card">
            <img src="../images/veryhappy.png" alt="Very Happy" class="emoji-img large-emoji">
            <div class="emoji-label">Very Happy</div>
          </a>

          <a href="moodSuggest.php?mood=happy" class="emoji-card">
            <img src="../images/happy.png" alt="Happy" class="emoji-img">
            <div class="emoji-label">Happy</div>
          </a>

          <a href="moodSuggest.php?mood=normal" class="emoji-card">
            <img src="../images/normal.png" alt="Normal" class="emoji-img">
            <div class="emoji-label">Normal</div>
          </a>

          <a href="moodSuggest.php?mood=sad" class="emoji-card">
            <img src="../images/sad.png" alt="Sad" class="emoji-img small-emoji">
            <div class="emoji-label">Sad</div>
          </a>

          <a href="moodSuggest.php?mood=upset" class="emoji-card">
            <img src="../images/upset.png" alt="Upset" class="emoji-img">
            <div class="emoji-label">Upset</div>
          </a>
        </div>
      </div>
    </div>
  </div>

  <?php include '../component/footer.php'; ?>
</body>
</html>
