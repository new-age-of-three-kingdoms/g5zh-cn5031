<?php
$sub_menu = "100290";
include_once('./_common.php');

if ($is_admin != 'super')
    alert('您没有访问权限');

$token = get_token();

// 栏目创建数据表
if( !isset($g5['menu_table']) ){
    die('<meta charset="utf-8">请在dbconfig.php文件添加<strong>$g5[\'menu_table\'] = G5_TABLE_PREFIX.\'menu\';</strong>  ');
}

if(!sql_query(" DESCRIBE {$g5['menu_table']} ", false)) {
    sql_query(" CREATE TABLE IF NOT EXISTS `{$g5['menu_table']}` (
                  `me_id` int(11) NOT NULL AUTO_INCREMENT,
                  `me_code` varchar(255) NOT NULL DEFAULT '',
                  `me_name` varchar(255) NOT NULL DEFAULT '',
                  `me_link` varchar(255) NOT NULL DEFAULT '',
                  `me_target` varchar(255) NOT NULL DEFAULT '0',
                  `me_order` int(11) NOT NULL DEFAULT '0',
                  `me_use` tinyint(4) NOT NULL DEFAULT '0',
                  `me_mobile_use` tinyint(4) NOT NULL DEFAULT '0',
                  PRIMARY KEY (`me_id`)
                ) ENGINE=MyISAM DEFAULT CHARSET=utf8 ", true);
}

$sql = " select * from {$g5['menu_table']} order by me_id ";
$result = sql_query($sql);

$g5['title'] = "栏目设置";
include_once('./admin.head.php');

$colspan = 7;
?>

<div class="local_desc01 local_desc">
    <p><strong>注意!</strong>栏目设置完成后需要点击<strong>确定</strong>才能更新修改设置</p>
</div>

<form name="fmenulist" id="fmenulist" method="post" action="./menu_list_update.php" onsubmit="return fmenulist_submit(this);">
<input type="hidden" name="token" value="<?php echo $token ?>">

<div class="btn_add01 btn_add">
    <button type="button" onclick="return add_menu();">栏目添加<span class="sound_only"> 新窗口</span></button>
</div>

<div id="menulist" class="tbl_head01 tbl_wrap">
    <table>
    <caption><?php echo $g5['title']; ?> 目录</caption>
    <thead>
    <tr>
        <th scope="col">栏目</th>
        <th scope="col">链接</th>
        <th scope="col">新窗口</th>
        <th scope="col">顺序</th>
        <th scope="col">PC版</th>
        <th scope="col">触屏版</th>
        <th scope="col">管理</th>
    </tr>
    </thead>
    <tbody>
    <?php
    for ($i=0; $row=sql_fetch_array($result); $i++)
    {
        $bg = 'bg'.($i%2);
        $sub_menu_class = '';
        if(strlen($row['me_code']) == 4) {
            $sub_menu_class = ' sub_menu_class';
            $sub_menu_info = '<span class="sound_only">'.$row['me_name'].'的子栏目</span>';
            $sub_menu_ico = '<span class="sub_menu_ico"></span>';
        }
    ?>
    <tr class="<?php echo $bg; ?> menu_list menu_group_<?php echo substr($row['me_code'], 0, 2); ?>">
        <td class="td_category<?php echo $sub_menu_class; ?>">
            <input type="hidden" name="code[]" value="<?php echo substr($row['me_code'], 0, 2) ?>">
            <label for="me_name_<?php echo $i; ?>" class="sound_only"><?php echo $sub_menu_info; ?> 栏目<strong class="sound_only"> 必选项</strong></label>
            <input type="text" name="me_name[]" value="<?php echo $row['me_name'] ?>" id="me_name_<?php echo $i; ?>" required class="required frm_input full_input">
        </td>
        <td>
            <label for="me_link_<?php echo $i; ?>" class="sound_only">链接<strong class="sound_only"> 必选项</strong></label>
            <input type="text" name="me_link[]" value="<?php echo $row['me_link'] ?>" id="me_link_<?php echo $i; ?>" required class="required frm_input full_input">
        </td>
        <td class="td_mng">
            <label for="me_target_<?php echo $i; ?>" class="sound_only">新窗口</label>
            <select name="me_target[]" id="me_target_<?php echo $i; ?>">
                <option value="self"<?php echo get_selected($row['me_target'], 'self', true); ?>>不使用</option>
                <option value="blank"<?php echo get_selected($row['me_target'], 'blank', true); ?>>使用</option>
            </select>
        </td>
        <td class="td_num">
            <label for="me_order_<?php echo $i; ?>" class="sound_only">顺序</label>
            <input type="text" name="me_order[]" value="<?php echo $row['me_order'] ?>" id="me_order_<?php echo $i; ?>" class="frm_input" size="5">
        </td>
        <td class="td_mng">
            <label for="me_use_<?php echo $i; ?>" class="sound_only">PC版</label>
            <select name="me_use[]" id="me_use_<?php echo $i; ?>">
                <option value="1"<?php echo get_selected($row['me_use'], '1', true); ?>>使用</option>
                <option value="0"<?php echo get_selected($row['me_use'], '0', true); ?>>不使用</option>
            </select>
        </td>
        <td class="td_mng">
            <label for="me_mobile_use_<?php echo $i; ?>" class="sound_only">触屏版</label>
            <select name="me_mobile_use[]" id="me_mobile_use_<?php echo $i; ?>">
                <option value="1"<?php echo get_selected($row['me_mobile_use'], '1', true); ?>>使用</option>
                <option value="0"<?php echo get_selected($row['me_mobile_use'], '0', true); ?>>不使用</option>
            </select>
        </td>
        <td class="td_mng">
            <?php if(strlen($row['me_code']) == 2) { ?>
            <button type="button" class="btn_add_submenu">添加</button>
            <?php } ?>
            <button type="button" class="btn_del_menu">删除</button>
        </td>
    </tr>
    <?php
    }

    if ($i==0)
        echo '<tr id="empty_menu_list"><td colspan="'.$colspan.'" class="empty_table">未找到相应信息</td></tr>';
    ?>
    </tbody>
    </table>
