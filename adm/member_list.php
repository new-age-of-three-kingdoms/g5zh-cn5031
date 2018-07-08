<?php
$sub_menu = "200100";
include_once('./_common.php');

auth_check($auth[$sub_menu], 'r');

$sql_common = " from {$g5['member_table']} ";

$sql_search = " where (1) ";
if ($stx) {
    $sql_search .= " and ( ";
    switch ($sfl) {
        case 'mb_point' :
            $sql_search .= " ({$sfl} >= '{$stx}') ";
            break;
        case 'mb_level' :
            $sql_search .= " ({$sfl} = '{$stx}') ";
            break;
        case 'mb_tel' :
        case 'mb_hp' :
            $sql_search .= " ({$sfl} like '%{$stx}') ";
            break;
        default :
            $sql_search .= " ({$sfl} like '{$stx}%') ";
            break;
    }
    $sql_search .= " ) ";
}

if ($is_admin != 'super')
    $sql_search .= " and mb_level <= '{$member['mb_level']}' ";

if (!$sst) {
    $sst = "mb_datetime";
    $sod = "desc";
}

$sql_order = " order by {$sst} {$sod} ";

$sql = " select count(*) as cnt {$sql_common} {$sql_search} {$sql_order} ";
$row = sql_fetch($sql);
$total_count = $row['cnt'];

$rows = $config['cf_page_rows'];
$total_page  = ceil($total_count / $rows);  // 计算所有页码
if ($page < 1) $page = 1; // 如果没有页码时设置为1
$from_record = ($page - 1) * $rows; // 获取开始行

// 删除账户会员数量
$sql = " select count(*) as cnt {$sql_common} {$sql_search} and mb_leave_date <> '' {$sql_order} ";
$row = sql_fetch($sql);
$leave_count = $row['cnt'];

// 封号会员账号
$sql = " select count(*) as cnt {$sql_common} {$sql_search} and mb_intercept_date <> '' {$sql_order} ";
$row = sql_fetch($sql);
$intercept_count = $row['cnt'];

$listall = '<a href="'.$_SERVER['PHP_SELF'].'" class="ov_listall">全部目录</a>';

$g5['title'] = '会员管理';
include_once('./admin.head.php');

$sql = " select * {$sql_common} {$sql_search} {$sql_order} limit {$from_record}, {$rows} ";
$result = sql_query($sql);

$colspan = 16;
?>

<div class="local_ov01 local_ov">
    <?php echo $listall ?>
    全部会员<?php echo number_format($total_count) ?>名，其中
    <a href="?sst=mb_intercept_date&amp;sod=desc&amp;sfl=<?php echo $sfl ?>&amp;stx=<?php echo $stx ?>">封号 <?php echo number_format($intercept_count) ?></a>名,
    <a href="?sst=mb_leave_date&amp;sod=desc&amp;sfl=<?php echo $sfl ?>&amp;stx=<?php echo $stx ?>">删除 <?php echo number_format($leave_count) ?></a>名
</div>

<form id="fsearch" name="fsearch" class="local_sch01 local_sch" method="get">

<label for="sfl" class="sound_only">搜索类型</label>
<select name="sfl" id="sfl">
    <option value="mb_id"<?php echo get_selected($_GET['sfl'], "mb_id"); ?>>会员ID</option>
    <option value="mb_nick"<?php echo get_selected($_GET['sfl'], "mb_nick"); ?>>昵称</option>
    <option value="mb_name"<?php echo get_selected($_GET['sfl'], "mb_name"); ?>>姓名</option>
    <option value="mb_level"<?php echo get_selected($_GET['sfl'], "mb_level"); ?>>权限</option>
    <option value="mb_email"<?php echo get_selected($_GET['sfl'], "mb_email"); ?>>E-MAIL</option>
    <option value="mb_tel"<?php echo get_selected($_GET['sfl'], "mb_tel"); ?>>电话号码</option>
    <option value="mb_hp"<?php echo get_selected($_GET['sfl'], "mb_hp"); ?>>手机号码</option>
    <option value="mb_point"<?php echo get_selected($_GET['sfl'], "mb_point"); ?>>积分</option>
    <option value="mb_datetime"<?php echo get_selected($_GET['sfl'], "mb_datetime"); ?>>注册时间</option>
    <option value="mb_ip"<?php echo get_selected($_GET['sfl'], "mb_ip"); ?>>IP</option>
    <option value="mb_recommend"<?php echo get_selected($_GET['sfl'], "mb_recommend"); ?>>推荐人</option>
