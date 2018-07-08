<?php
if (!defined("_GNUBOARD_")) exit; //禁止单独访问此页

// add_stylesheet('css 文本', 显示顺序); 数字越小的优先显示
add_stylesheet('<link rel="stylesheet" href="'.$outlogin_skin_url.'/style.css">', 0);
?>

<!-- 引用登录皮肤-登录后状态开始 { -->
<section id="ol_after" class="ol">
    <header id="ol_after_hd">
        <h2>我的账户</h2>
        <strong>会员:<?php echo $nick ?></strong>
        <?php if ($is_admin == 'super' || $is_auth) {  ?><a href="<?php echo G5_ADMIN_URL ?>" class="btn_admin">网站管理</a><?php }  ?>
    </header>
    <ul id="ol_after_private">
        <li>
            <a href="<?php echo G5_BBS_URL ?>/memo.php" target="_blank" id="ol_after_memo" class="win_memo">
                <span class="sound_only">未读</span>短信
                <strong><?php echo $memo_not_read ?></strong>
            </a>
        </li>
        <li>
            <a href="<?php echo G5_BBS_URL ?>/point.php" target="_blank" id="ol_after_pt" class="win_point">
                积分
                <strong><?php echo $point ?></strong>
            </a>
        </li>
        <li>
            <a href="<?php echo G5_BBS_URL ?>/scrap.php" target="_blank" id="ol_after_scrap" class="win_scrap">收藏</a>
        </li>
    </ul>
    <footer id="ol_after_ft">
        <a href="<?php echo G5_BBS_URL ?>/member_confirm.php?url=register_form.php" id="ol_after_info">信息设置</a>
        <a href="<?php echo G5_BBS_URL ?>/logout.php" id="ol_after_logout">注销</a>
    </footer>
</section>

<script>
// 如果需要使用删除账户功能请使用以下代码
function member_leave()
{
    if (confirm("您确定需要删除本站账户资料吗？"))
        location.href = "<?php echo G5_BBS_URL ?>/member_confirm.php?url=member_leave.php";
}
</script>
<!-- } 引用登录皮肤-登录后状态结束 -->
