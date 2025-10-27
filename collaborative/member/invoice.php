<?php
require '../component/connect.php'; // Database connection
require '../vendor/autoload.php'; // Make sure PHPMailer and Dompdf are loaded
use Dompdf\Dompdf;
use Dompdf\Options;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Fetch order details (assuming you have $orderID from GET or session)
$orderID = $_GET['orderId'] ?? null;

if (!$orderID) {
    echo "Order ID is required.";
    exit;
}

// Prepare the database query
$stmt = $pdo->prepare('
    SELECT o.orderId, o.orderDate, o.totalAmount, o.discountAmount, o.orderStatus, 
           u.email, u.name AS recipientName, 
           oi.foodId, f.foodName, oi.quantity, oi.price AS itemPrice, oi.totalAmount AS itemTotalPrice,
           p.amount AS paymentAmount  
    FROM orders o
    LEFT JOIN order_detail oi ON o.orderId = oi.orderId
    LEFT JOIN fooddetail f ON oi.foodId = f.id
    LEFT JOIN student u ON o.studentID = u.studentID
    LEFT JOIN payment p ON o.orderId = p.orderId -- Ensure this join is in place
    WHERE o.orderId = ?
');
$stmt->execute([$orderID]);
$orderDetails = $stmt->fetchAll();

if (!$orderDetails) {
    echo "Order not found.";
    exit;
}

$commonDetails = $orderDetails[0]; // Assuming at least one result

// Dompdf setup
$options = new Options();
$options->set('isHtml5ParserEnabled', true);
$options->set('isRemoteEnabled', true);

$dompdf = new Dompdf($options);

// Generate HTML content for the invoice (without shipping fee)
$html = '
<!DOCTYPE html>
<html>
<head>
    <style>
        body {
            font-family: Arial, sans-serif;
        }
        .invoice-box {
            max-width: 800px;
            margin: auto;
            padding: 30px;
            border: 1px solid #eee;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.15);
            font-size: 16px;
            line-height: 24px;
            color: #555;
        }
        .invoice-box table {
            width: 100%;
            line-height: inherit;
            text-align: left;
        }
        .invoice-box table td {
            padding: 5px;
            vertical-align: top;
        }
        .invoice-box table tr td:nth-child(2) {
            text-align: right;
        }
        .invoice-box table tr.heading td {
            background: #eee;
            border-bottom: 1px solid #ddd;
            font-weight: bold;
        }
        .invoice-box table tr.item td {
            border-bottom: 1px solid #eee;
        }
        .invoice-box table tr.item.last td {
            border-bottom: none;
        }
        .invoice-box table tr.total td:nth-child(4) {
            border-top: 2px solid #eee;
            font-weight: bold;
        }
        .invoice-box .right-align {
            text-align: right;
        }
        .invoice-box .left-align {
            text-align: left;
        }
    </style>
</head>
<body>
    <div class="invoice-box">
        <table>
            <tr class="top">
                <td colspan="4">
                    <table>
                        <tr>
                            <td class="title">
                                <h2>My Company</h2>
                            </td>
                            <td class="right-align">
                                Invoice #: ' . htmlspecialchars($commonDetails['orderId']) . '<br>
                                Created: ' . htmlspecialchars(date('F j, Y', strtotime($commonDetails['orderDate']))) . '<br>
                                Status: ' . htmlspecialchars($commonDetails['orderStatus']) . '
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
            <tr class="information">
                <td colspan="4">
                    <table>
                        <tr>
                            <td class="left-align">
                                My Company<br>
                                123 Street, City, State, 50000<br>
                                Malaysia
                            </td>
                            <td class="left-align">
                                ' . htmlspecialchars($commonDetails['recipientName']) . '<br>
                                ' . htmlspecialchars($commonDetails['email']) . '
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
            <tr class="heading">
                <td>Item</td>
                <td>Quantity</td>
                <td>Unit Price (RM)</td>
                <td>Total (RM)</td>
            </tr>';

foreach ($orderDetails as $order) {
    $html .= '
            <tr class="item">
                <td>' . htmlspecialchars($order['foodName']) . '</td>
                <td>' . htmlspecialchars($order['quantity']) . '</td>
                <td>' . htmlspecialchars(number_format($order['itemPrice'], 2)) . '</td>
                <td>' . htmlspecialchars(number_format($order['itemTotalPrice'], 2)) . '</td>
            </tr>';
}

// Add total amounts (without shipping fee)
$html .= '
            <tr class="total">
                <td>Subtotal</td>
                <td></td>
                <td></td>
                <td>' . htmlspecialchars(number_format($commonDetails['totalAmount'], 2)) . '</td>
            </tr>
            <tr class="total">
                <td>Discount</td>
                <td></td>
                <td></td>
                <td>' . htmlspecialchars(number_format($commonDetails['discountAmount'], 2)) . '</td>
            </tr>
            <tr class="total">
                <td>Total</td>
                <td></td>
                <td></td>
                <td>' . htmlspecialchars(number_format($commonDetails['paymentAmount'], 2)) . '</td>
            </tr>
        </table>
    </div>
</body>
</html>';

$dompdf->loadHtml($html);
$dompdf->setPaper('A4', 'portrait');
$dompdf->render();
$pdfContent = $dompdf->output(); // PDF content generated
$dompdf->stream(); 
// Send the email with PHPMailer
$mail = new PHPMailer(true);
try {
    // Set up PHPMailer
    $mail->isSMTP();
    $mail->Host = 'smtp.gmail.com';
    $mail->SMTPAuth = true;
    $mail->Username = 'ngjiawei0201@gmail.com';
    $mail->Password = 'tszsouqmytpxotlg';
    $mail->SMTPSecure = 'tls';
    $mail->Port = 587;

    $mail->setFrom('ngjiawei0201@gmail.com', 'Chowchu Support');
    $mail->addAddress($commonDetails['email']); // Customer's email

    // Email content
    $mail->isHTML(true);
    $mail->Subject = 'Invoice for Order #' . htmlspecialchars($commonDetails['orderId']);
    $mail->Body    = 'Please find your invoice attached.';

    // Attach the PDF invoice
    $mail->addStringAttachment($pdfContent, 'invoice_' . htmlspecialchars($commonDetails['orderId']) . '.pdf', 'base64', 'application/pdf');

    // Send the email
    $mail->send();
    echo '✅ Invoice has been sent to the customer!';
} catch (Exception $e) {
    echo "❌ Mail error: {$mail->ErrorInfo}";
}

?>
