<?php
    require_once("conn.php");
    // print_r($_POST);
    $user_id = $_POST['user_id'];
    $content = $_POST['content'];
    $parent_id = $_POST['parent_id'];
    
    $sql = "INSERT INTO comments (user_id, content, parent_id) VALUES ($user_id, '$content', $parent_id)";
    $conn->query($sql);
    $last_id = $conn->insert_id;

    $sql = "SELECT * FROM comments WHERE id=$last_id";
    $result = $conn->query($sql);
    $row = $result->fetch_assoc();
    $time = $row['created_at'];
    
    $sql = "SELECT * FROM users WHERE id=$user_id";
    $result = $conn->query($sql);
    $row = $result->fetch_assoc();
    $user = $row['nickname'];
    $conn->close();

    if($parent_id === '0'){
        $arr = array('result' => 'success', 'parentId' => $parent_id, 'id' => $last_id, 'time' => $time, 'user' => $user);
        echo json_encode($arr);
    }else{
        header('Location: index.php');
    }
?>