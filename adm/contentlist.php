<?php
$sub_menu = '300600';
include_once('./_common.php');

auth_check($auth[$sub_menu], "r");

if( !isset($g5['content_table']) ){
    die('<meta charset="utf-8">/data/dbconfig.php 文件中请加入<strong>$g5[\'content_table\'] = G5_TABLE_PREFIX.\'content\';</strong>');
}
//检查内容(项目)中是否有信息数据表
if(!sql_query(" DESCRIBE {$g5['content_table']} ", false)) {
    if(sql_query(" DESCRIBE {$g5['g5_shop_content_table']} ", false)) {
        sql_query(" ALTER TABLE {$g5['g5_shop_content_table']} RENAME TO `{$g5['content_table']}` ;", false);
    } else {
       $query_cp = sql_query(" CREATE TABLE IF NOT EXISTS `{$g5['content_table']}` (
                      `co_id` varchar(20) NOT NULL DEFAULT '',
                      `co_html` tinyint(4) NOT NULL DEFAULT '0',
                      `co_subject` varchar(255) NOT NULL DEFAULT '',
                      `co_content` longtext NOT NULL,
                      `co_hit` int(11) NOT NULL DEFAULT '0',
                      `co_include_head` varchar(255) NOT NULL,
                      `co_include_tail` varchar(255) NOT NULL,
                      PRIMARY KEY (`co_id`)
                    ) ENGINE=MyISAM DEFAULT CHARSET=utf8 ", true);

        // 内容创建管理
        sql_query(" insert into `{$g5['content_table']}` set co_id = 'company', co_html = '1', co_subject = '网站介绍', co_content= '<p align=center><b>请设置公司介绍内容</b></p>' ", false );
        sql_query(" insert into `{$g5['content_table']}` set co_id = 'privacy', co_html = '1', co_subject = '个人隐私保护条例', co_content= '<p align=center><b>请设置个人隐私保护条例</b></p>' ", false );
        sql_query(" insert into `{$g5['content_table']}` set co_id = 'provision', co_html = '1', co_subject = '网站服务条款', co_content= '<p align=center><b>请设置网站服务条款</b></p>' ", false );
    }
}

$g5['title'] = '内容管理';
include_once (G5_ADMIN_PATH.'/admin.head.php');

$sql_common = " from {$g5['content_table']} ";

// 仅计算所有数据行数量
$sql = " select count(*) as cnt " . $sql_common;
$row = sql_fetch($sql);
$total_count = $row['cnt'];

$rows = $config['cf_page_rows'];
$total_page  = ceil($total_count / $rows);  // 计算所有页码
if ($page < 1) { $page = 1; } // 如果没有页码时设置为1
$from_record = ($page - 1) * $rows; // 获取开始行

$sql = "select * $sql_common order by co_id limit $from_record, {$config['cf_page_rows']} ";
$result = sql_query($sql);
?>

<div class="local_ov01 local_ov">
    <?php if ($page > 1) {?><a href="<?php echo $_SERVER['PHP_SELF']; ?>">返回首页</a><?php } ?>
    <span>全部 内容 <?php echo $total_count; ?>件</span>
</div>

<div class="btn_add01 btn_add">
    <a href="./contentform.php">内容 添加</a>
</div>

<div class="tbl_head01 tbl_wrap">
    <table>
    <caption><?php echo $g5['title']; ?> 目录</caption>
    <thead>
    <tr>
        <th scope="col">ID</th>
        <th scope="col">主题</th>
        <th scope="col">管理</th>
    </tr>
    </thead>
    <tbody>
    <?php for ($i=0; $row=mysql_fetch_array($result); $i++) {
        $bg = 'bg'.($i%2);
    ?>
    <tr class="<?php echo $bg; ?>">
        <td class="td_id"><?php echo $row['co_id']; ?></td>
        <td><?php echo htmlspecialchars2($row['co_subject']); ?></td>
        <td class="td_mng">
            <a href="./contentform.php?w=u&amp;co_id=<?php echo $row['co_id']; ?>"><span class="sound_only"><?php echo htmlspecialchars2($row['co_subject']); ?> </span>修改</a>
            <a href="<?php echo G5_BBS_URL; ?>/content.php?co_id=<?php echo $row['co_id']; ?>"><span class="sound_only"><?php echo htmlspecialchars2($row['co_subject']); ?> </span> 查看</a>
            <a href="./contentformupdate.php?w=d&amp;co_id=<?php echo $row['co_id']; ?>" onclick="return delete_confirm();"><span class="sound_only"><?php echo htmlspecialchars2($row['co_subject']); ?> </span>删除</a>
        </td>
    </tr>
    <?php
    }
    if ($i == 0) {
        echo '<tr><td colspan="3" class="empty_table">未找到数据</td></tr>';
    }
    ?>
    </tbody>
    </table>
</div>

<?php echo get_paging(G5_IS_MOBILE ? $config['cf_mobile_pages'] : $config['cf_write_pages'], $page, $total_page, "{$_SERVER['PHP_SELF']}?$qstr&amp;page="); ?>

<?php
include_once (G5_ADMIN_PATH.'/admin.tail.php');
?>
