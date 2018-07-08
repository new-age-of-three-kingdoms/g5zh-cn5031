<?php
$sub_menu = '100900';
include_once('./_common.php');

if ($is_admin != 'super')
    alert('您没有访问权限', G5_URL);

$g5['title'] = '删除缓存文件';
include_once('./admin.head.php');
?>

<div class="local_desc02 local_desc">
    <p>
        显示执行完成之前请勿关闭或刷新当前页面
    </p>
</div>

<?php
flush();

if (!$dir=@opendir(G5_DATA_PATH.'/cache')) {
    echo '<p>无法打开缓存文件夹</p>';
}

$cnt=0;
echo '<ul>'.PHP_EOL;

$files = glob(G5_DATA_PATH.'/cache/latest-*');
if (is_array($files)) {
    foreach ($files as $cache_file) {
        $cnt++;
        unlink($cache_file);
        echo '<li>'.$cache_file.'</li>'.PHP_EOL;

        flush();

        if ($cnt%10==0) 
            echo PHP_EOL;
    }
}

echo '<li>执行完成</li></ul>'.PHP_EOL;
echo '<div class="local_desc01 local_desc"><p><strong>已删除最新文章缓存文件'.$cnt.'条数据</strong><br>您可以安全关闭或离开当前页面</p></div>'.PHP_EOL;
?>

<?php
include_once('./admin.tail.php');
?>