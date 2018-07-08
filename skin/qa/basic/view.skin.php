<?php
if (!defined("_GNUBOARD_")) exit; //禁止单独访问此页
include_once(G5_LIB_PATH.'/thumbnail.lib.php');

// add_stylesheet('css 文本', 显示顺序); 数字越小的优先显示
add_stylesheet('<link rel="stylesheet" href="'.$qa_skin_url.'/style.css">', 0);
?>

<script src="<?php echo G5_JS_URL; ?>/viewimageresize.js"></script>

<!-- 主题浏览开始 { -->
<div id="bo_v_table"><?php echo $qaconfig['qa_title']; ?></div>

<article id="bo_v">
    <header>
        <h1 id="bo_v_title">
            <?php
            echo $view['category'].' | '; // 分类显示结束
            echo $view['subject']; // 标题显示
            ?>
        </h1>
    </header>

    <section id="bo_v_info">
        <h2>页面信息</h2>
        作者 <strong><?php echo $view['name'] ?></strong>
        <span class="sound_only">发布时间</span><strong><?php echo $view['datetime']; ?></strong>
    </section>

    <?php if($view['download_count']) { ?>
    <!-- 附件开始 { -->
    <section id="bo_v_file">
        <h2>附件</h2>
        <ul>
        <?php
        // 可变参数
        for ($i=0; $i<$view['download_count']; $i++) {
         ?>
            <li>
                <a href="<?php echo $view['download_href'][$i];  ?>" class="view_file_download">
                    <img src="<?php echo $qa_skin_url ?>/img/icon_file.gif" alt="附件">
                    <strong><?php echo $view['download_source'][$i] ?></strong>
                </a>
            </li>
        <?php
        }
         ?>
        </ul>
    </section>
    <!-- } 附件结束 -->
    <?php } ?>

    <?php if($view['email'] || $view['hp']) { ?>
    <section id="bo_v_contact">
        <h2>联系信息</h2>
        <dl>
            <?php if($view['email']) { ?>
            <dt>邮件地址</dt>
            <dd><?php echo $view['email']; ?></dd>
            <?php } ?>
            <?php if($view['hp']) { ?>
            <dt>手机</dt>
            <dd><?php echo $view['hp']; ?></dd>
            <?php } ?>
        </dl>
    </section>
    <?php } ?>

    <!-- 论坛顶部按钮开始 { -->
    <div id="bo_v_top">
        <?php
        ob_start();
         ?>
        <?php if ($prev_href || $next_href) { ?>
        <ul class="bo_v_nb">
            <?php if ($prev_href) { ?><li><a href="<?php echo $prev_href ?>" class="btn_b01">上一篇</a></li><?php } ?>
            <?php if ($next_href) { ?><li><a href="<?php echo $next_href ?>" class="btn_b01">下一篇</a></li><?php } ?>
        </ul>
        <?php } ?>

        <ul class="bo_v_com">
            <?php if ($update_href) { ?><li><a href="<?php echo $update_href ?>" class="btn_b01">修改</a></li><?php } ?>
            <?php if ($delete_href) { ?><li><a href="<?php echo $delete_href ?>" class="btn_b01" onclick="del(this.href); return false;">删除</a></li><?php } ?>
            <li><a href="<?php echo $list_href ?>" class="btn_b01">目录</a></li>
            <?php if ($write_href) { ?><li><a href="<?php echo $write_href ?>" class="btn_b02">发表主题</a></li><?php } ?>
        </ul>
        <?php
        $link_buttons = ob_get_contents();
        ob_end_flush();
         ?>
    </div>
    <!-- } 论坛顶部按钮结束 -->

    <section id="bo_v_atc">
        <h2 id="bo_v_atc_title">全文</h2>

        <?php
        // 显示附件
        if($view['img_count']) {
            echo "<div id=\"bo_v_img\">\n";

            for ($i=0; $i<$view['img_count']; $i++) {
                //echo $view['img_file'][$i];
                echo get_view_thumbnail($view['img_file'][$i], $qaconfig['qa_image_width']);
            }

            echo "</div>\n";
        }
         ?>

        <!-- 主题内容开始 { -->
        <div id="bo_v_con"><?php echo get_view_thumbnail($view['content'], $qaconfig['qa_image_width']); ?></div>
        <!-- } 主题内容结束 -->

        <?php if($view['qa_type']) { ?>
        <div id="bo_v_addq"><a href="<?php echo $rewrite_href; ?>" class="btn_b01">追加提问</a></div>
        <?php } ?>

    </section>

    <?php
    // 如提问已有答案则直接显示，如无且是管理员访问时显示回复输入框
    if(!$view['qa_type']) {
        if($view['qa_status'] && $answer['qa_id'])
            include_once($qa_skin_path.'/view.answer.skin.php');
        else
            include_once($qa_skin_path.'/view.answerform.skin.php');
    }
    ?>

    <?php if($view['rel_count']) { ?>
    <section id="bo_v_rel">
        <h2>关联提问</h2>

        <div class="tbl_head01 tbl_wrap">
            <table>
            <thead>
            <tr>
                <th scope="col">分类</th>
                <th scope="col">主题</th>
                <th scope="col">状态</th>
                <th scope="col">提交时间</th>
            </tr>
            </thead>
            <tbody>
            <?php
            for($i=0; $i<$view['rel_count']; $i++) {
            ?>
            <tr>
                <td class="td_category"><?php echo get_text($rel_list[$i]['category']); ?></td>
                <td>
                    <a href="<?php echo $rel_list[$i]['view_href']; ?>">
                        <?php echo $rel_list[$i]['subject']; ?>
                    </a>
                </td>
                <td class="td_stat <?php echo ($list[$i]['qa_status'] ? 'txt_done' : 'txt_rdy'); ?>"><?php echo ($rel_list[$i]['qa_status'] ? '已回复' : '等待回复'); ?></td>
                <td class="td_date"><?php echo $rel_list[$i]['date']; ?></td>
            </tr>
            <?php
            }
            ?>
            </tbody>
            </table>
        </div>
    </section>
    <?php } ?>

    <!-- 超链接输入及输出开始 { -->
    <div id="bo_v_bot">
        <?php echo $link_buttons ?>
    </div>
    <!-- } 超链接输入及输出结束 -->

</article>
<!-- } 论坛版块浏览结束 -->

<script>
$(function() {
    $("a.view_image").click(function() {
        window.open(this.href, "large_image", "location=yes,links=no,toolbar=no,top=10,left=10,width=10,height=10,resizable=yes,scrollbars=no,status=no");
        return false;
    });

    // 图片尺寸调整
    $("#bo_v_atc").viewimageresize();
});
</script>