<?php
include_once("./_common.php");
include_once(G5_LIB_PATH."/register.lib.php");

$mb_recommend = trim($_POST["reg_mb_recommend"]);

if ($msg = valid_mb_id($mb_recommend)) {
    die("推荐人账号只能输入英文、数字、下划线");
}
if (!($msg = exist_mb_id($mb_recommend))) {
    die("未找到您输入的推荐人信息");
}
?>