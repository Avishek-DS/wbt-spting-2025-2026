<?php
$usernameErr = $passwordErr = "";
$username = $password = "";

function cleanInput($data) {
    return htmlspecialchars(trim($data));
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    if (empty($_POST["username"])) {
        $usernameErr = "Enter username";
    } else {
        $username = cleanInput($_POST["username"]);
    }

    if (empty($_POST["password"])) {
        $passwordErr = "Enter password";
    } else {
        $password = $_POST["password"];
    }

    if ($username == "admin" && $password == "123456") {
        echo "<script>alert('Login Success');</script>";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
<title>Login</title>

<style>
    body {
        margin: 0;
        font-family: Arial, sans-serif;
        background: linear-gradient(to right, #667eea, #764ba2);
        height: 100vh;
        display: flex;
        justify-content: center;
        align-items: center;
    }

    .login-box {
        background: white;
        padding: 25px;
        border-radius: 10px;
        width: 280px;
        box-shadow: 0 8px 20px rgba(0,0,0,0.2);
    }

    h2 {
        text-align: center;
        margin-bottom: 20px;
    }

    input {
        width: 100%;
        padding: 10px;
        margin: 8px 0;
        border-radius: 5px;
        border: 1px solid #ccc;
        outline: none;
    }

    input:focus {
        border-color: #667eea;
    }

    .btn {
        background: #667eea;
        color: white;
        border: none;
        cursor: pointer;
        font-weight: bold;
    }

    .btn:hover {
        background: #5a67d8;
    }

    .error {
        color: red;
        font-size: 12px;
        margin-top: -5px;
    }
</style>

</head>
<body>

<div class="login-box">
    <h2>Login</h2>

    <form method="post">
        <input type="text" name="username" placeholder="Username"
               value="<?php echo $username; ?>">
        <div class="error"><?php echo $usernameErr; ?></div>

        <input type="password" name="password" placeholder="Password">
        <div class="error"><?php echo $passwordErr; ?></div>

        <input type="submit" value="Login" class="btn">
    </form>
</div>

</body>
</html>