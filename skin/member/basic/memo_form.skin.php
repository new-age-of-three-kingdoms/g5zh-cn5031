<?php
if (!defined('_GNUBOARD_')) exit; //禁止单独访问此页

// add_stylesheet('css 文本', 显示顺序); 数字越小的优先显示
add_stylesheet('<link rel="stylesheet" href="'.$member_skin_url.'/style.css">', 0);
?>

<!-- 发送短信开始 { -->
<div id="memo_write" class="new_win mbskin">
    <h1 id="win_title">发送短信</h1>

    <ul class="win_ul">
        <li><a href="./memo.php?kind=recv">收件箱</a></li>
        <li><a href="./memo.php?kind=send">发件箱</a></li>
        <li><a href="./memo_form.php">写短信</a></li>
    </ul>

    <form name="fmemoform" action="<?php echo $memo_action_url; ?>" onsubmit="return fmemoform_submit(this);" method="post" autocomplete="off">
    <div class="tbl_frm01 tbl_wrap">
        <table>
        <caption>写短信</caption>
        <tbody>
        <tr>
            <th scope="row"><label for="me_recv_mb_id">收件会员ID<strong class="sound_only">必选项</strong></label></th>
            <td>
                <input type="text" name="me_recv_mb_id" value="<?php echo $me_recv_mb_id ?>" id="me_recv_mb_id" required class="frm_input required" size="47">
                <span class="frm_info">如果需要抄送多个会员请使用逗号(,)区分多个会员ID</span>
            </td>
        </tr>
        <tr>
            <th scope="row"><label for="me_memo">内容</label></th>
            <td><textarea name="me_memo" id="me_memo" required class="required"><?php echo $content ?></textarea></td>
        </tr>
        <tr>
            <th scope="row">验证码</th>
            <td>
                <?php echo captcha_html(); ?>
            </td>
        </tr>
        </tbody>
        </table>
    </div>

    <div class="win_btn">
        <input type="submit" value="发送" id="btn_submit" class="btn_submit">
        <button type="button" onclick="window.close();">关闭窗口</button>
    </div>
    </form>
</div>

<script>
function fmemoform_submit(f)
{
    <?php echo chk_captcha_js();  ?>

    return true;
}
</script>
<!-- } 发送短信结束 -->