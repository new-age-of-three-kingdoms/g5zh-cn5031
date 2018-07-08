<?php
if (!defined('_GNUBOARD_')) exit; //禁止单独访问此页

// add_stylesheet('css 文本', 显示顺序); 数字越小的优先显示
add_stylesheet('<link rel="stylesheet" href="'.$member_skin_url.'/style.css">', 0);
?>

<div class="mbskin">
    <script src="<?php echo G5_JS_URL ?>/jquery.register_form.js"></script>
    <?php if($config['cf_cert_use'] && ($config['cf_cert_ipin'] || $config['cf_cert_hp'])) { ?>
    <script src="<?php echo G5_JS_URL ?>/certify.js"></script>
    <?php } ?>

    <form name="fregisterform" id="fregisterform" action="<?php echo $register_action_url ?>" onsubmit="return fregisterform_submit(this);" method="post" enctype="multipart/form-data" autocomplete="off">
    <input type="hidden" name="w" value="<?php echo $w ?>">
    <input type="hidden" name="url" value="<?php echo $urlencode ?>">
    <input type="hidden" name="agree" value="<?php echo $agree ?>">
    <input type="hidden" name="agree2" value="<?php echo $agree2 ?>">
    <input type="hidden" name="cert_type" value="<?php echo $member['mb_certify']; ?>">
    <input type="hidden" name="cert_no" value="">
    <?php if (isset($member['mb_sex'])) { ?><input type="hidden" name="mb_sex" value="<?php echo $member['mb_sex'] ?>"><?php } ?>
    <?php if (isset($member['mb_nick_date']) && $member['mb_nick_date'] > date("Y-m-d", G5_SERVER_TIME - ($config['cf_nick_modify'] * 86400))) { // 如果未达到指定昵称修改期限 ?>
    <input type="hidden" name="mb_nick_default" value="<?php echo $member['mb_nick'] ?>">
    <input type="hidden" name="mb_nick" value="<?php echo $member['mb_nick'] ?>">
    <?php } ?>

    <div class="tbl_frm01 tbl_wrap">
        <table>
        <caption>会员账户注册信息</caption>
        <tr>
            <th scope="row"><label for="reg_mb_id">会员ID<strong class="sound_only">必选项</strong></label></th>
            <td>
                <span class="frm_info">可以使用英文、数字、下划线设定，至少需要3字节以上。</span>
                <input type="text" name="mb_id" value="<?php echo $member['mb_id'] ?>" id="reg_mb_id" class="frm_input <?php echo $required ?> <?php echo $readonly ?>" minlength="3" maxlength="20" <?php echo $required ?> <?php echo $readonly ?>>
                <span id="msg_mb_id"></span>
            </td>
        </tr>
        <tr>
            <th scope="row"><label for="reg_mb_password">密码<strong class="sound_only">必选项</strong></label></th>
            <td><input type="password" name="mb_password" id="reg_mb_password" class="frm_input <?php echo $required ?>" minlength="3" maxlength="20" <?php echo $required ?>></td>
        </tr>
        <tr>
            <th scope="row"><label for="reg_mb_password_re">密码重复确认<strong class="sound_only">必选项</strong></label></th>
            <td><input type="password" name="mb_password_re" id="reg_mb_password_re" class="frm_input <?php echo $required ?>" minlength="3" maxlength="20" <?php echo $required ?>></td>
        </tr>
        </table>
    </div>

    <div class="tbl_frm01 tbl_wrap">
        <table>
        <caption>个人信息设置</caption>
        <tr>
            <th scope="row"><label for="reg_mb_name">姓名<strong class="sound_only">必选项</strong></label></th>
            <td>
                <?php if ($config['cf_cert_use']) { ?>
                <span class="frm_info">实名认证完成后您的姓名将会自动输入、如进行手机认证您的手机号码将会使用认证的号码，将无法进行手动修改！</span>
                <?php } ?>
                <input type="text" id="reg_mb_name" name="mb_name" value="<?php echo $member['mb_name'] ?>" <?php echo $required ?> <?php echo $readonly; ?> class="frm_input <?php echo $required ?> <?php echo $readonly ?>">
                <?php
                if($config['cf_cert_use']) {
                    if($config['cf_cert_ipin'])
                        echo '<button type="button" id="win_ipin_cert" class="btn_frmline">实名认证</button>'.PHP_EOL;
                    if($config['cf_cert_hp'] && $config['cf_cert_hp'] != 'lg')
                        echo '<button type="button" id="win_hp_cert" class="btn_frmline">手机认证</button>'.PHP_EOL;

                    echo '<noscript>请允许您的浏览器运行JavaScript程序，否则将无法进行实名认证操作！</noscript>'.PHP_EOL;
                }
                ?>
                <?php
                if ($config['cf_cert_use'] && $member['mb_certify']) {
                    if($member['mb_certify'] == 'ipin')
                        $mb_cert = '实名';
                    else
                        $mb_cert = '手机';
                ?>
                <div id="msg_certify">
                    <strong><?php echo $mb_cert; ?> 实名认证</strong><?php if ($member['mb_adult']) { ?> 及 <strong>成人认证</strong><?php } ?> 完成
                </div>
                <?php } ?>
            </td>
        </tr>
        <?php if ($req_nick) { ?>
        <tr>
            <th scope="row"><label for="reg_mb_nick">昵称<strong class="sound_only">必选项</strong></label></th>
            <td>
                <span class="frm_info">
                    可以使用中文、英文及数字(中文2字以上，英文4字以上)<br>
                    设定的昵称将在 <?php echo (int)$config['cf_nick_modify'] ?>天以内禁止再次修改
                </span>
                <input type="hidden" name="mb_nick_default" value="<?php echo isset($member['mb_nick'])?$member['mb_nick']:''; ?>">
                <input type="text" name="mb_nick" value="<?php echo isset($member['mb_nick'])?$member['mb_nick']:''; ?>" id="reg_mb_nick" required class="frm_input required nospace" maxlength="20">
                <span id="msg_mb_nick"></span>
            </td>
        </tr>
        <?php } ?>

        <tr>
            <th scope="row"><label for="reg_mb_email">E-mail<strong class="sound_only">必选项</strong></label></th>
            <td>
                <?php if ($config['cf_use_email_certify']) {  ?>
                <span class="frm_info">
                    <?php if ($w=='') { echo "您需要完成邮箱地址验证后才能激活您的账户"; }  ?>
                    <?php if ($w=='u') { echo "修改邮件地址需要重新进行邮件地址认证"; }  ?>
                </span>
                <?php }  ?>
                <input type="hidden" name="old_email" value="<?php echo $member['mb_email'] ?>">
                <input type="email" name="mb_email" value="<?php echo isset($member['mb_email'])?$member['mb_email']:''; ?>" id="reg_mb_email" required class="frm_input email required" size="50" maxlength="100">
            </td>
        </tr>

        <?php if ($config['cf_use_homepage']) { ?>
        <tr>
            <th scope="row"><label for="reg_mb_homepage">网站主页<?php if ($config['cf_req_homepage']){ ?><strong class="sound_only">必选项</strong><?php } ?></label></th>
            <td><input type="url" name="mb_homepage" value="<?php echo $member['mb_homepage'] ?>" id="reg_mb_homepage" class="frm_input <?php echo $config['cf_req_homepage']?"required":""; ?>" maxlength="255" <?php echo $config['cf_req_homepage']?"required":""; ?>></td>
        </tr>
        <?php } ?>

        <?php if ($config['cf_use_tel']) { ?>
        <tr>
            <th scope="row"><label for="reg_mb_tel">电话号码<?php if ($config['cf_req_tel']) { ?><strong class="sound_only">必选项</strong><?php } ?></label></th>
            <td><input type="text" name="mb_tel" value="<?php echo $member['mb_tel'] ?>" id="reg_mb_tel" class="frm_input <?php echo $config['cf_req_tel']?"required":""; ?>" maxlength="20" <?php echo $config['cf_req_tel']?"required":""; ?>></td>
        </tr>
        <?php } ?>

        <?php if ($config['cf_use_hp']) {  ?>
        <tr>
            <th scope="row"><label for="reg_mb_hp">手机号码<?php if ($config['cf_req_hp']) { ?><strong class="sound_only">必选项</strong><?php } ?></label></th>
            <td>
                <input type="text" name="mb_hp" value="<?php echo $member['mb_hp'] ?>" id="reg_mb_hp" <?php echo ($config['cf_req_hp'])?"required":""; ?> class="frm_input <?php echo ($config['cf_req_hp'])?"required":""; ?>" maxlength="20">
                <?php if ($config['cf_cert_use'] && $config['cf_cert_hp']) { ?>
                <input type="hidden" name="old_mb_hp" value="<?php echo $member['mb_hp'] ?>">
                <?php } ?>
            </td>
        </tr>
        <?php } ?>

        <?php if ($config['cf_use_addr']) { ?>
        <tr>
            <th scope="row">
                地址
                <?php if ($config['cf_req_addr']) { ?><strong class="sound_only">必选项</strong><?php } ?>
            </th>
            <td>
                <label for="reg_mb_zip1" class="sound_only">邮编前三位<?php echo $config['cf_req_addr']?'<strong class="sound_only"> 必选项</strong>':''; ?></label>
                <input type="text" name="mb_zip1" value="<?php echo $member['mb_zip1'] ?>" id="reg_mb_zip1" <?php echo $config['cf_req_addr']?"required":""; ?> class="frm_input <?php echo $config['cf_req_addr']?"required":""; ?>" size="3" maxlength="3">
                -
                <label for="reg_mb_zip2" class="sound_only">邮编后三位<?php echo $config['cf_req_addr']?'<strong class="sound_only"> 必选项</strong>':''; ?></label>
                <input type="text" name="mb_zip2" value="<?php echo $member['mb_zip2'] ?>" id="reg_mb_zip2" <?php echo $config['cf_req_addr']?"required":""; ?> class="frm_input <?php echo $config['cf_req_addr']?"required":""; ?>" size="3" maxlength="3">
                <button type="button" class="btn_frmline" onclick="win_zip('fregisterform', 'mb_zip1', 'mb_zip2', 'mb_addr1', 'mb_addr2', 'mb_addr3', 'mb_addr_jibeon');">搜索地址</button><br>
                <label for="reg_mb_addr1" class="sound_only">地址<?php echo $config['cf_req_addr']?'<strong class="sound_only"> 必选项</strong>':''; ?></label>
                <input type="text" name="mb_addr1" value="<?php echo $member['mb_addr1'] ?>" id="reg_mb_addr1" <?php echo $config['cf_req_addr']?"required":""; ?> class="frm_input frm_address <?php echo $config['cf_req_addr']?"required":""; ?>" size="50"><br>
                <label for="reg_mb_addr2" class="sound_only">详细地址</label>
                <input type="text" name="mb_addr2" value="<?php echo $member['mb_addr2'] ?>" id="reg_mb_addr2" class="frm_input frm_address" size="50">
                <br>
                <label for="reg_mb_addr3" class="sound_only">参考备注</label>
                <input type="text" name="mb_addr3" value="<?php echo $member['mb_addr3'] ?>" id="reg_mb_addr3" class="frm_input frm_address" size="50" readonly="readonly">
                <input type="hidden" name="mb_addr_jibeon" value="<?php echo $member['mb_addr_jibeon']; ?>">
            </td>
        </tr>
        <?php } ?>
        </table>
    </div>

    <div class="tbl_frm01 tbl_wrap">
        <table>
        <caption>其他个人设置</caption>
        <?php if ($config['cf_use_signature']) { ?>
        <tr>
            <th scope="row"><label for="reg_mb_signature">签名<?php if ($config['cf_req_signature']){ ?><strong class="sound_only">必选项</strong><?php } ?></label></th>
            <td><textarea name="mb_signature" id="reg_mb_signature" class="<?php echo $config['cf_req_signature']?"required":""; ?>" <?php echo $config['cf_req_signature']?"required":""; ?>><?php echo $member['mb_signature'] ?></textarea></td>
        </tr>
        <?php } ?>

        <?php if ($config['cf_use_profile']) { ?>
        <tr>
            <th scope="row"><label for="reg_mb_profile">自我介绍</label></th>
            <td><textarea name="mb_profile" id="reg_mb_profile" class="<?php echo $config['cf_req_profile']?"required":""; ?>" <?php echo $config['cf_req_profile']?"required":""; ?>><?php echo $member['mb_profile'] ?></textarea></td>
        </tr>
        <?php } ?>

        <?php if ($config['cf_use_member_icon'] && $member['mb_level'] >= $config['cf_icon_level']) { ?>
        <tr>
            <th scope="row"><label for="reg_mb_icon">会员头像</label></th>
            <td>
                <span class="frm_info">
                    图片尺寸要求，宽度：<?php echo $config['cf_member_icon_width'] ?>像素, 高 <?php echo $config['cf_member_icon_height'] ?>像素以内<br>
                    仅限使用gif格式，大小： <?php echo number_format($config['cf_member_icon_size']) ?>字节以下。
                </span>
                <input type="file" name="mb_icon" id="reg_mb_icon" class="frm_input">
                <?php if ($w == 'u' && file_exists($mb_icon_path)) { ?>
                <img src="<?php echo $mb_icon_url ?>" alt="会员头像">
                <input type="checkbox" name="del_mb_icon" value="1" id="del_mb_icon">
                <label for="del_mb_icon">删除</label>
                <?php } ?>
            </td>
        </tr>
        <?php } ?>

        <tr>
            <th scope="row"><label for="reg_mb_mailling">邮件通知服务</label></th>
            <td>
                <input type="checkbox" name="mb_mailling" value="1" id="reg_mb_mailling" <?php echo ($w=='' || $member['mb_mailling'])?'checked':''; ?>>
                我愿意接收来自本站的邮件信息
            </td>
        </tr>

        <?php if ($config['cf_use_hp']) { ?>
        <tr>
            <th scope="row"><label for="reg_mb_sms">SMS短信设置</label></th>
            <td>
                <input type="checkbox" name="mb_sms" value="1" id="reg_mb_sms" <?php echo ($w=='' || $member['mb_sms'])?'checked':''; ?>>
                我愿意接收来自本站的短信信息
            </td>
        </tr>
        <?php } ?>

        <?php if (isset($member['mb_open_date']) && $member['mb_open_date'] <= date("Y-m-d", G5_SERVER_TIME - ($config['cf_open_modify'] * 86400)) || empty($member['mb_open_date'])) { // 达到信息状态限定时间允许修改 ?>
        <tr>
            <th scope="row"><label for="reg_mb_open">信息公开设置</label></th>
            <td>
                <span class="frm_info">
                    修改信息公开设置状态后将在 <?php echo (int)$config['cf_open_modify'] ?>天以内禁止再次修改
                </span>
                <input type="hidden" name="mb_open_default" value="<?php echo $member['mb_open'] ?>">
                <input type="checkbox" name="mb_open" value="1" id="reg_mb_open" <?php echo ($w=='' || $member['mb_open'])?'checked':''; ?>>
                允许其他会员查看我的基本信息
            </td>
        </tr>
        <?php } else { ?>
        <tr>
            <th scope="row">信息公开设置</th>
            <td>
                <span class="frm_info">
                    信息公开设置修改后 <?php echo (int)$config['cf_open_modify'] ?>天以内, <?php echo date("Y年 m月 j日", isset($member['mb_open_date']) ? strtotime("{$member['mb_open_date']} 00:00:00")+$config['cf_open_modify']*86400:G5_SERVER_TIME+$config['cf_open_modify']*86400); ?> 禁止被修改<br>
                    如果不公开基本设置将同时禁用站内短信功能，您将无法接收及发送站内短信
                </span>
                <input type="hidden" name="mb_open" value="<?php echo $member['mb_open'] ?>">
            </td>
        </tr>
        <?php } ?>

        <?php if ($w == "" && $config['cf_use_recommend']) { ?>
        <tr>
            <th scope="row"><label for="reg_mb_recommend">推荐人ID</label></th>
            <td><input type="text" name="mb_recommend" id="reg_mb_recommend" class="frm_input"></td>
        </tr>
        <?php } ?>

        <tr>
            <th scope="row">验证码</th>
            <td><?php echo captcha_html(); ?></td>
        </tr>
        </table>
    </div>

    <div class="btn_confirm">
        <input type="submit" value="<?php echo $w==''?'注册会员':'信息设置'; ?>" id="btn_submit" class="btn_submit" accesskey="s">
        <a href="<?php echo G5_URL; ?>/" class="btn_cancel">取消</a>
    </div>
    </form>

    <script>
    $(function() {
        $("#reg_zip_find").css("display", "inline-block");

        <?php if($config['cf_cert_use'] && $config['cf_cert_ipin']) { ?>
        // 实名认证
        $("#win_ipin_cert").click(function() {
            if(!cert_confirm())
                return false;

            var url = "<?php echo G5_OKNAME_URL; ?>/ipin1.php";
            certify_win_open('kcb-ipin', url);
            return;
        });

        <?php } ?>
        <?php if($config['cf_cert_use'] && $config['cf_cert_hp']) { ?>
        // 手机认证
        $("#win_hp_cert").click(function() {
            if(!cert_confirm())
                return false;

            <?php
            switch($config['cf_cert_hp']) {
                case 'kcb':
                    $cert_url = G5_OKNAME_URL.'/hpcert1.php';
                    $cert_type = 'kcb-hp';
                    break;
                case 'kcp':
                    $cert_url = G5_KCPCERT_URL.'/kcpcert_form.php';
                    $cert_type = 'kcp-hp';
                    break;
                default:
                    echo 'alert("请在网站设置中设置开启手机认证程序后使用");';
                    echo 'return false;';
                    break;
            }
            ?>

            certify_win_open("<?php echo $cert_type; ?>", "<?php echo $cert_url; ?>");
            return;
        });
        <?php } ?>
    });

    // 认证检测
    function cert_confirm()
    {
        var val = document.fregisterform.cert_type.value;
        var type;

        switch(val) {
            case "ipin":
                type = "实名";
                break;
            case "hp":
                type = "手机";
                break;
            default:
                return true;
        }

        if(confirm("您已进行过"+type+"认证\n\n需要删除原有认证，进行新的认证吗？"))
            return true;
        else
            return false;
    }

    // submit 最终检测确认
    function fregisterform_submit(f)
    {
        // 会员ID 检查
        if (f.w.value == "") {
            var msg = reg_mb_id_check();
            if (msg) {
                alert(msg);
                f.mb_id.select();
                return false;
            }
        }

        if (f.w.value == '') {
            if (f.mb_password.value.length < 3) {
                alert('密码必须输入3个以上字符组成');
                f.mb_password.focus();
                return false;
            }
        }

        if (f.mb_password.value != f.mb_password_re.value) {
            alert('您输入的密码不一致');
            f.mb_password_re.focus();
            return false;
        }

        if (f.mb_password.value.length > 0) {
            if (f.mb_password_re.value.length < 3) {
                alert('密码必须输入3个以上字符组成');
                f.mb_password_re.focus();
                return false;
            }
        }

        // 姓名 检查
        if (f.w.value=='') {
            if (f.mb_name.value.length < 1) {
                alert('请输入姓名');
                f.mb_name.focus();
                return false;
            }
        }

        <?php if($w == '' && $config['cf_cert_use'] && $config['cf_cert_req']) { ?>
        // 实名认证检查
        if(f.cert_no.value=="") {
            alert("为了完成注册需要进行实名认证");
            return false;
        }
        <?php } ?>

        // 昵称检查
        if ((f.w.value == "") || (f.w.value == "u" && f.mb_nick.defaultValue != f.mb_nick.value)) {
            var msg = reg_mb_nick_check();
            if (msg) {
                alert(msg);
                f.reg_mb_nick.select();
                return false;
            }
        }

        // E-mail 检查
        if ((f.w.value == "") || (f.w.value == "u" && f.mb_email.defaultValue != f.mb_email.value)) {
            var msg = reg_mb_email_check();
            if (msg) {
                alert(msg);
                f.reg_mb_email.select();
                return false;
            }
        }

        <?php if (($config['cf_use_hp'] || $config['cf_cert_hp']) && $config['cf_req_hp']) {  ?>
        // 手机号码检查
        var msg = reg_mb_hp_check();
        if (msg) {
            alert(msg);
            f.reg_mb_hp.select();
            return false;
        }
        <?php } ?>

        if (typeof f.mb_icon != 'undefined') {
            if (f.mb_icon.value) {
                if (!f.mb_icon.value.toLowerCase().match(/.(gif)$/i)) {
                    alert('会员头像只能使用gif格式');
                    f.mb_icon.focus();
                    return false;
                }
            }
        }

        if (typeof(f.mb_recommend) != 'undefined' && f.mb_recommend.value) {
            if (f.mb_id.value == f.mb_recommend.value) {
                alert('您不能设置自己为推荐人');
                f.mb_recommend.focus();
                return false;
            }

            var msg = reg_mb_recommend_check();
            if (msg) {
                alert(msg);
                f.mb_recommend.select();
                return false;
            }
        }

        <?php echo chk_captcha_js(); ?>

        document.getElementById("btn_submit").disabled = "disabled";

        return true;
    }
    </script>
</div>