<?php
if (!defined('_GNUBOARD_')) exit; //禁止单独访问此页

// add_stylesheet('css 文本', 显示顺序); 数字越小的优先显示
add_stylesheet('<link rel="stylesheet" href="'.$member_skin_url.'/style.css">', 0);
?>

<div id="profile" class="new_win mbskin">
    <h1 id="win_title"><?php echo $mb_nick ?>的介绍</h1>

    <div class="tbl_frm01 tbl_wrap">
        <table>
        <tbody>
        <tr>
            <th scope="row">会员等级</th>
            <td><?php echo $mb['mb_level'] ?></td>
        </tr>
        <tr>
            <th scope="row">积分</th>
            <td><?php echo number_format($mb['mb_point']) ?></td>
        </tr>
        <?php if ($mb_homepage) { ?>
        <tr>
            <th scope="row">网站主页</th>
            <td><a href="<?php echo $mb_homepage ?>" target="_blank"><?php echo $mb_homepage ?></a></td>
        </tr>
        <?php } ?>
        <tr>
            <th scope="row">注册时间</th>
            <td><?php echo ($member['mb_level'] >= $mb['mb_level']) ?  substr($mb['mb_datetime'],0,10) ." (".number_format($mb_reg_after)." 日)" : "无法获取"; ?></td>
        </tr>
        <tr>
            <th scope="row">最后登录</th>
            <td><?php echo ($member['mb_level'] >= $mb['mb_level']) ? $mb['mb_today_login'] : "无法获取"; ?></td>
        </tr>
        </tbody>
        </table>
    </div>

    <section>
        <h2>欢迎语</h2>
        <p><?php echo $mb_profile ?></p>
    </section>

    <div class="win_btn">
        <button type="button" onclick="window.close();">关闭窗口</button>
    </div>
</div>
