<?php
    require_once("conn.php");
    
    $nickname = $_POST['nickname'];
    $username = $_POST['username'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $session_id = uniqid();
    $sql = "INSERT INTO users (username, password, nickname) VALUES ('$username', '$password', '$nickname') ";
    $result = $conn->query($sql);

    if($result){
        $last_id = $conn->insert_id;
        setcookie("session_id", $session_id, time()+3600*24);
        $putSession = "INSERT INTO users_certificate (session_id, user_id) VALUES ('$session_id','$last_id')";
        $conn->query($putSession);
    }

    $conn->close();
    header('Location: index.php');
?>