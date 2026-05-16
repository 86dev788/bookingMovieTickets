<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <style>
        /* Container */
        .container {
            width: 40%;
            margin: 0 auto;
        }

        /* Login */
        #div_login {
            border: 1px solid gray;
            border-radius: 3px;
            width: 470px;
            height: 270px;
            box-shadow: 0px 2px 2px 0px gray;
            margin: 0 auto;
        }

        #div_login h1 {
            margin-top: 0px;
            font-weight: normal;
            padding: 10px;
            background-color: cornflowerblue;
            color: white;
            font-family: sans-serif;
        }

        #div_login div {
            clear: both;
            margin-top: 10px;
            padding: 5px;
        }

        #div_login .textbox {
            width: 96%;
            padding: 7px;
        }
    </style>
</head>

<body>
    <div class="container">
        <form method="post" action="">
            <div id="div_login">
                <h1>Login</h1>
                <div>
                    <input type="email" class="form-control" id="txt_email" name="txt_email" placeholder="Email" required />
                </div>
                <div>
                    <input type="password" class="form-control" id="txt_pwd" name="txt_pwd" placeholder="Password" required />
                </div>
                <div>
                    <input type="submit" value="Submit" name="but_submit" id="but_submit" class="btn btn-primary" />
                </div>
            </div>
        </form>
    </div>
</body>

</html>

<?php
include "config.php";

if (isset($_POST['but_submit'])) {

    $email = mysqli_real_escape_string($con, $_POST['txt_email']);
    $password = mysqli_real_escape_string($con, $_POST['txt_pwd']);

    if ($email != "" && $password != "") {
        $stmt = $con->prepare("SELECT email, password FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result && $result->num_rows === 1) {
            $row = $result->fetch_assoc();
            if ($row['password'] === $password) {
                $_SESSION['uname'] = $email;
                header('Location: admin.php');
                exit();
            }
        }

        echo "Invalid email or password";
        $stmt->close();
    }
}
?>