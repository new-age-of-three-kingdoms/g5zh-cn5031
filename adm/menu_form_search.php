<?php
include_once('./_common.php');

if ($is_admin != 'super')
    die('您没有访问权限');

switch($type) {
    case 'group':
        $sql = " select gr_id as id, gr_subject as subject
                    from {$g5['group_table']}
                    order by gr_order, gr_id ";
        break;
    case 'board':
        $sql = " select bo_table as id, bo_subject as subject
                    from {$g5['board_table']}
                    order by bo_order, bo_table ";
        break;
    case 'content':
        $sql = " select co_id as id, co_subject as subject
                    from {$g5['content_table']}
                    order by co_id ";
        break;
    default:
        $sql = '';
        break;
}
?>

<?php
if($sql) {
    $result = sql_query($sql);

    for($i=0; $row=sql_fetch_array($result); $i++) {
        if($i == 0) {
?>

<div class="tbl_head01 tbl_wrap">
    <table>
    <thead>
    <tr>
        <th scope="col">主题</th>
        <th scope="col">选择</th>
    </tr>
    </thead>
    <tbody>

<?php }
        switch($type) {
            case 'group':
                $link = G5_BBS_URL.'/group.php?gr_id='.$row['id'];
                break;
            case 'board':
                $link = G5_BBS_URL.'/board.php?bo_table='.$row['id'];
                break;
            case 'content':
                $link = G5_BBS_URL.'/content.php?co_id='.$row['id'];
                break;
            default:
                $link = '';
                break;
        }
?>

    <tr>
        <td><?php echo $row['subject']; ?></td>
        <td class="td_mngsmall">
            <input type="hidden" name="subject[]" value="<?php echo preg_replace('/[\'\"]/', '', $row['subject']); ?>">
            <input type="hidden" name="link[]" value="<?php echo $link; ?>">
            <button type="button" class="add_select"><span class="sound_only"><?php echo $row['subject']; ?> </span>选择</button>
        </td>
    </tr>

<?php } ?>

    </tbody>
    </table>
</div>

<div class="btn_win02 btn_win">
    <button type="button" class="btn_cancel" onclick="window.close();">关闭窗口</button>
</div>

<?php } else { ?>

<div class="tbl_frm01 tbl_wrap">
    <table>
    <colgroup>
        <col class="grid_2">
        <col>
    </colgroup>
    <tbody>
    <tr>
        <th scope="row"><label for="me_name">栏目<strong class="sound_only"> 必选项</strong></label></th>
        <td><input type="text" name="me_name" id="me_name" required class="frm_input required"></td>
    </tr>
    <tr>
        <th scope="row"><label for="me_link">链接<strong class="sound_only"> 必选项</strong></label></th>
        <td>
            <?php echo help('链接地址请包含http://'); ?>
            <input type="text" name="me_link" id="me_link" required class="frm_input full_input required">
        </td>
    </tr>
    </tbody>
    </table>
</div>

<div class="btn_win02 btn_win">
    <button type="button" id="add_manual" class="btn_submit">添加</button>
    <button type="button" class="btn_cancel" onclick="window.close();">关闭窗口</button>
</div>
<?php } ?>