<?php
if (!defined('_GNUBOARD_')) exit; //禁止单独访问此页

// add_stylesheet('css 文本', 显示顺序); 数字越小的优先显示
add_stylesheet('<link rel="stylesheet" href="'.$qa_skin_url.'/style.css">', 0);
?>

<div id="bo_list">
    <?php if ($category_option) { ?>
    <!-- 分类开始 { -->
    <nav id="bo_cate">
        <h2><?php echo $qaconfig['qa_title'] ?> 分类</h2>
        <ul id="bo_cate_ul">
            <?php echo $category_option ?>
        </ul>
    </nav>
    <!-- } 分类结束 -->
    <?php } ?>

     <!-- 论坛版块页面信息及按钮部分开始 { -->
    <div class="bo_fx">
        <div id="bo_list_total">
            <span>Total <?php echo number_format($total_count) ?>件</span>
            <?php echo $page ?> 页
        </div>

        <?php if ($admin_href || $write_href) { ?>
        <ul class="btn_bo_user">
            <?php if ($admin_href) { ?><li><a href="<?php echo $admin_href ?>" class="btn_admin">管理员</a></li><?php } ?>
            <?php if ($write_href) { ?><li><a href="<?php echo $write_href ?>" class="btn_b02">提问&咨询</a></li><?php } ?>
        </ul>
        <?php } ?>
    </div>
    <!-- } 论坛版块页面信息及按钮部分结束 -->

    <form name="fqalist" id="fqalist" action="./qadelete.php" onsubmit="return fqalist_submit(this);" method="post">
    <input type="hidden" name="stx" value="<?php echo $stx; ?>">
    <input type="hidden" name="sca" value="<?php echo $sca; ?>">
    <input type="hidden" name="page" value="<?php echo $page; ?>">

    <?php if ($is_checkbox) { ?>
    <div id="list_chk">
        <label for="chkall" class="sound_only">主题 全部</label>
        <input type="checkbox" id="chkall" onclick="if (this.checked) all_checked(true); else all_checked(false);">
    </div>
    <?php } ?>

    <div class="ul_wrap ul_01">
        <ul>
            <?php
            for ($i=0; $i<count($list); $i++) {
            ?>
            <li class="bo_li<?php if ($is_checkbox) echo ' bo_adm'; ?>">
                <?php if ($is_checkbox) { ?>
                <div class="li_chk">
                    <label for="chk_qa_id_<?php echo $i ?>" class="sound_only"><?php echo $list[$i]['subject']; ?></label>
                    <input type="checkbox" name="chk_qa_id[]" value="<?php echo $list[$i]['qa_id'] ?>" id="chk_qa_id_<?php echo $i ?>">
                </div>
                <?php } ?>
                <div class="li_title">
                    <a href="<?php echo $list[$i]['view_href']; ?>">
                        <strong><?php echo $list[$i]['category']; ?></strong>
                        <?php echo $list[$i]['subject']; ?>
                    </a>
                </div>
                <div class="li_info">
                    <span><?php echo $list[$i]['num']; ?></span>
                    <span><?php echo $list[$i]['name']; ?></span>
                    <span><?php echo $list[$i]['date']; ?></span>
                    <span><?php echo $list[$i]['icon_file']; ?></span>
                </div>
                <div class="li_stat <?php echo ($list[$i]['qa_status'] ? 'txt_done' : 'txt_rdy'); ?>"><?php echo ($list[$i]['qa_status'] ? '已回复' : '等待回复'); ?></div>
            </li>
            <?php
            }
            ?>

            <?php if ($i == 0) { echo '<li class="empty_list">没有主题内容可以显示</li>'; } ?>
        </ul>
    </div>

    <div class="bo_fx">
        <?php if ($is_checkbox) { ?>
        <ul class="btn_bo_adm">
            <li><input type="submit" name="btn_submit" value="删除所选" onclick="document.pressed=this.value"></li>
        </ul>
        <?php } ?>

        <ul class="btn_bo_user">
            <?php if ($list_href) { ?><li><a href="<?php echo $list_href ?>" class="btn_b01">目录</a></li><?php } ?>
            <?php if ($write_href) { ?><li><a href="<?php echo $write_href ?>" class="btn_b02">提问&咨询</a></li><?php } ?>
        </ul>
    </div>
    </form>
</div>

<?php if($is_checkbox) { ?>
<noscript>
<p>浏览器中屏蔽javascript代码运行时<br>将不会出现操作提示直接执行所选操作，请务必谨慎操作！</p>
</noscript>
<?php } ?>

<!-- 页 -->
<?php echo $list_pages;  ?>

<!-- 论坛版块搜索开始 { -->
<fieldset id="bo_sch">
    <legend>搜索主题</legend>

    <form name="fsearch" method="get">
    <input type="hidden" name="sca" value="<?php echo $sca ?>">
    <label for="stx" class="sound_only">关键词<strong class="sound_only"> 必选项</strong></label>
    <input type="text" name="stx" value="<?php echo stripslashes($stx) ?>" required id="stx" class="frm_input required" size="15" maxlength="15">
    <input type="submit" value="搜索" class="btn_submit">
    </form>
</fieldset>
<!-- } 论坛版块搜索结束 -->

<?php if ($is_checkbox) { ?>
<script>
function all_checked(sw) {
    var f = document.fqalist;

    for (var i=0; i<f.length; i++) {
        if (f.elements[i].name == "chk_qa_id[]")
            f.elements[i].checked = sw;
    }
}

function fqalist_submit(f) {
    var chk_count = 0;

    for (var i=0; i<f.length; i++) {
        if (f.elements[i].name == "chk_qa_id[]" && f.elements[i].checked)
            chk_count++;
    }

    if (!chk_count) {
        alert("请选择需要"+document.pressed+"内容");
        return false;
    }

    if(document.pressed == "删除所选") {
        if (!confirm("确定删除所选内容吗？\n\n删除后的内容将无法进行恢复"))
            return false;
    }

    return true;
}
</script>
<?php } ?>
<!-- } 论坛版块目录结束 -->