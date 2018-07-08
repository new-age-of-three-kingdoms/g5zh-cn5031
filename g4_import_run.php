<?php
include_once('./_common.php');

ob_end_clean();

include_once(G5_LIB_PATH.'/connect.lib.php');
include_once(G5_LIB_PATH.'/outlogin.lib.php');

set_time_limit ( 0 );
ini_set('memory_limit', '50M');

$g5['title'] = 'gnuboard4数据迁移';
include_once(G5_PATH.'/head.sub.php');

echo '<link rel="stylesheet" href="'.G5_URL.'/g4_import.css">';

if(empty($_POST))
    alert('请使用正常方式访问', G5_URL);

if(get_session('tables_copied') == 'done')
    alert('当前网站已执行数据迁移转化操作，重复执行该操作可能会发生严重错误', G5_URL);

if($is_admin != 'super')
    alert('请您登录网站管理员账号后进行操作', G5_URL);

$g4_config_file = trim($_POST['file_path']);

if(!$g4_config_file)
    alert('请设置config.php文件路径');

if(!is_file($g4_config_file))
    alert('config.php文件路径设置错误，未找到对应文件');

$is_euckr = false;
?>
<script>
// 防止刷新
function noRefresh()
{
    /* 屏蔽CTRL + N键. */
    if ((event.keyCode == 78) && (event.ctrlKey == true))
    {
        event.keyCode = 0;
        return false;
    }
    /* 屏蔽 F5键. */
    if(event.keyCode == 116)
    {
        event.keyCode = 0;
        return false;
    }
}

document.onkeydown = noRefresh ;
</script>

