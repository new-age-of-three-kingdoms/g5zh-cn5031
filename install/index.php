<?php
include_once ('../config.php');
$title = G5_VERSION." 版权确认 1/3";
include_once ('./install.inc.php');
?>

<?php
if ($exists_data_dir && $write_data_dir) {
    // 必须模块检测
    require_once('./library.check.php');
?>
<form action="./install_config.php" method="post" onsubmit="return frm_submit(this);">

<div class="ins_inner">
    <p>
        <strong class="st_strong">请仔细浏览并确认版权信息</strong><br>
        同意遵守版权信息才能进行安装操作
    </p>

    <div class="ins_ta ins_license">
        <textarea name="textarea" id="ins_license" readonly><?php echo implode('', file('../LICENSE.txt')); ?></textarea>
    </div>

    <div id="ins_agree">
        <label for="agree">同意</label>
        <input type="checkbox" name="agree" value="同意" id="agree">
    </div>

    <div class="inner_btn">
        <input type="submit" value="下一步">
    </div>
</div>

</form>

<script>
function frm_submit(f)
{
    if (!f.agree.checked) {
        alert("请同意版权信息");
        return false;
    }
    return true;
}
</script>
<?php
} // if
?>

<?php
include_once ('./install.inc2.php');
?>
