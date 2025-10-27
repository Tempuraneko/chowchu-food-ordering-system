<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>About Us - Snowie's Pet Shop</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">

</head>

<body>
    <?php
    include '../component/connect.php';
    include '../component/_base2.php';
    include '../component/header.php';
    ?>

    <main>
    <section class="about-us-section">
        <div class="about-logo" >
            <img src="../image/chowChu.png" alt="Chow Chu Logo">
        </div>

        <h1 >About ChowChu</h1>
        <p>Welcome to <strong>Chow Chu</strong>, where relaxation meets style. Our brand is dedicated to crafting products that bring comfort, serenity, and effortless elegance into your everyday life. Whether you're unwinding after a long day, setting the mood for a cozy evening, or simply embracing a laid-back lifestyle, Mellow Mood is here to enhance your experience.</p>

        <p>At <strong>Chow Chu</strong>, we believe relaxation is an art. Our carefully curated collection of products—from cozy essentials to ambient accessories—is designed to create a soothing atmosphere wherever you are. With a commitment to quality, sustainability, and timeless aesthetics, we help you find balance in the midst of a busy world.</p>

        <p>Our mission is to provide a seamless experience that allows you to unwind, recharge, and embrace a life filled with comfort and tranquility.</p>

        <!-- If Mellow Mood has a physical location, update the address and embed the correct map -->
        <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3983.535442162486!2d101.72522597348937!3d3.2158619527420234!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x31cc386996155555%3A0x77dfcf144dc35f05!2sTAR%20UMT%20Yum%20Yum%20Canteen!5e0!3m2!1sen!2smy!4v1746347834695!5m2!1sen!2smy" width="600" height="450" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>

        <h3>Our Location</h3>
        <p>If you’d like to visit us, we’re located at:</p>
        <h3>L1-06A, Seventeen Mall, Jalan 17/38, Seksyen 17, 46400 Petaling Jaya, Selangor</h3>

        <br /><br />
        <h2>Contact Information</h2>
        <p>If you have any questions or need assistance, feel free to reach out to us:</p>
        <ul>
            <li>Email: <a href="mailto:petonlinestore202409@gmail.com">petonlinestore202409@gmail.com</a></li>
        </ul>
    </section>

    </main>
    <?php include '../component/footer.php'; ?>
</body>

<style>
    /* Global Settings */
    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
        font-family: Arial, sans-serif;
        text-align: center;
    }

    body {
        background-color: #f4f4f4;
        color: #333;
        line-height: 1.6;
    }

    a {
        text-decoration: none;
        color: #333;
    }

    a:hover {
        color: #ff6600;
    }

    main {
        padding: 40px 20px;
    }

    .about-us-section {
        max-width: 1000px;
        margin: 0 auto;
        padding: 20px;
        background-color: #fff;
        border-radius: 8px;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    }

    .about-us-section h1 {
        font-size: 36px;
        color: #333;
        margin-bottom: 20px;
        text-align: center;
    }

    .about-us-section p {
        font-size: 18px;
        margin-bottom: 20px;
        line-height: 1.8;
    }

    .about-us-section h2 {
        font-size: 28px;
        color: #333;
        margin-bottom: 10px;
    }
</style>

</html>