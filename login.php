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
    <script src="main.js"></script>
</head>
<body>
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
</body>
</html>