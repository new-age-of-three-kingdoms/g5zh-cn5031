<?php
if (!defined('_GNUBOARD_')) exit; //禁止单独访问此页

if(!$is_admin && $group['gr_device'] == 'pc')
    alert($group['gr_subject'].' 当前群组只允许使用电脑版访问');

include_once(G5_MOBILE_PATH.'/_head.php');
?>

<!-- 首页 最新文章开始 -->
<?php
//  最新文章
$sql = " select bo_table, bo_subject
            from {$g5['board_table']}
            where gr_id = '{$gr_id}'
              and bo_list_level <= '{$member['mb_level']}'
              and bo_device <> 'pc' ";
if(!$is_admin)
    $sql .= " and bo_use_cert = '' ";
$sql .= " order by bo_table ";
$result = sql_query($sql);
for ($i=0; $row=sql_fetch_array($result); $i++) {
    // 此代码用于调用最新文章
    // 不指定皮肤时使用默认皮肤

    // 使用方法
    // latest(皮肤, 论坛版块id, 显示行数, 字节数);
    echo latest('basic', $row['bo_table'], 5, 70);
}
?>
<!-- 首页 最新内容结束 -->

<?php
include_once(G5_MOBILE_PATH.'/_tail.php');
?>
