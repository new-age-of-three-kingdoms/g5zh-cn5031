<?php
if (!defined('_GNUBOARD_')) exit; //禁止单独访问此页
$delete_str = "";
if ($w == 'x') $delete_str = "评论";
if ($w == 'u') $g5['title'] = $delete_str."编辑/修改";
else if ($w == 'd' || $w == 'x') $g5['title'] = $delete_str."删除";
else $g5['title'] = $g5['title'];

// add_stylesheet('css 文本', 显示顺序); 数字越小的优先显示
add_stylesheet('<link rel="stylesheet" href="'.$member_skin_url.'/style.css">', 0);
?>

<!-- 密码重复确认开始 { -->
<div id="pw_confirm" class="mbskin">
    <h1><?php echo $g5['title'] ?></h1>
    <p>
        <?php if ($w == 'u') { ?>
        <strong>确认会员权限后才能进行修改</strong>
        如果您是本文作者请输入发表文章时设置的密码
        <?php } else if ($w == 'd' || $w == 'x') {  ?>
        <strong>只有作者本人才能删除</strong>
        如果您是本文作者请输入发表文章时设置的密码
        <?php } else {  ?>
        <strong>此内容为加密内容</strong>
        只有作者本人及指定管理员才能浏览
        <?php }  ?>
    </p>

    <form name="fboardpassword" action="<?php echo $action;  ?>" method="post">
    <input type="hidden" name="w" value="<?php echo $w ?>">
    <input type="hidden" name="bo_table" value="<?php echo $bo_table ?>">
    <input type="hidden" name="wr_id" value="<?php echo $wr_id ?>">
    <input type="hidden" name="comment_id" value="<?php echo $comment_id ?>">
    <input type="hidden" name="sfl" value="<?php echo $sfl ?>">
    <input type="hidden" name="stx" value="<?php echo $stx ?>">
    <input type="hidden" name="page" value="<?php echo $page ?>">

    <fieldset>
        <label for="pw_wr_password">密码<strong class="sound_only">必选项</strong></label>
        <input type="password" name="wr_password" id="password_wr_password" required class="frm_input required" size="15" maxLength="20">
        <input type="submit" value="确定" class="btn_submit">
    </fieldset>
    </form>

    <div class="btn_confirm">
        <a href="<?php echo $return_url ?>">返回</a>
    </div>

</div>
<!-- } 密码重复确认结束 -->