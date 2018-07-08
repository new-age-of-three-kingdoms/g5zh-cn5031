<?php
if (!defined('_GNUBOARD_')) exit; //禁止单独访问此页
$data_path = '../'.G5_DATA_DIR;

if (!$title) $title = G5_VERSION." 安装";
?>
<!doctype html>
<html lang="zh-cn">
<head>
<meta charset="utf-8">
<title><?php echo $title; ?></title>
<link rel="stylesheet" href="install.css">
</head>
<body>

<div id="ins_bar">
    <span id="bar_img">GNUBOARD5</span>
    <span id="bar_txt">INSTALLATION</span>
</div>

<?php
// 如果已有设置文件则无法进行安装
$dbconfig_file = $data_path.'/'.G5_DBCONFIG_FILE;
if (file_exists($dbconfig_file)) {
?>
<h1>已有<?php echo G5_VERSION; ?>安装或在运行</h1>

<div class="ins_inner">
    <p>由于已有相同程序运行无法进行安装<br />如需要重新安装请您删除以下设定文件</p>
    <ul>
        <li><?php echo $dbconfig_file ?></li>
    </ul>
</div>
<?php
    exit;
}
?>

<?php
$exists_data_dir = true;
// 查看是否拥有data目录
if (!is_dir($data_path))
{
?>
<h1>为了<?php echo G5_VERSION; ?>成功安装运行，请确认以下信息</h1>

<div class="ins_inner">
    <p>
        请在根目录内创建<?php echo G5_DATA_DIR ?>目录<br />
        (common.php 文件所在的目录为程序使用的根目录)<br /><br />
        $> mkdir <?php echo G5_DATA_DIR ?><br /><br />
        windows系统运行时请创建data文件夹<br /><br />
        完成以上操作后请刷新浏览器
    </p>
</div>
<?php
    $exists_data_dir = false;
}
?>

<?php
$write_data_dir = true;
// data文件夹权限检查
if (strtoupper(substr(PHP_OS, 0, 3)) !== 'WIN') {
    $sapi_type = php_sapi_name();
    if (substr($sapi_type, 0, 3) == 'cgi') {
        if (!(is_readable($data_path) && is_executable($data_path)))
        {
        ?>
        <div class="ins_inner">
            <p>
                <?php echo G5_DATA_DIR ?>请将此文件夹属性（权限）修改为705<br /><br />
                $> chmod 705 <?php echo G5_DATA_DIR ?> 或者 chmod uo+rx <?php echo G5_DATA_DIR ?><br /><br />
                完成以上操作后请刷新浏览器
            </p>
        </div>
        <?php
            $write_data_dir = false;
        }
    } else {
        if (!(is_readable($data_path) && is_writeable($data_path) && is_executable($data_path)))
        {
        ?>
        <div class="ins_inner">
            <p>
                <?php echo G5_DATA_DIR ?>请将此文件夹属性（权限）修改为707<br /><br />
                $> chmod 707 <?php echo G5_DATA_DIR ?> 或者 chmod uo+rwx <?php echo G5_DATA_DIR ?><br /><br />
                完成以上操作后请刷新浏览器
            </p>
        </div>
        <?php
            $write_data_dir = false;
        }
    }
}
?>