<?php
include_once('./_common.php');
include_once(G5_LIB_PATH.'/latest.lib.php');
$g5['title'] = $group['gr_subject'];

if (G5_IS_MOBILE) {
    include_once(G5_MOBILE_PATH.'/group.php');
    return;
}

if(!$is_admin && $group['gr_device'] == 'mobile')
    alert($group['gr_subject'].'群组仅允许触屏版使用，请您使用移动设备访问！');

include_once('./_head.php');
?>


<!-- 首页 最新文章开始 -->
<?php
//  最新文章
$sql = " select bo_table, bo_subject
            from {$g5[board_table]}
            where gr_id = '{$gr_id}'
              and bo_list_level <= '{$member[mb_level]}'
              and bo_device <> 'mobile' ";
if(!$is_admin)
    $sql .= " and bo_use_cert = '' ";
$sql .= " order by bo_order ";
$result = sql_query($sql);
for ($i=0; $row=sql_fetch_array($result); $i++) {
    $lt_style = "";
    if ($i%2==1) $lt_style = "margin-left:20px";
    else $lt_style = "";
?>
    <div style="float:left;<?php echo $lt_style ?>">
    <?php
    // 此代码用于调用最新文章
    // 使用方法 : latest(skin, 论坛版块id, 显示行数, 字节长度);
    echo latest('basic', $row['bo_table'], 5, 70);
    ?>
    </div>
<?php
}
?>
<!-- 首页 最新内容结束 -->

<?php
include_once('./_tail.php');
?>
