<?php
$sub_menu = '300700';
include_once('./_common.php');

auth_check($auth[$sub_menu], "r");

//检查dbconfig文件是否拥有$g5['faq_table'] , $g5['faq_master_table'] 排列参数
if( !isset($g5['faq_table']) || !isset($g5['faq_master_table']) ){
    die('<meta charset="utf-8">请在/data/dbconfig.php 文件添加 <br ><strong>$g5[\'faq_table\'] = G5_TABLE_PREFIX.\'faq\';</strong><br ><strong>$g5[\'faq_master_table\'] = G5_TABLE_PREFIX.\'faq_master\';</strong><br >.');
}

//检查是否已有常见问题数据
if(!sql_query(" DESCRIBE {$g5['faq_master_table']} ", false)) {
    if(sql_query(" DESCRIBE {$g5['g5_shop_faq_master_table']} ", false)) {
        sql_query(" ALTER TABLE {$g5['g5_shop_faq_master_table']} RENAME TO `{$g5['faq_master_table']}` ;", false);
    } else {
       $query_cp = sql_query(" CREATE TABLE IF NOT EXISTS `{$g5['faq_master_table']}` (
                      `fm_id` int(11) NOT NULL AUTO_INCREMENT,
                      `fm_subject` varchar(255) NOT NULL DEFAULT '',
                      `fm_head_html` text NOT NULL,
                      `fm_tail_html` text NOT NULL,
                      `fm_order` int(11) NOT NULL DEFAULT '0',
                      PRIMARY KEY (`fm_id`)
                    ) ENGINE=MyISAM DEFAULT CHARSET=utf8 ", true);
    }
    // FAQ Master
    sql_query(" insert into `{$g5['faq_master_table']}` set fm_id = '1', fm_subject = '常见问题' ", false);
}

//检查是否有提问数据
if(!sql_query(" DESCRIBE {$g5['faq_table']} ", false)) {
    if(sql_query(" DESCRIBE {$g5['g5_shop_faq_table']} ", false)) {
        sql_query(" ALTER TABLE {$g5['g5_shop_faq_table']} RENAME TO `{$g5['faq_table']}` ;", false);
    } else {
       $query_cp = sql_query(" CREATE TABLE IF NOT EXISTS `{$g5['faq_table']}` (
                      `fa_id` int(11) NOT NULL AUTO_INCREMENT,
                      `fm_id` int(11) NOT NULL DEFAULT '0',
                      `fa_subject` text NOT NULL,
                      `fa_content` text NOT NULL,
                      `fa_order` int(11) NOT NULL DEFAULT '0',
                      PRIMARY KEY (`fa_id`),
                      KEY `fm_id` (`fm_id`)
                    ) ENGINE=MyISAM DEFAULT CHARSET=utf8 ", true);
    }
}

$g5['title'] = 'FAQ管理';
include_once (G5_ADMIN_PATH.'/admin.head.php');

$sql_common = " from {$g5['faq_master_table']} ";

// 仅计算所有数据行数量
$sql = " select count(*) as cnt " . $sql_common;
$row = sql_fetch($sql);
$total_count = $row['cnt'];

$rows = $config['cf_page_rows'];
$total_page  = ceil($total_count / $rows);  // 计算所有页码
if ($page < 1) { $page = 1; } // 如果没有页码时设置为1
$from_record = ($page - 1) * $rows; // 获取开始行

$sql = "select * $sql_common order by fm_order, fm_id limit $from_record, {$config['cf_page_rows']} ";
$result = sql_query($sql);
?>

<div class="local_ov01 local_ov">
    <?php if ($page > 1) {?><a href="<?php echo $_SERVER['PHP_SELF']; ?>">返回首页</a><?php } ?>
    <span>全部 FAQ <?php echo $total_count; ?>件</span>
</div>

<div class="local_desc01 local_desc">
    <ol>
        <li>常见问题无数量限制，请根据需要添加</li>
        <li>点击<strong>创建常见问题分类</strong>设置分类(至少需要创建一个分类)</li>
        <li>创建分类后可以通过点击分类标题进入内容设置</li>
    </ol>
</div>

<div class="btn_add01 btn_add">
    <a href="./faqmasterform.php">创建常见问题分类</a>
</div>

<div class="tbl_head01 tbl_wrap">
    <table>
    <caption><?php echo $g5['title']; ?> 目录</caption>
    <thead>
    <tr>
        <th scope="col">ID</th>
        <th scope="col">主题</th>
        <th scope="col">问题数量</th>
        <th scope="col">顺序</th>
        <th scope="col">管理</th>
    </tr>
    </thead>
    <tbody>
    <?php for ($i=0; $row=mysql_fetch_array($result); $i++) {
        $sql1 = " select COUNT(*) as cnt from {$g5['faq_table']} where fm_id = '{$row['fm_id']}' ";
        $row1 = sql_fetch($sql1);
        $cnt = $row1['cnt'];
        $bg = 'bg'.($i%2);
    ?>
    <tr class="<?php echo $bg; ?>">
        <td class="td_num"><?php echo $row['fm_id']; ?></td>
        <td><a href="./faqlist.php?fm_id=<?php echo $row['fm_id']; ?>&amp;fm_subject=<?php echo $row['fm_subject']; ?>"><?php echo stripslashes($row['fm_subject']); ?></a></td>
        <td class="td_num"><?php echo $cnt; ?></td>
        <td class="td_num"><?php echo $row['fm_order']?></td>
        <td class="td_mng">
            <a href="./faqmasterform.php?w=u&amp;fm_id=<?php echo $row['fm_id']; ?>"><span class="sound_only"><?php echo stripslashes($row['fm_subject']); ?> </span>修改</a>
            <a href="<?php echo G5_BBS_URL; ?>/faq.php?fm_id=<?php echo $row['fm_id']; ?>"><span class="sound_only"><?php echo stripslashes($row['fm_subject']); ?> </span>查看</a>
            <a href="./faqmasterformupdate.php?w=d&amp;fm_id=<?php echo $row['fm_id']; ?>" onclick="return delete_confirm();"><span class="sound_only"><?php echo stripslashes($row['fm_subject']); ?> </span>删除</a>
        </td>
    </tr>
    <?php
    }

    if ($i == 0){
        echo '<tr><td colspan="5" class="empty_table"><span>未找到数据</span></td></tr>';
    }
    ?>
    </tbody>
    </table>
</div>

<?php echo get_paging(G5_IS_MOBILE ? $config['cf_mobile_pages'] : $config['cf_write_pages'], $page, $total_page, "{$_SERVER['PHP_SELF']}?$qstr&amp;page="); ?>

<?php
include_once (G5_ADMIN_PATH.'/admin.tail.php');
?>
