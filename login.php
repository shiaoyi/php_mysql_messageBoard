<?php
    require_once("conn.php");

    $error_message = "";
    if(!empty($_POST['username'])){
        $username = $_POST['username'];
        $password = $_POST['password'];

        $stmt = $conn->prepare("SELECT * FROM users WHERE username=? ");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();

        if($result->num_rows > 0){
            $row = $result->fetch_assoc();
            if(password_verify($password,$row['password'])){
                $user_id = $row['id'];
                $sql = "SELECT session_id FROM users_certificate WHERE user_id = $user_id";
                $result = $conn->query($sql);
                $getSession = $result->fetch_assoc();
                setcookie('session_id', $getSession['session_id'], time()+3600*24);
                $conn->close();
                header('Location: index.php');
            }else{
                $error_message="密碼錯誤!";
            }
        }else{
            $error_message="帳號或密碼錯誤!";
        }
        $conn->close(); 
    }
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Register</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" type="text/css" media="screen" href="main.css">
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
    <script src="main.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>
    <style>
        *{
            position: relative;
            box-sizing: border-box;
        }
        .login__main{
            margin: 10px auto;
            width: 300px;
            padding: 20px;
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-light bg-light">
        <a class="navbar-brand" href="index.php">留言板</a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarText" aria-controls="navbarText" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarText">
            <ul class="navbar-nav mr-auto">
                <li class="nav-item active">
                    <a class="nav-link" href="register.php">註冊 <span class="sr-only">(current)</span></a>
                </li>
            </ul>
        </div>
    </nav>
    <div class="login__main">
        <h2>登入</h2>
        <form method="POST" action="login.php">
            <div>username: <input type="text" name="username"/></div>
            <br>
            <div>password: <input type="password" name="password"/></div>
            <br>
            <input type="submit" />
            <br>
            <?php
                if($error_message !== ""){
                    echo $error_message;
                }
            ?>
        </form>
    </div>
</body>
</html>