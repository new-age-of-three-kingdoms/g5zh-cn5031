<?php
if (!defined('_GNUBOARD_')) exit; //禁止单独访问此页

include_once(G5_MOBILE_PATH.'/_head.php');
?>

<!-- 首页 最新文章开始 -->
<?php
//  最新文章
$sql = " select bo_table
            from `{$g5['board_table']}` a left join `{$g5['group_table']}` b on (a.gr_id=b.gr_id)
            where a.bo_device <> 'pc' ";
if(!$is_admin)
    $sql .= " and a.bo_use_cert = '' ";
$sql .= " order by b.gr_order, a.bo_order ";
$result = sql_query($sql);
for ($i=0; $row=sql_fetch_array($result); $i++) {
    // 此代码用于调用最新文章
    // 不指定皮肤时使用默认皮肤

    // 使用方法
    // latest(皮肤, 论坛版块id, 显示行数, 字节数);
    echo latest("basic", $row['bo_table'], 5, 25);
}
?>
<!-- 首页 最新内容结束 -->

<?php
include_once(G5_MOBILE_PATH.'/_tail.php');
?>