</select>
<label for="stx" class="sound_only">关键词<strong class="sound_only"> 必选项</strong></label>
<input type="text" name="stx" value="<?php echo $stx ?>" id="stx" required class="required frm_input">
<input type="submit" class="btn_submit" value="搜索">

</form>

<div class="local_desc01 local_desc">
    <p>
        删除会员账户时删除会员信息及相关数据会保留会员ID名称及昵称，以防止短时内重复注册给其他会员产生混淆
    </p>
</div>

<?php if ($is_admin == 'super') { ?>
<div class="btn_add01 btn_add">
    <a href="./member_form.php" id="member_add">会员添加</a>
</div>
<?php } ?>

<form name="fmemberlist" id="fmemberlist" action="./member_list_update.php" onsubmit="return fmemberlist_submit(this);" method="post">
<input type="hidden" name="sst" value="<?php echo $sst ?>">
<input type="hidden" name="sod" value="<?php echo $sod ?>">
<input type="hidden" name="sfl" value="<?php echo $sfl ?>">
<input type="hidden" name="stx" value="<?php echo $stx ?>">
<input type="hidden" name="page" value="<?php echo $page ?>">

<div class="tbl_head02 tbl_wrap">
    <table>
    <caption><?php echo $g5['title']; ?> 目录</caption>
    <thead>
    <tr>
        <th scope="col" rowspan="2" id="mb_list_chk">
            <label for="chkall" class="sound_only">全部会员</label>
            <input type="checkbox" name="chkall" value="1" id="chkall" onclick="check_all(this.form)">
        </th>
        <th scope="col" rowspan="2" id="mb_list_id"><?php echo subject_sort_link('mb_id') ?>会员ID</a></th>
        <th scope="col" id="mb_list_name"><?php echo subject_sort_link('mb_name') ?>姓名</a></th>
        <th scope="col" colspan="6" id="mb_list_cert"><?php echo subject_sort_link('mb_certify', '', 'desc') ?>实名认证</a></th>
        <th scope="col" id="mb_list_mobile">手机</th>
        <th scope="col" id="mb_list_auth">状态/<?php echo subject_sort_link('mb_level', '', 'desc') ?>权限</a></th>
        <th scope="col" id="mb_list_lastcall"><?php echo subject_sort_link('mb_today_login', '', 'desc') ?>最后登录</a></th>
        <th scope="col" rowspan="2" id="mb_list_grp">授权<br>群组</th>
        <th scope="col" rowspan="2" id="mb_list_mng">管理</th>
    </tr>
    <tr>
        <th scope="col" id="mb_list_nick"><?php echo subject_sort_link('mb_nick') ?>昵称</a></th>
        <th scope="col" id="mb_list_mailc"><?php echo subject_sort_link('mb_email_certify', '', 'desc') ?>邮件<br>认证</a></th>
        <th scope="col" id="mb_list_open"><?php echo subject_sort_link('mb_open', '', 'desc') ?>信息<br>设置</a></th>
        <th scope="col" id="mb_list_mailr"><?php echo subject_sort_link('mb_mailling', '', 'desc') ?>邮件<br>订阅</a></th>
        <th scope="col" id="mb_list_sms"><?php echo subject_sort_link('mb_sms', '', 'desc') ?>短信<br>订阅</a></th>
        <th scope="col" id="mb_list_adultc"><?php echo subject_sort_link('mb_adult', '', 'desc') ?>成人<br>认证</a></th>
        <th scope="col" id="mb_list_deny"><?php echo subject_sort_link('mb_intercept_date', '', 'desc') ?>禁止<br>访问</a></th>
        <th scope="col" id="mb_list_tel">电话号码</th>
        <th scope="col" id="mb_list_point"><?php echo subject_sort_link('mb_point', '', 'desc') ?> 积分</a></th>
        <th scope="col" id="mb_list_join"><?php echo subject_sort_link('mb_datetime', '', 'desc') ?>注册日期</a></th>
    </tr>
    </thead>
    <tbody>
    <?php
    for ($i=0; $row=sql_fetch_array($result); $i++) {
        // 允许访问群组
        $sql2 = " select count(*) as cnt from {$g5['group_member_table']} where mb_id = '{$row['mb_id']}' ";
        $row2 = sql_fetch($sql2);
        $group = '';
        if ($row2['cnt'])
            $group = '<a href="./boardgroupmember_form.php?mb_id='.$row['mb_id'].'">'.$row2['cnt'].'</a>';

        if ($is_admin == 'group') {
            $s_mod = '';
            $s_del = '';
        } else {
            $s_mod = '<a href="./member_form.php?'.$qstr.'&amp;w=u&amp;mb_id='.$row['mb_id'].'">修改</a>';
            //$s_del = '<a href="javascript:post_delete(\'member_delete.php\', \''.$row['mb_id'].'\');">删除</a>';
        }
        $s_grp = '<a href="./boardgroupmember_form.php?mb_id='.$row['mb_id'].'">群组</a>';

        $leave_date = $row['mb_leave_date'] ? $row['mb_leave_date'] : date('Ymd', G5_SERVER_TIME);
        $intercept_date = $row['mb_intercept_date'] ? $row['mb_intercept_date'] : date('Ymd', G5_SERVER_TIME);

        $mb_nick = get_sideview($row['mb_id'], $row['mb_nick'], $row['mb_email'], $row['mb_homepage']);

        $mb_id = $row['mb_id'];
        $leave_msg = '';
        $intercept_msg = '';
        $intercept_title = '';
        if ($row['mb_leave_date']) {
            $mb_id = $mb_id;
            $leave_msg = '<span class="mb_leave_msg">已删除</span>';
        }
        else if ($row['mb_intercept_date']) {
            $mb_id = $mb_id;
            $intercept_msg = '<span class="mb_intercept_msg">已封号</span>';
            $intercept_title = '解除封号';
        }
        if ($intercept_title == '')
            $intercept_title = '禁止访问';

        $address = $row['mb_zip1'] ? print_address($row['mb_addr1'], $row['mb_addr2'], $row['mb_addr3'], $row['mb_addr_jibeon']) : '';

        $bg = 'bg'.($i%2);

        switch($row['mb_certify']) {
            case 'hp':
                $mb_certify_case = '手机';
                $mb_certify_val = 'hp';
                break;
            case 'ipin':
                $mb_certify_case = '实名';
                $mb_certify_val = '';
                break;
            case 'admin':
                $mb_certify_case = '管理员';
                $mb_certify_val = 'admin';
                break;
            default:
                $mb_certify_case = '&nbsp;';
                $mb_certify_val = 'admin';
                break;
        }
    ?>

    <tr class="<?php echo $bg; ?>">
        <td headers="mb_list_chk" class="td_chk" rowspan="2">
            <input type="hidden" name="mb_id[<?php echo $i ?>]" value="<?php echo $row['mb_id'] ?>" id="mb_id_<?php echo $i ?>">
            <label for="chk_<?php echo $i; ?>" class="sound_only"><?php echo get_text($row['mb_name']); ?> <?php echo $row['mb_nick']; ?></label>
            <input type="checkbox" name="chk[]" value="<?php echo $i ?>" id="chk_<?php echo $i ?>">
        </td>
        <td headers="mb_list_id" rowspan="2" class="td_name sv_use"><?php echo $mb_id ?></td>
        <td headers="mb_list_name" class="td_mbname"><?php echo get_text($row['mb_name']); ?></td>
        <td headers="mb_list_cert" colspan="6" class="td_mbcert">
            <input type="radio" name="mb_certify[<?php echo $i; ?>]" value="ipin" id="mb_certify_ipin_<?php echo $i; ?>" <?php echo $row['mb_certify']=='ipin'?'checked':''; ?>>
            <label for="mb_certify_ipin_<?php echo $i; ?>">实名</label>
            <input type="radio" name="mb_certify[<?php echo $i; ?>]" value="hp" id="mb_certify_hp_<?php echo $i; ?>" <?php echo $row['mb_certify']=='hp'?'checked':''; ?>>
            <label for="mb_certify_hp_<?php echo $i; ?>">手机</label>
        </td>
        <td headers="mb_list_mobile" class="td_tel"><?php echo $row['mb_hp']; ?></td>
        <td headers="mb_list_auth" class="td_mbstat">
            <?php
            if ($leave_msg || $intercept_msg) echo $leave_msg.' '.$intercept_msg;
            else echo "正常";
            ?>
            <?php echo get_member_level_select("mb_level[$i]", 1, $member['mb_level'], $row['mb_level']) ?>
        </td>
        <td headers="mb_list_lastcall" class="td_date"><?php echo substr($row['mb_today_login'],2,8); ?></td>
        <td headers="mb_list_grp" rowspan="2" class="td_numsmall"><?php echo $group ?></td>
        <td headers="mb_list_mng" rowspan="2" class="td_mngsmall"><?php echo $s_mod ?> <?php echo $s_grp ?></td>
    </tr>
    <tr class="<?php echo $bg; ?>">
        <td headers="mb_list_nick" class="td_name sv_use"><div><?php echo $mb_nick ?></div></td>
        <td headers="mb_list_mailc" class="td_chk"><?php echo preg_match('/[1-9]/', $row['mb_email_certify'])?'<span class="txt_true">Yes</span>':'<span class="txt_false">No</span>'; ?></td>
        <td headers="mb_list_open" class="td_chk">
            <label for="mb_open_<?php echo $i; ?>" class="sound_only">信息公开设置</label>
            <input type="checkbox" name="mb_open[<?php echo $i; ?>]" <?php echo $row['mb_open']?'checked':''; ?> value="1" id="mb_open_<?php echo $i; ?>">
        </td>
        <td headers="mb_list_mailr" class="td_chk">
            <label for="mb_mailling_<?php echo $i; ?>" class="sound_only">邮件订阅</label>
            <input type="checkbox" name="mb_mailling[<?php echo $i; ?>]" <?php echo $row['mb_mailling']?'checked':''; ?> value="1" id="mb_mailling_<?php echo $i; ?>">
        </td>
        <td headers="mb_list_sms" class="td_chk">
            <label for="mb_sms_<?php echo $i; ?>" class="sound_only">短信订阅</label>
            <input type="checkbox" name="mb_sms[<?php echo $i; ?>]" <?php echo $row['mb_sms']?'checked':''; ?> value="1" id="mb_sms_<?php echo $i; ?>">
        </td>
        <td headers="mb_list_adultc" class="td_chk">
            <label for="mb_adult_<?php echo $i; ?>" class="sound_only">成人认证</label>
            <input type="checkbox" name="mb_adult[<?php echo $i; ?>]" <?php echo $row['mb_adult']?'checked':''; ?> value="1" id="mb_adult_<?php echo $i; ?>">
        </td>
        <td headers="mb_list_deny" class="td_chk">
            <?php if(empty($row['mb_leave_date'])){ ?>
            <input type="checkbox" name="mb_intercept_date[<?php echo $i; ?>]" <?php echo $row['mb_intercept_date']?'checked':''; ?> value="<?php echo $intercept_date ?>" id="mb_intercept_date_<?php echo $i ?>" title="<?php echo $intercept_title ?>">
            <label for="mb_intercept_date_<?php echo $i; ?>" class="sound_only">禁止访问</label>
            <?php } ?>
        </td>
        <td headers="mb_list_tel" class="td_tel"><?php echo $row['mb_tel']; ?></td>
        <td headers="mb_list_point" class="td_num"><a href="point_list.php?sfl=mb_id&amp;stx=<?php echo $row['mb_id'] ?>"><?php echo number_format($row['mb_point']) ?></a></td>
        <td headers="mb_list_join" class="td_date"><?php echo substr($row['mb_datetime'],2,8); ?></td>
    </tr>

    <?php
    }
    if ($i == 0)
        echo "<tr><td colspan=\"".$colspan."\" class=\"empty_table\">未找到相应信息</td></tr>";
    ?>
    </tbody>
    </table>
</div>

<div class="btn_list01 btn_list">
    <input type="submit" name="act_button" value="编辑所选" onclick="document.pressed=this.value">
    <input type="submit" name="act_button" value="删除所选" onclick="document.pressed=this.value">
</div>

</form>

<?php echo get_paging(G5_IS_MOBILE ? $config['cf_mobile_pages'] : $config['cf_write_pages'], $page, $total_page, '?'.$qstr.'&amp;page='); ?>

<script>
function fmemberlist_submit(f)
{
    if (!is_checked("chk[]")) {
        alert("请选择需要"+document.pressed+"的项目");
        return false;
    }

    if(document.pressed == "删除所选") {
        if(!confirm("点击确定删除所选内容")) {
            return false;
        }
    }

    return true;
}
</script>

<?php
include_once ('./admin.tail.php');
?>
