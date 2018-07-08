<?php
$gmnow = gmdate('D, d M Y H:i:s').' GMT';
header('Expires: 0'); // rfc2616 - Section 14.21
header('Last-Modified: ' . $gmnow);
header('Cache-Control: no-store, no-cache, must-revalidate'); // HTTP/1.1
header('Cache-Control: pre-check=0, post-check=0, max-age=0'); // HTTP/1.1
header('Pragma: no-cache'); // HTTP/1.0

include_once ('../config.php');
$title = G5_VERSION." 运行环境设置 2/3";
include_once ('./install.inc.php');

if (!isset($_POST['agree']) || $_POST['agree'] != '同意') {
    echo "<div class=\"ins_inner\"><p>需要浏览并同意版权信息才能进行安装</p>".PHP_EOL;
    echo "<div class=\"inner_btn\"><a href=\"./\">返回</a></div></div>".PHP_EOL;
    exit;
}
?>


<form id="frm_install" method="post" action="./install_db.php" autocomplete="off" onsubmit="return frm_install_submit(this)">

<div class="ins_inner">
    <table class="ins_frm">
    <caption>MySQL设置</caption>
    <colgroup>
        <col style="width:150px">
        <col>
    </colgroup>
    <tbody>
    <tr>
        <th scope="row"><label for="mysql_host">Host</label></th>
        <td>
            <input name="mysql_host" type="text" value="localhost" id="mysql_host">
        </td>
    </tr>
    <tr>
        <th scope="row"><label for="mysql_user">User</label></th>
        <td>
            <input name="mysql_user" type="text" id="mysql_user">
        </td>
    </tr>
    <tr>
        <th scope="row"><label for="mysql_pass">Password</label></th>
        <td>
            <input name="mysql_pass" type="text" id="mysql_pass">
        </td>
    </tr>
    <tr>
        <th scope="row"><label for="mysql_db">DB</label></th>
        <td>
            <input name="mysql_db" type="text" id="mysql_db">
        </td>
    </tr>
    <tr>
        <th scope="row"><label for="table_prefix">数据表前缀名称</label></th>
        <td>
            <input name="table_prefix" type="text" value="g5_" id="table_prefix">
            <span>如无特别需求请勿修改默认值</span>
        </td>
    </tr>
    </tbody>
    </table>

    <table class="ins_frm">
    <caption>管理员信息设置</caption>
    <colgroup>
        <col style="width:150px">
        <col>
    </colgroup>
    <tbody>
    <tr>
        <th scope="row"><label for="admin_id">会员 ID</label></th>
        <td>
            <input name="admin_id" type="text" value="admin" id="admin_id">
        </td>
    </tr>
    <tr>
        <th scope="row"><label for="admin_pass">密码</label></th>
        <td>
            <input name="admin_pass" type="text" id="admin_pass">
        </td>
    </tr>
    <tr>
        <th scope="row"><label for="admin_name">姓名</label></th>
        <td>
            <input name="admin_name" type="text" value="网站管理员" id="admin_name">
        </td>
    </tr>
    <tr>
        <th scope="row"><label for="admin_email">E-mail</label></th>
        <td>
            <input name="admin_email" type="text" value="admin@domain.com" id="admin_email">
        </td>
    </tr>
    </tbody>
    </table>

    <p>
        <strong class="st_strong">注意！如果已有 <?php echo G5_VERSION ?>在运行会导致原有数据被覆盖</strong><br>
        如果您已了解注意事项请点击下一步进行安装
    </p>

    <div class="inner_btn">
        <input type="submit" value="下一步">
    </div>
</div>

<script>
function frm_install_submit(f)
{
    if (f.mysql_host.value == '')
    {
        alert('请设置MySQL服务器地址'); f.mysql_host.focus(); return false;
    }
    else if (f.mysql_user.value == '')
    {
        alert('请设置MySQL数据库用户名'); f.mysql_user.focus(); return false;
    }
    else if (f.mysql_db.value == '')
    {
        alert('请设置MySQL数据库名称'); f.mysql_db.focus(); return false;
    }
    else if (f.admin_id.value == '')
    {
        alert('请设置管理员ID'); f.admin_id.focus(); return false;
    }
    else if (f.admin_pass.value == '')
    {
        alert('请设置管理员密码'); f.admin_pass.focus(); return false;
    }
    else if (f.admin_name.value == '')
    {
        alert('请设置管理员名称'); f.admin_name.focus(); return false;
    }
    else if (f.admin_email.value == '')
    {
        alert('请设置管理员邮件地址'); f.admin_email.focus(); return false;
    }


    if(/^[a-z][a-z0-9]/i.test(f.admin_id.value) == false) {
        alert('网站管理员ID必须使用英文字母开头，可以使用英文字母及数字组合');
        f.admin_id.focus();
        return false;
    }

    return true;
}
</script>

<?php
include_once ('./install.inc2.php');
?>