<?php
$sub_menu = '100920';
include_once('./_common.php');

if ($is_admin != 'super')
    alert('您没有访问权限', G5_URL);

$g5['title'] = '批量删除缩略图文件';
include_once('./admin.head.php');
?>

<div class="local_desc02 local_desc">
    <p>
        显示执行完成之前请勿关闭或刷新当前页面
    </p>
</div>

<?php
$directory = array();
$dl = array('file', 'editor');

foreach($dl as $val) {
    if($handle = opendir(G5_DATA_PATH.'/'.$val)) {
        while(false !== ($entry = readdir($handle))) {
            if($entry == '.' || $entry == '..')
                continue;

            $path = G5_DATA_PATH.'/'.$val.'/'.$entry;

            if(is_dir($path))
                $directory[] = $path;
        }
    }
}

flush();

if (empty($directory)) {
    echo '<p>无法打开缩略图文件夹</p>';
}

$cnt=0;
echo '<ul>'.PHP_EOL;

foreach($directory as $dir) {
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
}

echo '<li>执行完成</li></ul>'.PHP_EOL;
echo '<div class="local_desc01 local_desc"><p><strong>删除缩略图'.$cnt.'个</strong><br>您可以安全关闭或离开当前页面</p></div>'.PHP_EOL;
?>

<?php
include_once('./admin.tail.php');
?>