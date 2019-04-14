<?php
    require_once("conn.php");

    $is_login = false;
    $user_id = '';
    if(isset($_COOKIE["session_id"]) && !empty($_COOKIE["session_id"])){
        $session_id = $_COOKIE["session_id"];
        $sql = "SELECT * FROM users_certificate WHERE session_id ='$session_id'";
        $result = $conn->query($sql);
        if($result->num_rows > 0){
            $is_login = true;
            $row = $result->fetch_assoc();
            $user_id = $row['user_id'];
        }
    }else{

    }
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Message Board</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" type="text/css" media="screen" href="board.css">
    <script src="https://code.jquery.com/jquery-3.4.0.min.js"></script>
    <script>
        $(document).ready(function(){
            $('form').submit(function(e){
                const parentId = $(e.target).find('input[name=parent_id]').val();
                let content = $(e.target).find('textarea[name=content]').val();
                const userId = $(e.target).find('input[name=user_id]').val();

                if(parentId === '0'){
                    e.preventDefault();
                }else{
                    return;
                }
                // 送request到server
                $.ajax({
                    type: 'POST',
                    url: 'add_comment.php',
                    data: {
                        content: content,
                        parent_id: parentId,
                        user_id: userId
                    },
                    success: function(resp){
                        console.log(resp);
                        var res = JSON.parse(resp);
                        console.log(res.parentId);
                        // 前後端可溝通串資料了!
                        if(res.result === 'success'){
                            
                            $('.board__comments').prepend(`
                            <div class="comment">
                                <div class="comment__header">
                                    <div class="comment__author">${res.user}</div>
                                    <div class="comment__time">${res.time}</div>
                                </div>
                                <div class="comment__content">
                                    ${content}
                                </div>                                                         
                                <div class="board__subcomments">
                                    <div class="board__form">
                                        <form method="POST" action="add_comment.php">
                                            <div class="board__form-textarea">
                                                <textarea name="content" placeholder="留言..." ></textarea>
                                            </div>
                                            <input type="hidden" name="parent_id" value="${res.id}" />
                                            <input type="hidden" name="user_id" value="${userId}" />
                                            <input type='submit' class='board__form-submit' value='送出' />
                                        </form>
                                    </div> 
                                </div>
                            </div>
                            `)
    
                            $(e.target).find('textarea[name=content]').val('');
                            
                        }
                    }
                });
            })
        })
    </script>
</head>
<body>
    <div class="board__main" >
        <?
            if(!$is_login){
        ?>
            <a href="login.php">登入</a>
            <a href="register.php">註冊</a>
        <?
            }else{
        ?>
            <a href="logout.php">登出</a>
        <?
            }
        ?>
        
        <h1 class="board__title">
            留言板
        </h1>
        <div class="board__form">
            <form method="POST" action="add_comment.php">
                <div class="board__form-textarea">
                    <textarea name="content" placeholder="留言..." ></textarea>
                </div>
                <input type="hidden" name="parent_id" value="0" />
                <input type="hidden" name="user_id" value="<? echo $user_id ?>" />
                <?php
                    if($is_login){
                        echo "<input type='submit' class='board__form-submit' value='送出' />";
                    }else{
                        echo "<input type='submit' class='board__form-submit' value='請先登入' disabled />";
                    }
                ?>
                
            </form>
        </div> 
        <div class="board__comments">
<?php
    // 顯示所有留言
    require_once('conn.php');
    // 列出所有parent_id=0的文、且取得每篇文章作者的nickname
    $sql = "SELECT comments.id,comments.content,comments.created_at,users.nickname FROM comments left join users ON comments.user_id = users.id WHERE parent_id=0 order by created_at DESC";
    $result = $conn->query($sql);
    while($row = $result->fetch_assoc()){
?>
            <div class="comment">
                <div class="comment__header">
                    <div class="comment__author"><? echo $row['nickname'] ?></div>
                    <div class="comment__time"><? echo $row['created_at'] ?></div>
                </div>
                <div class="comment__content">
                    <? echo htmlspecialchars($row['content'], ENT_QUOTES, 'utf-8') ?>
                </div>
                <div class="board__subcomments">
<?php
    // 顯示所有子留言
    require_once('conn.php');
    $parent_id = $row['id'];
    $sql_child = "SELECT comments.*, users.nickname FROM comments left join users ON comments.user_id=users.id WHERE parent_id=$parent_id order by created_at DESC";
    $result_child = $conn->query($sql_child);
    while($sub_comment = $result_child->fetch_assoc()){
?>
                <div class="comment">
                    <div class="comment__header">
                        <div class="comment__author"><? echo $sub_comment['nickname'] ?></div>
                        <div class="comment__time"><? echo $sub_comment['created_at'] ?></div>
                    </div>
                    <div class="comment__content">
                        <? echo htmlspecialchars($sub_comment['content'], ENT_QUOTES, 'utf-8') ?>
                    </div>
                </div>
<? } ?>
                <div class="board__form">
                    <form method="POST" action="add_comment.php">
                        <div class="board__form-textarea">
                            <textarea name="content" placeholder="留言..." ></textarea>
                        </div>
                        <input type="hidden" name="parent_id" value="<? echo $row['id'] ?>" />
                        <input type="hidden" name="user_id" value="<? echo $user_id ?>" />
                        <?php
                        if($is_login){
                            echo "<input type='submit' class='board__form-submit' value='送出' />";
                        }else{
                            echo "<input type='submit' class='board__form-submit' value='請先登入' disabled />";
                        }
                        ?>
                    </form>
                </div> 
            </div>
    </div>
<? } ?>
        </div>
    </div>
    
</body>
</html>