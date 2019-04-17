$(document).ready(() =>{
    $('form').submit((e)=>{
        const parentId = $(e.target).find('input[name=parent_id]').val();
        const content = $(e.target).find('textarea[name=content]').val();
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