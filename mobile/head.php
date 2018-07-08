<?php
if (!defined('_GNUBOARD_')) exit; //禁止单独访问此页

include_once(G5_PATH.'/head.sub.php');
include_once(G5_LIB_PATH.'/latest.lib.php');
include_once(G5_LIB_PATH.'/outlogin.lib.php');
include_once(G5_LIB_PATH.'/poll.lib.php');
include_once(G5_LIB_PATH.'/visit.lib.php');
include_once(G5_LIB_PATH.'/connect.lib.php');
include_once(G5_LIB_PATH.'/popular.lib.php');
?>

<header id="hd">
    <h1 id="hd_h1"><?php echo $g5['title'] ?></h1>

    <div class="to_content"><a href="#container">浏览全文</a></div>

    <?php
    if(defined('_INDEX_')) { // 仅在index页面执行
        include G5_MOBILE_PATH.'/newwin.inc.php'; // 弹出层
    } ?>

    <div id="hd_wrapper">

        <div id="logo">
            <a href="<?php echo G5_URL ?>"><img src="<?php echo G5_IMG_URL ?>/logo.jpg" alt="<?php echo $config['cf_title']; ?>"></a>
        </div>

        <button type="button" id="gnb_open" class="hd_opener">栏目<span class="sound_only"> 打开</span></button>

        <div id="gnb" class="hd_div">
            <ul id="gnb_1dul">
            <?php
            $sql = " select *
                        from {$g5['menu_table']}
                        where me_mobile_use = '1'
                          and length(me_code) = '2'
                        order by me_order, me_id ";
            $result = sql_query($sql, false);

            for($i=0; $row=sql_fetch_array($result); $i++) {
            ?>
                <li class="gnb_1dli">
                    <a href="<?php echo $row['me_link']; ?>" target="_<?php echo $row['me_target']; ?>" class="gnb_1da"><?php echo $row['me_name'] ?></a>
                    <?php
                    $sql2 = " select *
                                from {$g5['menu_table']}
                                where me_mobile_use = '1'
                                  and length(me_code) = '4'
                                  and substring(me_code, 1, 2) = '{$row['me_code']}'
                                order by me_order, me_id ";
                    $result2 = sql_query($sql2);

                    for ($k=0; $row2=sql_fetch_array($result2); $k++) {
                        if($k == 0)
                            echo '<ul class="gnb_2dul">'.PHP_EOL;
                    ?>
                        <li class="gnb_2dli"><a href="<?php echo $row2['me_link']; ?>" target="_<?php echo $row2['me_target']; ?>" class="gnb_2da"><span></span><?php echo $row2['me_name'] ?></a></li>
                    <?php
                    }

                    if($k > 0)
                        echo '</ul>'.PHP_EOL;
                    ?>
                </li>
            <?php
            }

            if ($i == 0) {  ?>
                <li id="gnb_empty">栏目正在准备中<?php if ($is_admin) { ?> <br><a href="<?php echo G5_ADMIN_URL; ?>/menu_list.php">请登录管理员账号后进入 &gt; 网站设置 &gt; 栏目设置</a>进行编辑<?php } ?></li>
            <?php } ?>
            </ul>
            <button type="button" id="gnb_close" class="hd_closer"><span class="sound_only">栏目 </span>关闭</button>
        </div>

        <button type="button" id="hd_sch_open" class="hd_opener">搜索<span class="sound_only"> 打开</span></button>

        <div id="hd_sch" class="hd_div">
            <h2>搜索全站</h2>
            <form name="fsearchbox" action="<?php echo G5_BBS_URL ?>/search.php" onsubmit="return fsearchbox_submit(this);" method="get">
            <input type="hidden" name="sfl" value="wr_subject||wr_content">
            <input type="hidden" name="sop" value="and">
            <input type="text" name="stx" id="sch_stx" placeholder="关键词(必选项)" required class="required" maxlength="20">
            <input type="submit" value="搜索" id="sch_submit">
            </form>

            <script>
            function fsearchbox_submit(f)
            {
                if (f.stx.value.length < 2) {
                    alert("请至少输入两个字以上关键词进行搜索");
                    f.stx.select();
                    f.stx.focus();
                    return false;
                }

                // 如果产生负荷请注释以下代码
                var cnt = 0;
                for (var i=0; i<f.stx.value.length; i++) {
                    if (f.stx.value.charAt(i) == ' ')
                        cnt++;
                }

                if (cnt > 1) {
                    alert("为了精确搜索请勿使用多个空格联想条件");
                    f.stx.select();
                    f.stx.focus();
                    return false;
                }

                return true;
            }
            </script>
            <button type="button" id="sch_close" class="hd_closer"><span class="sound_only">搜索 </span>关闭</button>
        </div>

        <script>
        $(function () {
            $(".hd_opener").on("click", function() {
                var $this = $(this);
                var $hd_layer = $this.next(".hd_div");

                if($hd_layer.is(":visible")) {
                    $hd_layer.hide();
                    $this.find("span").text("打开");
                } else {
                    var $hd_layer2 = $(".hd_div:visible");
                    $hd_layer2.prev(".hd_opener").find("span").text("打开");
                    $hd_layer2.hide();

                    $hd_layer.show();
                    $this.find("span").text("关闭");
                }
            });

            $(".hd_closer").on("click", function() {
                var idx = $(".hd_closer").index($(this));
                $(".hd_div:visible").hide();
                $(".hd_opener:eq("+idx+")").find("span").text("打开");
            });
        });
        </script>

        <ul id="hd_nb">
            <li><a href="<?php echo G5_BBS_URL ?>/qalist.php" id="snb_new">在线咨询</a></li>
            <li><a href="<?php echo G5_BBS_URL ?>/faq.php" id="snb_faq">FAQ</a></li>
            <li><a href="<?php echo G5_BBS_URL ?>/current_connect.php" id="snb_cnt">访问人数 <?php echo connect(); // 当前在线人数 ?></a></li>
            <li><a href="<?php echo G5_BBS_URL ?>/new.php" id="snb_new">新主题</a></li>
            <?php if ($is_member) { ?>
            <?php if ($is_admin) { ?>
            <li><a href="<?php echo G5_ADMIN_URL ?>" id="snb_adm"><b>管理员</b></a></li>
            <?php } ?>
            <li><a href="<?php echo G5_BBS_URL ?>/member_confirm.php?url=<?php echo G5_BBS_URL ?>/register_form.php" id="snb_modify">信息设置</a></li>
            <li><a href="<?php echo G5_BBS_URL ?>/logout.php" id="snb_logout">注销</a></li>
            <?php } else { ?>
            <li><a href="<?php echo G5_BBS_URL ?>/register.php" id="snb_join">注册会员</a></li>
            <li><a href="<?php echo G5_BBS_URL ?>/login.php" id="snb_login">登录</a></li>
            <?php } ?>
        </ul>

    </div>
</header>

<hr>

<div id="wrapper">
    <div id="aside">
        <?php echo outlogin('basic'); // 引用登录 ?>
    </div>
    <div id="container">
        <?php if ((!$bo_table || $w == 's' ) && !defined("_INDEX_")) { ?><div id="container_title"><?php echo $g5['title'] ?></div><?php } ?>
        <div id="text_size">
            <!-- font_resize('元件id', '需除去的class', '需添加的class'); -->
            <button id="size_down" onclick="font_resize('container', 'ts_up ts_up2', '');"><img src="<?php echo G5_URL; ?>/img/ts01.gif" alt="默认"></button>
            <button id="size_def" onclick="font_resize('container', 'ts_up ts_up2', 'ts_up');"><img src="<?php echo G5_URL; ?>/img/ts02.gif" alt="放大"></button>
            <button id="size_up" onclick="font_resize('container', 'ts_up ts_up2', 'ts_up2');"><img src="<?php echo G5_URL; ?>/img/ts03.gif" alt="最大"></button>
        </div>