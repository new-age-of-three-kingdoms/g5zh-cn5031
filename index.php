<?php
define('_INDEX_', true);
include_once('./_common.php');

// 首页初始画面路径设置 : 请勿随意更改此段代码
if ($config['cf_include_index'] && is_file(G5_PATH.'/'.$config['cf_include_index'])) {
    include_once(G5_PATH.'/'.$config['cf_include_index']);
    return; // 不执行以下代码
}

if (G5_IS_MOBILE) {
    include_once(G5_MOBILE_PATH.'/index.php');
    return;
}

include_once('./_head.php');
?>

<h2 class="sound_only">最新文章</h2>
<!-- 最新文章开始 { -->
<?php
//  最新文章
$sql = " select bo_table
            from `{$g5['board_table']}` a left join `{$g5['group_table']}` b on (a.gr_id=b.gr_id)
            where a.bo_device <> 'mobile' ";
if(!$is_admin)
    $sql .= " and a.bo_use_cert = '' ";
$sql .= " order by b.gr_order, a.bo_order ";
$result = sql_query($sql);
for ($i=0; $row=sql_fetch_array($result); $i++) {
    if ($i%2==1) $lt_style = "margin-left:20px";
    else $lt_style = "";
?>
    <div style="float:left;<?php echo $lt_style ?>">
        <?php
        // 此代码用于调用最新文章
        // 使用方法 : latest(skin, 论坛版块id, 显示行数, 字节长度);
        echo latest("basic", $row['bo_table'], 5, 25);
        ?>
    </div>
<?php
}
?>
<!-- } 最新内容结束 -->

<?php
include_once('./_tail.php');
?>
