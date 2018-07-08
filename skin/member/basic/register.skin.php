<?php
if (!defined('_GNUBOARD_')) exit; //禁止单独访问此页

// add_stylesheet('css 文本', 显示顺序); 数字越小的优先显示
add_stylesheet('<link rel="stylesheet" href="'.$member_skin_url.'/style.css">', 0);
?>

<!-- 会员注册条款开始 { -->
<div class="mbskin">
    <form  name="fregister" id="fregister" action="<?php echo $register_action_url ?>" onsubmit="return fregister_submit(this);" method="POST" autocomplete="off">

    <p>请仔细浏览会员注册条款，只有同意该条款才能进行注册</p>

    <section id="fregister_term">
        <h2>会员注册条款</h2>
        <textarea readonly><?php echo get_text($config['cf_stipulation']) ?></textarea>
        <fieldset class="fregister_agree">
            <label for="agree11">同意会员注册条款</label>
            <input type="checkbox" name="agree" value="1" id="agree11">
        </fieldset>
    </section>

    <section id="fregister_private">
        <h2>个人隐私保护条例指南</h2>
        <textarea readonly><?php echo get_text($config['cf_privacy']) ?></textarea>
        <fieldset class="fregister_agree">
            <label for="agree21">同意个人隐私保护条例</label>
            <input type="checkbox" name="agree2" value="1" id="agree21">
        </fieldset>
    </section>

    <div class="btn_confirm">
        <input type="submit" class="btn_submit" value="注册会员">
    </div>

    </form>

    <script>
    function fregister_submit(f)
    {
        if (!f.agree.checked) {
            alert("您需要同意会员注册条款才能进行注册");
            f.agree.focus();
            return false;
        }

        if (!f.agree2.checked) {
            alert("您需要同意个人隐私保护条例才能进行注册");
            f.agree2.focus();
            return false;
        }

        return true;
    }
    </script>
</div>
<!-- } 会员注册条款结束 -->