<style>
#g4_import_run {}
#g4_import_run ol {margin: 0;padding: 0 0 0 25px;border: 1px solid #E9E9E9;border-bottom: 0;background: #f5f8f9;list-style:none;zoom:1}
#g4_import_run li {padding:7px 10px;border-bottom:1px solid #e9e9e9}
#g4_import_run #run_msg {padding:30px 0;text-align:center}
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

        <div id="g4_import_run">
            <ol>
        <?php
        flush();

        // g4的 confing.php
        require($g4_config_file);

        if(preg_replace('/[^a-z]/', '', strtolower($g4['charset'])) == 'euckr')
            $is_euckr = true;

        // member table 复制
        $columns = array();
        $fields = mysql_list_fields(G5_MYSQL_DB, $g5['member_table']);
        $count = mysql_num_fields($fields);
        for ($i = 0; $i < $count; $i++) {
            $fld = mysql_field_name($fields, $i);
            $columns[] = $fld;
        }

        $sql = " select * from {$g4['member_table']} ";
        $result = sql_query($sql);
        for($i=0; $row=sql_fetch_array($result); $i++) {
            if($is_euckr)
                $row = array_map('iconv_utf8', $row);

            // 重复检查
            $sql2 = " select count(*) as cnt from {$g5['member_table']} where mb_id = '{$row['mb_id']}' ";
            $row2 = sql_fetch($sql2);
            if($row2['cnt'])
                continue;

            $comma = '';
            $sql_common = '';

            foreach($row as $key=>$val) {
                if($key == 'mb_no')
                    continue;

                if(!in_array($key, $columns))
                    continue;

                $sql_common .= $comma . " $key = '".addslashes($val)."' ";

                $comma = ',';
            }

            sql_query(" INSERT INTO {$g5['member_table']} SET $sql_common ");
        }

        echo '<li>member table 复制</li>'.PHP_EOL;
        unset($columns);
        unset($fiels);

        // point table 复制
        $sql = " select * from {$g4['point_table']} ";
        $result = sql_query($sql);
        for($i=0; $row=sql_fetch_array($result); $i++) {
            if($is_euckr)
                $row = array_map('iconv_utf8', $row);

            $comma = '';
            $sql_common = '';

            foreach($row as $key=>$val) {
                if($key == 'po_id')
                    continue;

                $sql_common .= $comma . " $key = '".addslashes($val)."' ";

                $comma = ',';
            }

            sql_query(" INSERT INTO {$g5['point_table']} SET $sql_common ");
        }
        echo '<li>point table 复制</li>'.PHP_EOL;

        // login table 复制
        $sql = " select * from {$g4['login_table']} ";
        $result = sql_query($sql);
        for($i=0; $row=sql_fetch_array($result); $i++) {
            if($is_euckr)
                $row = array_map('iconv_utf8', $row);

            // 重复检查
            $sql2 = " select count(*) as cnt from {$g5['login_table']} where lo_ip = '{$row['lo_ip']}' ";
            $row2 = sql_fetch($sql2);
            if($row2['cnt'])
                continue;

            $comma = '';
            $sql_common = '';

            foreach($row as $key=>$val) {
                $sql_common .= $comma . " $key = '".addslashes($val)."' ";

                $comma = ',';
            }

            sql_query(" INSERT INTO {$g5['login_table']} SET $sql_common ");
        }
        echo '<li>login table 复制</li>'.PHP_EOL;

        // visit table 复制
        $sql = " select * from {$g4['visit_table']} ";
        $result = sql_query($sql);

        // g5_visit 数据表初始化
        sql_query(" delete from {$g5['visit_table']} ");

        for($i=0; $row=sql_fetch_array($result); $i++) {
            if($is_euckr)
                $row = array_map('iconv_utf8', $row);

            // 重复检查
            /*
            $sql2 = " select count(*) as cnt from {$g5['visit_table']} where vi_ip = '{$row['vi_ip']}' and vi_date = '{$row['vi_date']}' ";
            $row2 = sql_fetch($sql2);
            if($row2['cnt'])
                continue;
            */

            $comma = '';
            $sql_common = '';

            foreach($row as $key=>$val) {
                $sql_common .= $comma . " $key = '".addslashes($val)."' ";

                $comma = ',';
            }

            sql_query(" INSERT INTO {$g5['visit_table']} SET $sql_common ");
        }
        echo '<li>visit table 复制</li>'.PHP_EOL;

        // visit sum table 复制
        $sql = " select * from {$g4['visit_sum_table']} ";
        $result = sql_query($sql);

        // g5_visit_sub 数据表初始化
        sql_query(" delete from {$g5['visit_sum_table']} ");

        for($i=0; $row=sql_fetch_array($result); $i++) {
            if($is_euckr)
                $row = array_map('iconv_utf8', $row);

            // 重复检查
            /*
            $sql2 = " select count(*) as cnt from {$g5['visit_sum_table']} where vs_date = '{$row['vs_date']}' ";
            $row2 = sql_fetch($sql2);
            if($row2['cnt'])
                continue;
            */

            $comma = '';
            $sql_common = '';

            foreach($row as $key=>$val) {
                $sql_common .= $comma . " $key = '".addslashes($val)."' ";

                $comma = ',';
            }

            sql_query(" INSERT INTO {$g5['visit_sum_table']} SET $sql_common ");
        }
        echo '<li>visit sum table 复制</li>'.PHP_EOL;

        // group table 复制
        $columns = array();
        $fields = mysql_list_fields(G5_MYSQL_DB, $g5['group_table']);
        $count = mysql_num_fields($fields);
        for ($i = 0; $i < $count; $i++) {
            $fld = mysql_field_name($fields, $i);
            $columns[] = $fld;
        }

        $sql = " select * from {$g4['group_table']} ";
        $result = sql_query($sql);
        for($i=0; $row=sql_fetch_array($result); $i++) {
            if($is_euckr)
                $row = array_map('iconv_utf8', $row);

            // 重复检查
            $sql2 = " select count(*) as cnt from {$g5['group_table']} where gr_id = '{$row['gr_id']}' ";
            $row2 = sql_fetch($sql2);
            if($row2['cnt'])
                continue;

            $comma = '';
            $sql_common = '';

            foreach($row as $key=>$val) {
                if(!in_array($key, $columns))
                    continue;

                $sql_common .= $comma . " $key = '".addslashes($val)."' ";

                $comma = ',';
            }

            sql_query(" INSERT INTO {$g5['group_table']} SET $sql_common ");
        }

        echo '<li>group table 复制</li>'.PHP_EOL;
        unset($columns);
        unset($fiels);

        // board 复制
        $columns = array();
        $fields = mysql_list_fields(G5_MYSQL_DB, $g5['board_table']);
        $count = mysql_num_fields($fields);
        for ($i = 0; $i < $count; $i++) {
            $fld = mysql_field_name($fields, $i);
            $columns[] = $fld;
        }

        $sql = " select * from {$g4['board_table']} ";
        $result = sql_query($sql);
        for($i=0; $row=sql_fetch_array($result); $i++) {
            if($is_euckr)
                $row = array_map('iconv_utf8', $row);

            // 重复检查
            $sql2 = " select count(*) as cnt from {$g5['board_table']} where bo_table = '{$row['bo_table']}' ";
            $row2 = sql_fetch($sql2);
            if($row2['cnt'])
                continue;

            $comma = '';
            $sql_common = '';

            foreach($row as $key=>$val) {
                if(!in_array($key, $columns))
                    continue;

                $sql_common .= $comma . " $key = '".addslashes($val)."' ";

                $comma = ',';
            }

            sql_query(" INSERT INTO {$g5['board_table']} SET $sql_common ");

            // 创建论坛数据表
            $bo_table = $row['bo_table'];
            $file = file(G5_ADMIN_PATH.'/sql_write.sql');
            $sql = implode($file, "\n");

            $create_table = $g5['write_prefix'] . $bo_table;

            $source = array('/__TABLE_NAME__/', '/;/');
            $target = array($create_table, '');
            $sql = preg_replace($source, $target, $sql);

            // 复制文章
            if(sql_query($sql, FALSE)) {
                $write_table = $g4['write_prefix'].$bo_table;
                $columns2 = array();
                $fields2 = mysql_list_fields(G5_MYSQL_DB, $create_table);
                $count2 = mysql_num_fields($fields2);
                for ($j = 0; $j < $count2; $j++) {
                    $fld = mysql_field_name($fields2, $j);
                    $columns2[] = $fld;
                }

                $sql3 = " select * from $write_table ";
                $result3 = sql_query($sql3);

                for($k=0; $row3=sql_fetch_array($result3); $k++) {
                    if($is_euckr)
                        $row3 = array_map('iconv_utf8', $row3);

                    $comma3 = '';
                    $sql_common3 = '';

                    foreach($row3 as $key=>$val) {
                        if(!in_array($key, $columns2))
                            continue;

                        $sql_common3 .= $comma3 . " $key = '".addslashes($val)."' ";

                        $comma3 = ',';
                    }

                    // 附件数量
                    $wr_id = $row3['wr_id'];
                    $sql4 = " select count(*) as cnt from {$g4['board_file_table']} where bo_table = '$bo_table' and wr_id = '$wr_id' ";
                    $row4 = sql_fetch($sql4);

                    $sql_common3 .= " , wr_file = '{$row4['cnt']}' ";

                    sql_query(" INSERT INTO $create_table SET $sql_common3 ");
                }

                echo '<li>'.str_replace(G5_TABLE_PREFIX.'write_', '', $create_table).' 复制文章</li>';
            }
        }

        unset($columns);
        unset($fiels);

        // 其余数据表复制
        $tables = array('board_file', 'board_new', 'board_good', 'mail', 'memo', 'group_member', 'auth', 'popular', 'poll', 'poll_etc', 'scrap');

        foreach($tables as $table) {
            $columns = array();
            $fields = mysql_list_fields(G5_MYSQL_DB, $g5[$table.'_table']);
            $count = mysql_num_fields($fields);
            for ($i = 0; $i < $count; $i++) {
                $fld = mysql_field_name($fields, $i);
                $columns[] = $fld;
            }

            $src_table = $g4[$table.'_table'];
            $dst_table = $g5[$table.'_table'];
            $sql = " select * from $src_table ";
            $result = sql_query($sql);
            for($i=0; $row=sql_fetch_array($result); $i++) {
                if($is_euckr)
                    $row = array_map('iconv_utf8', $row);

                $comma = '';
                $sql_common = '';

                foreach($row as $key=>$val) {
                    if(!in_array($key, $columns))
                        continue;

                    $sql_common .= $comma . " $key = '".addslashes($val)."' ";

                    $comma = ',';
                }

                $result2 = sql_query(" INSERT INTO $dst_table SET $sql_common ", false);

                if(!$result2)
                    continue;
            }

            echo '<li>'.$table.' table 复制</li>'.PHP_EOL;
        }

        unset($columns);
        unset($fiels);

        echo '</ol>'.PHP_EOL;

        echo '<div id="run_msg">Gnuboard4数据迁移升级操作完成</div>'.PHP_EOL;

        // 执行完成记录到session
        set_session('tables_copied', 'done');
        ?>
        </div>

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