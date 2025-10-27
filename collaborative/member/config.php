<?php
define('STRIPE_SECRET_KEY', 'sk_test_51QyBnSFWhBwjflp0GNe2Qhea5GoKsbNej6bPuYwMS0bQrVHoUlhsLsus1VSAGNvgXCKsONyhlpOkSkMbU3Sb0ASZ00K8TYg83i'); 
define('STRIPE_PUBLISHABLE_KEY', 'pk_test_51QyBnSFWhBwjflp062iE5ZcIUoFSVoOD8K7qH3G2Ul2NFeiLmGfsXfVHOo56axZ2bGI6ttgI1O7onRXUxzSthlsO00kLj7YHx9'); 
define('STRIPE_SUCCESS_URL', 'http://localhost:8000/member/checkOut_success.php'); //Payment success URL 
define('STRIPE_CANCEL_URL', 'http://localhost:8000/member/checkOut.php'); //Payment cancel URL

define('DB_HOST', 'localhost');   
define('DB_USERNAME', 'root'); 
define('DB_PASSWORD', '');   
define('DB_NAME', 'foodshop'); 
?>
