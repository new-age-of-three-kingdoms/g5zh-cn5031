<?php
if (!defined('_GNUBOARD_')) exit; //禁止单独访问此页

// add_stylesheet('css 文本', 显示顺序); 数字越小的优先显示
add_stylesheet('<link rel="stylesheet" href="'.$faq_skin_url.'/style.css">', 0);

if ($admin_href)
    echo '<div class="faq_admin"><a href="'.$admin_href.'" class="btn_admin">FAQ编辑</a></div>';
?>

<!-- FAQ开始 { -->
<?php
if ($himg_src)
    echo '<div id="faq_himg" class="faq_img"><img src="'.$himg_src.'" alt=""></div>';

// 顶部HTML
echo '<div id="faq_hhtml">'.conv_content($fm['fm_head_html'], 1).'</div>';
?>

<?php
if( count($faq_master_list) ){
?>
<nav id="bo_cate">
    <h2>常见问题分类</h2>
    <ul id="bo_cate_ul">
        <?php
        foreach( $faq_master_list as $v ){
            $category_msg = '';
            $category_option = '';
            if($v['fm_id'] == $fm_id){ // 如果是当前所选分类时
                $category_option = ' id="bo_cate_on"';
                $category_msg = '<span class="sound_only">开启分类 </span>';
            }
        ?>
        <li><a href="<?php echo $category_href;?>?fm_id=<?php echo $v['fm_id'];?>" <?php echo $category_option;?> ><?php echo $category_msg.$v['fm_subject'];?></a></li>
        <?php
        }
        ?>
    </ul>
</nav>
<?php } ?>

<div id="faq_wrap" class="faq_<?php echo $fm_id; ?>">
    <?php // FAQ内容
    if( count($faq_list) ){
    ?>
    <section id="faq_con">
        <h2><?php echo $g5['title']; ?> 目录</h2>
        <ol>
            <?php
            foreach($faq_list as $key=>$v){
                if(empty($v))
                    continue;
            ?>
            <li>
                <h3><a href="#none" onclick="return faq_open(this);"><?php echo conv_content($v['fa_subject'], 1); ?></a></h3>
                <div class="con_inner">
                    <?php echo conv_content($v['fa_content'], 1); ?>
                    <div class="con_closer"><button type="button" class="closer_btn">关闭</button></div>
                </div>
            </li>
            <?php
            }
            ?>
        </ol>
    </section>
    <?php

    } else {
        if($stx){
            echo '<p class="empty_list">未找到主题内容</p>';
        } else {
            echo '<div class="empty_list">未找到常见问题';
            if($is_admin)
                echo '<br><a href="'.G5_ADMIN_URL.'/faqmasterlist.php">新建FAQ请通过FAQ管理</a>菜单进行设置';
            echo '</div>';
        }
    }
    ?>
</div>

<?php echo get_paging($page_rows, $page, $total_page, $_SERVER['PHP_SELF'].'?'.$qstr.'&amp;page='); ?>

<?php
// 底部HTML
echo '<div id="faq_thtml">'.conv_content($fm['fm_tail_html'], 1).'</div>';

if ($timg_src)
    echo '<div id="faq_timg" class="faq_img"><img src="'.$timg_src.'" alt=""></div>';
?>

<fieldset id="faq_sch">
    <legend>FAQ搜索</legend>

    <form name="faq_search_form" method="get">
    <input type="hidden" name="fm_id" value="<?php echo $fm_id;?>">
    <label for="stx" class="sound_only">关键词<strong class="sound_only"> 必选项</strong></label>
    <input type="text" name="stx" value="<?php echo $stx;?>" required id="stx" class="frm_input required" size="15" maxlength="15">
    <input type="submit" value="搜索" class="btn_submit">
    </form>
</fieldset>
<!-- } FAQ结束 -->

<?php
if ($admin_href)
    echo '<div class="faq_admin"><a href="'.$admin_href.'" class="btn_admin">FAQ编辑</a></div>';
?>

<script src="<?php echo G5_JS_URL; ?>/viewimageresize.js"></script>
<script>
$(function() {
    $(".closer_btn").on("click", function() {
        $(this).closest(".con_inner").slideToggle();
    });
});

function faq_open(el)
{
    var $con = $(el).closest("li").find(".con_inner");

    if($con.is(":visible")) {
        $con.slideUp();
    } else {
        $("#faq_con .con_inner:visible").css("display", "none");

        $con.slideDown(
            function() {
                // 图片尺寸调整
                $con.viewimageresize2();
            }
        );
    }

    return false;
}
</script>