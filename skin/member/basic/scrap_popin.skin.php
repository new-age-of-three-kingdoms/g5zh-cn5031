<?php
if (!defined('_GNUBOARD_')) exit; //禁止单独访问此页

// add_stylesheet('css 文本', 显示顺序); 数字越小的优先显示
add_stylesheet('<link rel="stylesheet" href="'.$member_skin_url.'/style.css">', 0);
?>

<!-- 收藏开始 { -->
<div id="scrap_do" class="new_win mbskin">
    <h1 id="win_title">加入到收藏</h1>

    <form name="f_scrap_popin" action="./scrap_popin_update.php" method="post">
    <input type="hidden" name="bo_table" value="<?php echo $bo_table ?>">
    <input type="hidden" name="wr_id" value="<?php echo $wr_id ?>">

    <div class="tbl_frm01 tbl_wrap">
        <table>
        <caption>确认收藏并发表评论</caption>
        <tbody>
        <tr>
            <th scope="row">主题</th>
            <td><?php echo get_text(cut_str($write['wr_subject'], 255)) ?></td>
        </tr>
        <tr>
            <th scope="row"><label for="wr_content">评论</label></th>
            <td><textarea name="wr_content" id="wr_content"></textarea></td>
        </tr>
        </tbody>
        </table>
    </div>

    <p class="win_desc">
        您可以在收藏同时快捷发表评论
    </p>

    <div class="win_btn">
        <input type="submit" value="加入收藏" class="btn_submit">
    </div>
    </form>
</div>
<!-- } 收藏结束 -->