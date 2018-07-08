<?php
if (!defined("_GNUBOARD_")) exit; //禁止单独访问此页

// add_stylesheet('css 文本', 显示顺序); 数字越小的优先显示
add_stylesheet('<link rel="stylesheet" href="'.$poll_skin_url.'/style.css">', 0);
?>

<form name="fpoll" action="<?php echo G5_BBS_URL ?>/poll_update.php" onsubmit="return fpoll_submit(this);" method="post">
<input type="hidden" name="po_id" value="<?php echo $po_id ?>">
<input type="hidden" name="skin_dir" value="<?php echo $skin_dir ?>">
<aside id="poll">
    <header>
        <h2>在线投票</h2>
        <?php if ($is_admin == "super") { ?><a href="<?php echo G5_ADMIN_URL ?>/poll_form.php?w=u&amp;po_id=<?php echo $po_id ?>" class="btn_admin">投票管理</a><?php } ?>
        <p><?php echo $po['po_subject'] ?></p>
    </header>
    <ul>
        <?php for ($i=1; $i<=9 && $po["po_poll{$i}"]; $i++) { ?>
        <li><input type="radio" name="gb_poll" value="<?php echo $i ?>" id="gb_poll_<?php echo $i ?>"> <label for="gb_poll_<?php echo $i ?>"><?php echo $po['po_poll'.$i] ?></label></li>
        <?php } ?>
    </ul>
    <footer>
        <input type="submit" value="投票">
        <a href="<?php echo G5_BBS_URL."/poll_result.php?po_id=$po_id&amp;skin_dir=$skin_dir" ?>" target="_blank" onclick="poll_result(this.href); return false;">查看结果</a>
    </footer>
</aside>
</form>

<script>
function fpoll_submit(f)
{
    <?php
    if ($member['mb_level'] < $po['po_level'])
        echo " alert('会员等级{$po['po_level']}级以上的会员才能参与投票'); return false; ";
    ?>

    var chk = false;
    for (i=0; i<f.gb_poll.length;i ++) {
        if (f.gb_poll[i].checked == true) {
            chk = f.gb_poll[i].value;
            break;
        }
    }

    if (!chk) {
        alert("请选择需要投票的项目");
        return false;
    }

    var new_win = window.open("about:blank", "win_poll", "width=616,height=500,scrollbars=yes,resizable=yes");
    f.target = "win_poll";

    return true;
}

function poll_result(url)
{
    <?php
    if ($member['mb_level'] < $po['po_level'])
        echo " alert('会员等级{$po['po_level']}级以上的会员才能查看投票结果'); return false; ";
    ?>

    win_poll(url);
}
</script>