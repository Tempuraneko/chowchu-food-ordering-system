<?php
require_once '../lib/PHPMailer.php';
require_once '../lib/SMTP.php';

require '../PHPMailer-master/src/PHPMailer.php';
require '../PHPMailer-master/src/SMTP.php';
require '../PHPMailer-master/src/Exception.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
// ============================================================================
// PHP Setups
// ============================================================================

date_default_timezone_set('Asia/Kuala_Lumpur');

// ============================================================================
// General Page Functions
// ============================================================================

// Is GET request?
function is_get()
{
    return $_SERVER['REQUEST_METHOD'] == 'GET';
}

// Is POST request?
function is_post()
{
    return $_SERVER['REQUEST_METHOD'] == 'POST';
}

// Obtain GET parameter
function get($key, $value = null)
{
    $value = $_GET[$key] ?? $value;
    return is_array($value) ? array_map('trim', $value) : trim($value);
}

// Obtain POST parameter
function post($key, $value = null)
{
    $value = $_POST[$key] ?? $value;
    return is_array($value) ? array_map('trim', $value) : trim($value);
}

// Obtain REQUEST (GET and POST) parameter
function req($key, $value = null)
{
    $value = $_REQUEST[$key] ?? $value;
    return is_array($value) ? array_map('trim', $value) : trim($value);
}

// Redirect to URL
function redirect($url = null)
{
    $url ??= $_SERVER['REQUEST_URI'];
    header("Location: $url");
    exit();
}

// Set or get temporary session variable
function temp($key, $value = null)
{
    if ($value !== null) {
        $_SESSION["temp_$key"] = $value;
    } else {
        $value = $_SESSION["temp_$key"] ?? null;
        unset($_SESSION["temp_$key"]);
        return $value;
    }
}

// Obtain uploaded file --> cast to object
function get_file($key)
{
    $f = $_FILES[$key] ?? null;
    if ($f && $f['error'] == 0) {
        return (object)$f;
    }
    return null;
}

// Crop, resize and save photo
function save_photo($f, $folder, $width = 200, $height = 200)
{
    $photo = uniqid() . '.jpg';

    require_once '../lib/SimpleImage.php';
    $img = new SimpleImage();
    $img->fromFile($f->tmp_name)
        ->thumbnail($width, $height)
        ->toFile("$folder/$photo", 'image/jpeg');

    return $photo;
}

// Is money?
function is_money($value)
{
    return preg_match('/^\-?\d+(\.\d{1,2})?$/', $value);
}

// Is email? 
function is_email($value)
{
    return filter_var($value, FILTER_VALIDATE_EMAIL) !== false;
}

// Is phone number? (Edited)
function is_phoneNo($value)
{
    return preg_match('/^01\d{8,9}$/', $value);
}

// Return local root path
function root($path = '')
{
    return "$_SERVER[DOCUMENT_ROOT]/$path";
}

// Return base url (host + port)
function base($path = '')
{
    return "http://$_SERVER[SERVER_NAME]:$_SERVER[SERVER_PORT]/$path";
}

// ============================================================================
// HTML Helpers
// ============================================================================

// Encode HTML special characters
function encode($value)
{
    return htmlentities($value);
}

// Generate <input type='text'>
function html_text($key, $attr = '')
{
    $value = encode($GLOBALS[$key] ?? '');
    echo "<input type='text' id='$key' name='$key' value='$value' $attr>";
}

// (Edited)
function html_email($key, $attr = '')
{

    $value = encode($GLOBALS[$key] ?? '');
    echo "<input type='email' id='$key' name='$key' value='$value' $attr>";
}

// Generate <input type='password'>
function html_password($key, $attr = '')
{
    $value = encode($GLOBALS[$key] ?? '');
    echo "<input type='password' id='$key' name='$key' value='$value' $attr>";
}

function html_password2($key, $attrs = [])
{
    $value = htmlspecialchars($GLOBALS[$key] ?? '', ENT_QUOTES, 'UTF-8');
    $attributes = '';
    foreach ($attrs as $attrKey => $attrValue) {
        $attributes .= "$attrKey='" . htmlspecialchars($attrValue, ENT_QUOTES, 'UTF-8') . "' ";
    }
    return "<input type='password' id='$key' name='$key' value='$value' $attributes>";
}

