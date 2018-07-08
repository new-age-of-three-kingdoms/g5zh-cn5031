<?php
$sub_menu = "100800";
include_once("./_common.php");

if ($is_admin != "super")
    alert("您没有访问权限", G5_URL);

$g5['title'] = "批量删除session";
include_once("./admin.head.php");
?>

<div class="local_desc02 local_desc">
    <p>
        显示执行完成之前请勿关闭或刷新当前页面
    </p>
</div>

    <?php
    flush();

    $list_tag_st = "";
    $list_tag_end = "";
    if (!$dir=@opendir(G5_DATA_PATH.'/session')) {
      echo "<p>无法打开session文件夹</p>";
    } else {
        $list_tag_st = "<ul>\n<li>执行完成</li>\n";
        $list_tag_end = "</ul>\n";
    }

    $cnt=0;
    echo $list_tag_st;
    while($file=readdir($dir)) {

        if (!strstr($file,'sess_')) continue;
        if (strpos($file,'sess_')!=0) continue;

        $session_file = G5_DATA_PATH.'/session/'.$file;

        if (!$atime=@fileatime($session_file)) {
            continue;
        }
        if (time() > $atime + (3600 * 6)) {  // 时间需要使用秒设定 默认值6小时以前
            $cnt++;
            $return = unlink($session_file);
            //echo "<script>document.getElementById('ct').innerHTML += '{$session_file}<br/>';</script>\n";
            echo "<li>{$session_file}</li>\n";

            flush();

            if ($cnt%10==0)
                //echo "<script>document.getElementById('ct').innerHTML = '';</script>\n";
                echo "\n";
        }
    }
    echo $list_tag_end;
    echo '<div class="local_desc01 local_desc"><p><strong>删除完成session'.$cnt.'条数据文件</strong><br>您可以安全关闭或离开当前页面</p></div>'.PHP_EOL;
?>

<?php
include_once("./admin.tail.php");
?>