</div>

<div class="btn_confirm01 btn_confirm">
    <input type="submit" name="act_button" value="确定" class="btn_submit">
</div>

</form>

<script>
$(function() {
    $(".btn_add_submenu").live("click", function() {
        var code = $(this).closest("tr").find("input[name='code[]']").val().substr(0, 2);
        add_submenu(code);
    });

    $(".btn_del_menu").live("click", function() {
        if(!confirm("点击确定删除栏目"))
            return false;

        var $tr = $(this).closest("tr");
        if($tr.find("td.sub_menu_class").size() > 0) {
            $tr.remove();
        } else {
            var code = $(this).closest("tr").find("input[name='code[]']").val().substr(0, 2);
            $("tr.menu_group_"+code).remove();
        }

        if($("#menulist tr.menu_list").size() < 1) {
            var list = "<tr id=\"empty_menu_list\"><td colspan=\"<?php echo $colspan; ?>\" class=\"empty_table\">未找到相应信息</td></tr>\n";
            $("#menulist table tbody").append(list);
        } else {
            $("#menulist tr.menu_list").each(function(index) {
                $(this).removeClass("bg0 bg1")
                    .addClass("bg"+(index % 2));
            });
        }
    });
});

function add_menu()
{
    var max_code = base_convert(0, 10, 36);
    $("#menulist tr.menu_list").each(function() {
        var me_code = $(this).find("input[name='code[]']").val().substr(0, 2);
        if(max_code < me_code)
            max_code = me_code;
    });

    var url = "./menu_form.php?code="+max_code+"&new=new";
    window.open(url, "add_menu", "left=100,top=100,width=550,height=650,scrollbars=yes,resizable=yes");
    return false;
}

function add_submenu(code)
{
    var url = "./menu_form.php?code="+code;
    window.open(url, "add_menu", "left=100,top=100,width=550,height=650,scrollbars=yes,resizable=yes");
    return false;
}

function base_convert(number, frombase, tobase) {
  //  discuss at: http://phpjs.org/functions/base_convert/
  // original by: Philippe Baumann
  // improved by: Rafał Kukawski (http://blog.kukawski.pl)
  //   example 1: base_convert('A37334', 16, 2);
  //   returns 1: '101000110111001100110100'

  return parseInt(number + '', frombase | 0)
    .toString(tobase | 0);
}

function fmenulist_submit(f)
{
    return true;
}
</script>

<?php
include_once ('./admin.tail.php');
?>