// Generate <input type='number'>
function html_number($key, $min = '', $max = '', $step = '', $attr = '')
{
    $value = encode($GLOBALS[$key] ?? '');
    echo "<input type='number' id='$key' name='$key' value='$value'
                 min='$min' max='$max' step='$step' $attr>";
}

// Generate <input type='search'>
function html_search($key, $attr = '')
{
    $value = encode($GLOBALS[$key] ?? '');
    echo "<input type='search' id='$key' name='$key' value='$value' $attr>";
}

// HTML Telephone Input Helper
function html_tel($key, $attr = '')
{
    $value = encode($GLOBALS[$key] ?? '');
    echo "<input type='tel' id='$key' name='$key' value='$value' $attr>";
}


// HTML Date Input Helper
function html_date($key, $attr = '')
{
    $value = encode($GLOBALS[$key] ?? '');
    echo "<input type='date' id='$key' name='$key' value='$value' $attr>";
}

// Generate <textarea>
function html_textarea($key, $attr = '')
{
    $value = encode($GLOBALS[$key] ?? '');
    echo "<textarea id='$key' name='$key' $attr>$value</textarea>";
}

// Generate SINGLE <input type='checkbox'>
function html_checkbox($key, $label = '', $attr = '')
{
    $value = encode($GLOBALS[$key] ?? '');
    $status = $value == 1 ? 'checked' : '';
    echo "<label><input type='checkbox' id='$key' name='$key' value='1' $status $attr>$label</label>";
}

// Generate <input type='radio'> list
function html_radios($key, $items, $br = false)
{
    $value = encode($GLOBALS[$key] ?? '');
    echo '<div>';
    foreach ($items as $id => $text) {
        $state = $id == $value ? 'checked' : '';
        echo "<label><input type='radio' id='{$key}_$id' name='$key' value='$id' $state>$text</label>";
        if ($br) {
            echo '<br>';
        }
    }
    echo '</div>';
}

// Generate <select>
function html_select($key, $items, $default = '- Select One -', $attr = '')
{
    $value = encode($GLOBALS[$key] ?? '');
    echo "<select id='$key' name='$key' $attr>";
    if ($default !== null) {
        echo "<option value=''>$default</option>";
    }
    foreach ($items as $id => $text) {
        $state = $id == $value ? 'selected' : '';
        echo "<option value='$id' $state>$text</option>";
    }
    echo '</select>';
}

// Generate <input type='file'>
function html_file($key, $accept = '', $attr = '')
{
    echo "<input type='file' id='$key' name='$key' accept='$accept' $attr>";
}

// Generate table headers <th>
function table_headers($fields, $sort, $dir, $href = '')
{
    foreach ($fields as $k => $v) {
        $d = 'asc'; // Default direction
        $c = '';    // Default class

        if ($k == $sort) {
            $d = $dir == 'asc' ? 'desc' : 'asc';
            $c = $dir;
        }

        echo "<th><a href='?sort=$k&dir=$d&$href' class='$c'>$v</a></th>";
    }
}

// ============================================================================
// Error Handlings
// ============================================================================

// Global error array
$_err = [];

// Generate <span class='err'>
function err($key)
{
    global $_err;
    if ($_err[$key] ?? false) {
        echo "<span class='err'>$_err[$key]</span>";
    } else {
        echo '<span></span>';
    }
}

// ============================================================================
// Security
// ============================================================================

// Global user object (Edited)
$_user = $_SESSION['user'] ?? null;
$_role = $_SESSION['role'] ?? null;

// Login user (Edited)
function login($user, $role, $redirectUrl, $showAlert = false, $alertMessage = '')
{

    $_SESSION['user'] = $user;
    $_SESSION['role'] = $role;

    if ($showAlert && $alertMessage) {
        echo "<script>
            alert('$alertMessage');
            window.location.href = '$redirectUrl';
        </script>";
        exit;
    } else {
        header("Location: $redirectUrl");
        exit;
    }
}

// Logout user (Edited)
function logout($url = '/')
{
    unset($_SESSION['user']);
    unset($_SESSION['role']);
    redirect($url);
}

