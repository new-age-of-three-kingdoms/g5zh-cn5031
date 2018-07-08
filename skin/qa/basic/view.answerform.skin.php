<?php
if (!defined("_GNUBOARD_")) exit; //禁止单独访问此页
?>

<section id="bo_v_ans">
    <?php
    if($is_admin) // 如果是管理员就允许刊登答案
    {
    ?>
    <h2>提交回复</h2>

    <form name="fanswer" method="post" action="./qawrite_update.php" onsubmit="return fwrite_submit(this);" autocomplete="off">
    <input type="hidden" name="qa_id" value="<?php echo $view['qa_id']; ?>">
    <input type="hidden" name="w" value="a">
    <input type="hidden" name="sca" value="<?php echo $sca ?>">
    <input type="hidden" name="stx" value="<?php echo $stx; ?>">
    <input type="hidden" name="page" value="<?php echo $page; ?>">
    <?php
    $option = '';
    $option_hidden = '';
    $option = '';

    if ($is_dhtml_editor) {
        $option_hidden .= '<input type="hidden" name="qa_html" value="1">';
    } else {
        $option .= "\n".'<input type="checkbox" id="qa_html" name="qa_html" onclick="html_auto_br(this);" value="'.$html_value.'" '.$html_checked.'>'."\n".'<label for="qa_html">html</label>';
    }

    echo $option_hidden;
    ?>

    <div class="tbl_frm01 tbl_wrap">
        <table>
        <tbody>
        <?php if ($option) { ?>
        <tr>
            <th scope="row">选项</th>
            <td><?php echo $option; ?></td>
        </tr>
        <?php } ?>
        <tr>
            <th><label for="qa_subject">主题</label></th>
            <td><input type="text" name="qa_subject" value="" id="qa_subject" required class="frm_input required" size="50" maxlength="255"></td>
        </tr>
        <tr>
        <th scope="row"><label for="qa_content">内容<strong class="sound_only">必选项</strong></label></th>
            <td class="wr_content">
                <?php echo $editor_html; // 开启编辑器时直接显示编辑器否则显示输入框textarea ?>
            </td>
        </tr>
        </tbody>
        </table>
    </div>

    <div class="btn_confirm">
        <input type="submit" value="发表回复" id="btn_submit" accesskey="s" class="btn_submit">
    </div>
    </form>

    <script>
    function html_auto_br(obj)
    {
        if (obj.checked) {
            result = confirm("需要开启自动换行功能吗？\n\n自动换行功能将自动将文本内容换行处代码更换为<BR>");
            if (result)
                obj.value = "2";
            else
                obj.value = "1";
        }
        else
            obj.value = "";
    }

    function fwrite_submit(f)
    {
        <?php echo $editor_js; // 检测文本输入是否完成   ?>

        var subject = "";
        var content = "";
        $.ajax({
            url: g5_bbs_url+"/ajax.filter.php",
            type: "POST",
            data: {
                "subject": f.qa_subject.value,
                "content": f.qa_content.value
            },
            dataType: "json",
            async: false,
            cache: false,
            success: function(data, textStatus) {
                subject = data.subject;
                content = data.content;
            }
        });

        if (subject) {
            alert("标题中包含禁用词组('"+subject+"')");
            f.qa_subject.focus();
            return false;
        }

        if (content) {
            alert("内容中包含禁用词组('"+content+"')");
            if (typeof(ed_qa_content) != "undefined")
                ed_qa_content.returnFalse();
            else
                f.qa_content.focus();
            return false;
        }

        document.getElementById("btn_submit").disabled = "disabled";

        return true;
    }
    </script>
    <?php
    }
    else
    {
    ?>
    <p id="ans_msg">您的提问已受理，我们将尽快为您进行解答</p>
    <?php
    }
    ?>
</section>