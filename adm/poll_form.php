<?php
$sub_menu = "200900";
include_once('./_common.php');

auth_check($auth[$sub_menu], 'w');

$token = get_token();

$html_title = '投票';
if ($w == '')
    $html_title .= '创建';
else if ($w == 'u')  {
    $html_title .= '编辑';
    $sql = " select * from {$g5['poll_table']} where po_id = '{$po_id}' ";
    $po = sql_fetch($sql);
} else
    alert('w值传递错误！');

$g5['title'] = $html_title;
include_once('./admin.head.php');
?>

<form name="fpoll" id="fpoll" action="./poll_form_update.php" method="post" enctype="multipart/form-data">
<input type="hidden" name="po_id" value="<?php echo $po_id ?>">
<input type="hidden" name="w" value="<?php echo $w ?>">
<input type="hidden" name="sfl" value="<?php echo $sfl ?>">
<input type="hidden" name="stx" value="<?php echo $stx ?>">
<input type="hidden" name="sst" value="<?php echo $sst ?>">
<input type="hidden" name="sod" value="<?php echo $sod ?>">
<input type="hidden" name="page" value="<?php echo $page ?>">
<input type="hidden" name="token" value="<?php echo $token ?>">

<div class="tbl_frm01 tbl_wrap">

    <table>
    <caption><?php echo $g5['title']; ?></caption>
    <tbody>
    <tr>
        <th scope="row"><label for="po_subject">投票标题<strong class="sound_only">必选项</strong></label></th>
        <td><input type="text" name="po_subject" value="<?php echo $po['po_subject'] ?>" id="po_subject" required class="required frm_input" size="80" maxlength="125"></td>
    </tr>

    <?php
    for ($i=1; $i<=9; $i++) {
        $required = '';
        if ($i==1 || $i==2) {
            $required = 'required';
            $sound_only = '<strong class="sound_only">必选项</strong>';
        }

        $po_poll = get_text($po['po_poll'.$i]);
    ?>

    <tr>
        <th scope="row"><label for="po_poll<?php echo $i ?>">项目<?php echo $i ?><?php echo $sound_only ?></label></th>
        <td>
            <input type="text" name="po_poll<?php echo $i ?>" value="<?php echo $po_poll ?>" id="po_poll<?php echo $i ?>" <?php echo $required ?> class="frm_input <?php echo $required ?>" maxlength="125">
            <label for="po_cnt<?php echo $i ?>">项目<?php echo $i ?>投票数</label>
            <input type="text" name="po_cnt<?php echo $i ?>" value="<?php echo $po['po_cnt'.$i] ?>" id="po_cnt<?php echo $i ?>" class="frm_input" size="3">
       </td>
    </tr>

    <?php } ?>

    <tr>
        <th scope="row"><label for="po_etc">其他评论</label></th>
        <td>
            <?php echo help('为了收集对此投票调查进行意见收集请设置简单提问') ?>
            <input type="text" name="po_etc" value="<?php echo get_text($po['po_etc']) ?>" id="po_etc" class="frm_input" size="80" maxlength="125">
        </td>
    </tr>
    <tr>
        <th scope="row"><label for="po_level">参与投票会员等级</label></th>
        <td>
            <?php echo help("设置为1时游客也可以参与投票") ?>
            <?php echo get_member_level_select('po_level', 1, 10, $po['po_level']) ?>以上可以参与
        </td>
    </tr>
    <tr>
        <th scope="row"><label for="po_point">积分</label></th>
        <td>
            <?php echo help('参与投票赠送积分') ?>
            <input type="text" name="po_point" value="<?php echo $po['po_point'] ?>" id="po_point" class="frm_input">分
        </td>
    </tr>

    <?php if ($w == 'u') { ?>
    <tr>
        <th scope="row"><label for="po_date">投票开始日期</label></th>
        <td><input type="text" name="po_date" value="<?php echo $po['po_date'] ?>" id="po_date" class="frm_input" maxlength="10"></td>
    </tr>
    <tr>
        <th scope="row"><label for="po_ips">参与投票ip地址</label></th>
        <td><textarea name="po_ips" id="po_ips" readonly rows="10"><?php echo preg_replace("/\n/", " / ", $po['po_ips']) ?></textarea></td>
    </tr>
    <tr>
        <th scope="row"><label for="mb_ids">参与投票会员</label></th>
        <td><textarea name="mb_ids" id="mb_ids" readonly rows="10"><?php echo preg_replace("/\n/", " / ", $po['mb_ids']) ?></textarea></td>
    </tr>
    <?php } ?>
    </tbody>
    </table>

</div>

<div class="btn_confirm01 btn_confirm">
    <input type="submit" value="确定" class="btn_submit" accesskey="s">
    <a href="./poll_list.php?<?php echo $qstr ?>">目录</a>
</div>

</form>

<?php
include_once('./admin.tail.php');
?>
