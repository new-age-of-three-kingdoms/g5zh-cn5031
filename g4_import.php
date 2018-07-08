<?php
include_once('./_common.php');
include_once(G5_LIB_PATH.'/connect.lib.php');
include_once(G5_LIB_PATH.'/outlogin.lib.php');

$g5['title'] = 'gnuboard4数据迁移';
include_once(G5_PATH.'/head.sub.php');

if(get_session('tables_copied') == 'done')
    alert('当前网站已执行数据迁移转化操作，重复执行该操作可能会发生严重错误', G5_URL);

if($is_admin != 'super')
    alert('请您登录网站管理员账号后进行操作', G5_URL);
?>

<style>
#g4_import p {padding:0 0 10px;line-height:1.8em}
#g4_import_frm {margin:20px 0 30px;padding:30px 0;border:1px solid #e9e9e9;background:#f5f8f9;text-align:center}
#g4_import_frm .frm_input {background-color:#fff !important}
#g4_import_frm .btn_submit {padding:0 10px;height:24px}
</style>

<!-- 顶部开始 { -->
<div id="hd">
    <h1 id="hd_h1"><?php echo $g5['title'] ?></h1>

    <div id="skip_to_container"><a href="#container">浏览全文</a></div>

    <div id="hd_wrapper">

        <div id="logo">
            <a href="<?php echo G5_URL ?>"><img src="<?php echo G5_IMG_URL ?>/logo.jpg" alt="<?php echo $config['cf_title']; ?>"></a>
        </div>

        <fieldset id="hd_sch">
            <legend>搜索全站</legend>
            <form name="fsearchbox" method="get" action="<?php echo G5_BBS_URL ?>/search.php" onsubmit="return fsearchbox_submit(this);">
            <input type="hidden" name="sfl" value="wr_subject||wr_content">
            <input type="hidden" name="sop" value="and">
            <label for="sch_stx" class="sound_only">关键词<strong class="sound_only"> 必选项</strong></label>
            <input type="text" name="stx" id="sch_stx" maxlength="20">
            <input type="submit" id="sch_submit" value="搜索">
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
        </fieldset>

        <ul id="tnb">
            <?php if ($is_member) {  ?>
            <?php if ($is_admin) {  ?>
            <li><a href="<?php echo G5_ADMIN_URL ?>"><b>管理员</b></a></li>
            <?php }  ?>
            <li><a href="<?php echo G5_BBS_URL ?>/member_confirm.php?url=<?php echo G5_BBS_URL ?>/register_form.php">信息设置</a></li>
            <li><a href="<?php echo G5_BBS_URL ?>/logout.php">注销</a></li>
            <?php } else {  ?>
            <li><a href="<?php echo G5_BBS_URL ?>/register.php">注册会员</a></li>
            <li><a href="<?php echo G5_BBS_URL ?>/login.php"><b>登录</b></a></li>
            <?php }  ?>
            <li><a href="<?php echo G5_BBS_URL ?>/qalist.php">在线咨询</a></li>
            <li><a href="<?php echo G5_BBS_URL ?>/current_connect.php">访问人数 <?php echo connect(); // 当前在线人数  ?></a></li>
            <li><a href="<?php echo G5_BBS_URL ?>/new.php">最新内容</a></li>
        </ul>

        <div id="text_size">
            <!-- font_resize('元件id', '需除去的class', '需添加的class'); -->
            <button id="size_down" onclick="font_resize('container', 'ts_up ts_up2', '');"><img src="<?php echo G5_URL; ?>/img/ts01.gif" alt="默认"></button>
            <button id="size_def" onclick="font_resize('container', 'ts_up ts_up2', 'ts_up');"><img src="<?php echo G5_URL; ?>/img/ts02.gif" alt="放大"></button>
            <button id="size_up" onclick="font_resize('container', 'ts_up ts_up2', 'ts_up2');"><img src="<?php echo G5_URL; ?>/img/ts03.gif" alt="最大"></button>
        </div>
    </div>

    <hr>

    <nav id="gnb">
        <h2>主要栏目</h2>
        <ul id="gnb_1dul">
            <li class="gnb_empty">不显示栏目</li>
        </ul>
    </nav>
</div>
<!-- } 顶部结束 -->

<hr>

<!-- 项目开始 { -->
<div id="wrapper">
    <div id="aside">
        <?php echo outlogin('basic'); // 引用登录  ?>
    </div>
    <div id="container">
        <?php if ((!$bo_table || $w == 's' ) && !defined("_INDEX_")) { ?><div id="container_title"><?php echo $g5['title'] ?></div><?php } ?>

        <div id="g4_import">
            <p>
                此程序需要在安装完成gnuboard5后立即执行<br>
                如用于已在运营且拥有数据的gnuboard5网站上运行此程序可能会发生严重的错误。<br>
                包括重复运行都会对当前已有数据发生严重错误，请务必仅执行一次。
            </p>
            <p>如需执行请确认gnuboard4 config.php文件路径后点击确定。</p>

            <form name="fimport" method="post" action="./g4_import_run.php" onsubmit="return fimport_submit(this);">
            <div id="g4_import_frm">
                <label for="file_path">config.php文件路径</label>
                <input type="text" name="file_path" id="file_path" required class="frm_input required">
                <input type="submit" value="确定" class="btn_submit">
            </div>
            </form>

            <p>
                路径是已gnuboard5的安装路径作为根路径、输入gnuboard4 config.php文件的相对路径<br>
                如果gnuboard4安装于网站根目录内，gnuboard5安装于名为G5的下级目录时所需要输入的路径是 ../config.php
            </p>

        </div>

        <script>
        function fimport_submit(f)
        {
            return confirm('确定执行gnuboard4数据迁移升级操作吗？');
        }
        </script>

    </div>
</div>

<!-- } 项目结束 -->

<hr>

<!-- 底部开始 { -->
<div id="ft">
    <div id="ft_catch"><img src="<?php echo G5_IMG_URL; ?>/ft.png" alt="<?php echo G5_VERSION ?>"></div>
    <div id="ft_copy">
        <p>
            Copyright &copy; <b>您的域名.</b> All rights reserved.<br>
            <a href="#">返回顶部</a>
        </p>
    </div>
</div>

<script>
$(function() {
    // 如有字体设定则执行
    font_resize("container", get_cookie("ck_font_resize_rmv_class"), get_cookie("ck_font_resize_add_class"));
});
</script>

<?php
include_once(G5_PATH.'/tail.sub.php');
?>