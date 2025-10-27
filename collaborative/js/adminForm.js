//auto generate product id
$(document).ready(function () {
    function generateProductId() {
        var category = $("#category").val();
        var prefix = '';

        switch (category) {
            case 'Pet Toys':
                prefix = 'PT';
                break;
            case 'Pet Food':
                prefix = 'PF';
                break;
            case 'Pet Treats and Biscuits':
                prefix = 'PTB';
                break;
            case 'Health Care':
                prefix = 'HC';
                break;
            case 'Pet Grooming':
                prefix = 'PG';
                break;
            case 'Accessories':
                prefix = 'PA';
                break;
            default:
                prefix = 'PT';
        }

        var randomNum = Math.floor(1000 + Math.random() * 9000);

        var productId = prefix + randomNum;

        $("#id").val(productId);
    }

    generateProductId();
    $("#category").change(generateProductId);
});

function updateStatus(orderId, newStatus) {
    fetch('updateStatus.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded'
        },
        body: `orderId=${orderId}&status=${newStatus}`
    })
    .then(response => response.text())
    .then(data => {
        alert('Status updated successfully!');
    })
    .catch(error => console.error('Error:', error));
}
