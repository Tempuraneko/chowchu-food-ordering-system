<script src="https://js.stripe.com/v3/"></script>

<script>
    // Ensure the client secret is correctly passed from PHP session to JavaScript
    var clientSecret = '<?php echo $_SESSION['payment_intent_client_secret']; ?>'; 

    var stripe = Stripe('pk_test_51QyBnSFWhBwjflp062iE5ZcIUoFSVoOD8K7qH3G2Ul2NFeiLmGfsXfVHOo56axZ2bGI6ttgI1O7onRXUxzSthlsO00kLj7YHx9');  // Your Stripe publishable key
    
    // Confirm the payment on the client side
    stripe.confirmCardPayment(clientSecret).then(function(result) {
        if (result.error) {
            // Handle error, show message to user
            alert('Payment failed: ' + result.error.message);
        } else {
            // Payment succeeded, redirect user to confirmation page
            // Make sure to pass the `orderID` correctly from PHP to JavaScript
            var orderID = <?php echo json_encode($orderID); ?>;  // Ensure `orderID` is properly passed as JavaScript value
            window.location.href = "trackOrder.php?orderID=" + orderID;
        }
    });
</script>