// Authorization  (Edited)
function auth(...$roles)
{
    // $_role = the global variable set by the login(role) when user login read from table
    // $roles = role accessible to the web page

    global $_user, $_role;

    if ($_user) {
        if ($roles) {
            //if (in_array($_user->role, $roles)) {
            //    return; // OK
            //}
            if (in_array($_role, $roles)) {
                return;
            }
        } else {
            return; // OK
        }
    }

    redirect('../member/memberLogin.php');
}

// Authorization For Manager only
function authManager()
{

    global $_user, $_role;

    if ($_user) {
        if ($_role == 'manager') {
            return;
        }
    }

    redirect('../admin/accessRestricted.php');
}



//============================================================================================

// ============================================================================
// Email Functions
// ============================================================================

// Send Mail - Forgot password
function get_mail()
{

    $m = new PHPMailer(true);
    $m->isSMTP();
    $m->SMTPAuth = true;
    $m->Host = 'smtp.gmail.com';
    $m->Port = 587;
    $m->Username = 'petonlinestore202409@gmail.com';
    $m->Password = 'goig pfrt tpnl bhjf';
    $m->CharSet = 'utf-8';
    $m->setFrom($m->Username, "😺 Snowie's Pet Shop");

    return $m;
}


// Send Email - Register Email Verification 
function sendVerificationEmail($name, $email, $token)
{


    $mail = new PHPMailer(true);
    try {
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';  // Gmail SMTP server
        $mail->SMTPAuth   = true;
        $mail->Username   = 'petonlinestore202409@gmail.com'; // Your Gmail email address
        $mail->Password   = 'goig pfrt tpnl bhjf'; // Generate an app password in your Gmail settings
        $mail->SMTPSecure = 'tls';
        $mail->Port       = 587;

        $mail->setFrom($mail->Username, "Snowie's Pet Shop");
        $mail->addAddress($email);
        $mail->isHTML(true);

        $mail->Subject = 'Verify Your Email Address';
        $mail->Body = "Dear $name, <br><br> Thank you for signing up with Online Pet Shop. To complete the registration process and verify your email address, please click the link below: <br><br>
                    <a href='http://localhost:8000/page/VerificationEmail.php?token=$token'>Verify Email</a><br><br>If you did not request this verification or believe it to be a mistake, please disregard this email. For assistance or further inquiries, feel free to contact our support team at quillverse.devspace@gmail.com.We look forward to having you onboard!";

        if ($mail->send()) {
            $success_message = "Account created successfully. Please check your email to verify your account.";
        } else {
            $error_message = "Failed to send verification email. Please try again.";
        }
    } catch (Exception $e) {
        error_log("Mailer Error: {$mail->ErrorInfo}"); // Log any exceptions
    }
}

function sendReactivationRequest($name, $email, $message)
{
    try {
        $mail = get_mail();
        $mail->addAddress('petonlinestore202409@gmail.com');
        $mail->Subject = "Account Reactivation Request from $name";
        $mail->Body = "
        <h3>Account Reactivation Request</h3>
        <p><strong>Name:</strong> $name</p>
        <p><strong>Email:</strong> $email</p>
        <p><strong>Message:</strong> $message</p>
        <br><br>
        <p>This is an automated email sent from Snowie's Pet Shop. Please review the account reactivation request and take the necessary action.</p>
        ";
        $mail->isHTML(true);
        if ($mail->send()) {
            temp('info', 'Your request has been submitted. We will get back to you soon.');
        } else {
            temp('info', 'Failed to send the reactivation request. Please try again later.');
        }
    } catch (Exception $e) {
        echo "<p>Mailer Error: {$mail->ErrorInfo}</p>";
    }
}
// ============================================================================
// Database Setups and Functions
// ============================================================================

// Database connection  (Edited)
try {
    $db = new PDO('mysql:host=localhost;dbname=shop;charset=utf8mb4', 'root', '');
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die('Database connection failed: ' . $e->getMessage());
}


function is_unique($value, $table, $field)
{
    global $db;
    $stm = $db->prepare("SELECT COUNT(*) FROM $table WHERE $field = ?");
    $stm->execute([$value]);
    return $stm->fetchColumn() == 0;
}

function is_exists($value, $table, $field)
{
    global $db;
    $stm = $db->prepare("SELECT COUNT(*) FROM $table WHERE $field = ?");
    $stm->execute([$value]);
    return $stm->fetchColumn() > 0;
}

// ============================================================================
// Global Constants and Variables
// ============================================================================
