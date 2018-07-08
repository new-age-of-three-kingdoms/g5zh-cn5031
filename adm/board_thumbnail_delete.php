<?php
$sub_menu = '300100';
include_once('./_common.php');

auth_check($auth[$sub_menu], 'w');

if(!$board['bo_table'])
    alert('不存在的论坛版块');

$g5['title'] = $board['bo_subject'].' 缩略图删除';
include_once('./admin.head.php');
?>

<div class="local_desc02 local_desc">
    <p>
        显示执行完成之前请勿关闭或刷新当前页面
    </p>
</div>

<?php
$dir = G5_DATA_PATH.'/file/'.$bo_table;

$cnt = 0;
if(is_dir($dir)) {
    echo '<ul>';
    $files = glob($dir.'/thumb-*');
    if (is_array($files)) {
        foreach($files as $thumbnail) {
            $cnt++;
            @unlink($thumbnail);

            echo '<li>'.$thumbnail.'</li>'.PHP_EOL;

            flush();

            if ($cnt%10==0)
                echo PHP_EOL;
        }
    }

    echo '<li>执行完成</li></ul>'.PHP_EOL;
    echo '<div class="local_desc01 local_desc"><p><strong>删除缩略图'.$cnt.'个</strong></p></div>'.PHP_EOL;
} else {
    echo '<p>未找到附件目录</p>';
}
?>

<div class="btn_confirm01 btn_confirm"><a href="./board_form.php?w=u&amp;bo_table=<?php echo $bo_table; ?>&amp;<?php echo $qstr; ?>">返回论坛编辑</a></div>

<?php
include_once('./admin.tail.php');
?>