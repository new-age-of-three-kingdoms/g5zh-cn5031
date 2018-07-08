<?php
$sub_menu = '100310';
include_once('./_common.php');

auth_check($auth[$sub_menu], "r");

if( !isset($g5['new_win_table']) ){
    die('<meta charset="utf-8">/data/dbconfig.php 文件中请加入 <strong>$g5[\'new_win_table\'] = G5_TABLE_PREFIX.\'new_win\';</strong> ');
}
//检查内容(项目)中是否有信息数据表
if(!sql_query(" DESCRIBE {$g5['new_win_table']} ", false)) {
    if(sql_query(" DESCRIBE {$g5['g5_shop_new_win_table']} ", false)) {
        sql_query(" ALTER TABLE {$g5['g5_shop_new_win_table']} RENAME TO `{$g5['new_win_table']}` ;", false);
    } else {
       $query_cp = sql_query(" CREATE TABLE IF NOT EXISTS `{$g5['new_win_table']}` (
                      `nw_id` int(11) NOT NULL AUTO_INCREMENT,
                      `nw_device` varchar(10) NOT NULL DEFAULT 'both',
                      `nw_begin_time` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
                      `nw_end_time` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
                      `nw_disable_hours` int(11) NOT NULL DEFAULT '0',
                      `nw_left` int(11) NOT NULL DEFAULT '0',
                      `nw_top` int(11) NOT NULL DEFAULT '0',
                      `nw_height` int(11) NOT NULL DEFAULT '0',
                      `nw_width` int(11) NOT NULL DEFAULT '0',
                      `nw_subject` text NOT NULL,
                      `nw_content` text NOT NULL,
                      `nw_content_html` tinyint(4) NOT NULL DEFAULT '0',
                      PRIMARY KEY (`nw_id`)
                    ) ENGINE=MyISAM DEFAULT CHARSET=utf8 ", true);
    }
}

$g5['title'] = '弹出层 管理';
include_once (G5_ADMIN_PATH.'/admin.head.php');

$sql_common = " from {$g5['new_win_table']} ";

// 仅计算所有数据行数量
$sql = " select count(*) as cnt " . $sql_common;
$row = sql_fetch($sql);
$total_count = $row['cnt'];

$sql = "select * $sql_common order by nw_id desc ";
$result = sql_query($sql);
?>

<div class="local_ov01 local_ov">全部 <?php echo $total_count; ?>件</div>

<div class="btn_add01 btn_add">
    <a href="./newwinform.php">新窗口管理添加</a>
</div>

<div class="tbl_head01 tbl_wrap">
    <table>
    <caption><?php echo $g5['title']; ?> 目录</caption>
    <thead>
    <tr>
        <th scope="col">序号</th>
        <th scope="col">主题</th>
        <th scope="col">访问设备</th>
        <th scope="col">开始时间</th>
        <th scope="col">结束时间</th>
        <th scope="col">时间</th>
        <th scope="col">Left</th>
        <th scope="col">Top</th>
        <th scope="col">Width</th>
        <th scope="col">Height</th>
        <th scope="col">管理</th>
    </tr>
    </thead>
    <tbody>
    <?php
    for ($i=0; $row=mysql_fetch_array($result); $i++) {
        $bg = 'bg'.($i%2);

        switch($row['nw_device']) {
            case 'pc':
                $nw_device = 'PC';
                break;
            case 'mobile':
                $nw_device = '触屏版';
                break;
            default:
                $nw_device = '全部';
                break;
        }
    ?>
    <tr class="<?php echo $bg; ?>">
        <td class="td_num"><?php echo $row['nw_id']; ?></td>
        <td><?php echo $row['nw_subject']; ?></td>
        <td class="td_device"><?php echo $nw_device; ?></td>
        <td class="td_datetime"><?php echo substr($row['nw_begin_time'],2,14); ?></td>
        <td class="td_datetime"><?php echo substr($row['nw_end_time'],2,14); ?></td>
        <td class="td_num"><?php echo $row['nw_disable_hours']; ?>时间</td>
        <td class="td_num"><?php echo $row['nw_left']; ?>px</td>
        <td class="td_num"><?php echo $row['nw_top']; ?>px</td>
        <td class="td_num"><?php echo $row['nw_width']; ?>px</td>
        <td class="td_num"><?php echo $row['nw_height']; ?>px</td>
        <td class="td_mngsmall">
            <a href="./newwinform.php?w=u&amp;nw_id=<?php echo $row['nw_id']; ?>"><span class="sound_only"><?php echo $row['nw_subject']; ?> </span>修改</a>
            <a href="./newwinformupdate.php?w=d&amp;nw_id=<?php echo $row['nw_id']; ?>" onclick="return delete_confirm();"><span class="sound_only"><?php echo $row['nw_subject']; ?> </span>删除</a>
        </td>
    </tr>
    <?php
    }

    if ($i == 0) {
        echo '<tr><td colspan="11" class="empty_table">未找到数据</td></tr>';
    }
    ?>
    </tbody>
    </table>
</div>


<?php
include_once (G5_ADMIN_PATH.'/admin.tail.php');
?>
