<?php
include_once('./_common.php');

$subject = strip_tags($_POST['subject']);
$content = strip_tags($_POST['content']);

//$filter = explode(",", strtolower(trim($config['cf_filter'])));
// strtolower 替换成以下代码 (sir会员提供)
$filter = explode(",", trim($config['cf_filter']));
for ($i=0; $i<count($filter); $i++) {
    $str = $filter[$i];

    // 标题筛选 (搜索到对应内容就终止)
    $subj = "";
    $pos = stripos($subject, $str);
    if ($pos !== false) {
        $subj = $str;
        break;
    }

    // 内容筛选 (搜索到对应内容就终止)
    $cont = "";
    $pos = stripos($content, $str);
    if ($pos !== false) {
        $cont = $str;
        break;
    }
}

die("{\"subject\":\"$subj\",\"content\":\"$cont\"}");
?>