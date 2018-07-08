<?php
if (!defined('_GNUBOARD_')) exit; //禁止单独访问此页

// add_stylesheet('css 文本', 显示顺序); 数字越小的优先显示
add_stylesheet('<link rel="stylesheet" href="'.$qa_skin_url.'/style.css">', 0);
?>

<section id="bo_w">
    <!-- 编辑主题/修改主题开始 { -->
    <form name="fwrite" id="fwrite" action="<?php echo $action_url ?>" onsubmit="return fwrite_submit(this);" method="post" enctype="multipart/form-data" autocomplete="off">
    <input type="hidden" name="w" value="<?php echo $w ?>">
    <input type="hidden" name="qa_id" value="<?php echo $qa_id ?>">
    <input type="hidden" name="sca" value="<?php echo $sca ?>">
    <input type="hidden" name="stx" value="<?php echo $stx ?>">
    <input type="hidden" name="page" value="<?php echo $page ?>">
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

    <div class="tbl_frm01 tbl_wrp">
        <table>
        <tbody>
        <?php if ($category_option) { ?>
        <tr>
            <th scope="row"><label for="qa_category">分类<strong class="sound_only">必选项</strong></label></th>
            <td>
                <select name="qa_category" id="qa_category" required class="required" >
                    <option value="">请选择</option>
                    <?php echo $category_option ?>
                </select>
            </td>
        </tr>
        <?php } ?>

        <?php if ($option) { ?>
        <tr>
            <th scope="row">选项</th>
            <td><?php echo $option; ?></td>
        </tr>
        <?php } ?>

        <?php if ($is_email) { ?>
        <tr>
            <th scope="row"><label for="qa_email">邮件地址</label></th>
            <td>
                <input type="text" name="qa_email" value="<?php echo $write['qa_email']; ?>" id="qa_email" <?php echo $req_email; ?> class="<?php echo $req_email.' '; ?>frm_input email" size="50" maxlength="100">
                <input type="checkbox" name="qa_email_recv" value="1" <?php if($write['qa_email_recv']) echo 'checked="checked"'; ?>>
                <label for="qa_email_recv">接收回复</label>
            </td>
        </tr>
        <?php } ?>

        <?php if ($is_hp) { ?>
        <tr>
            <th scope="row"><label for="qa_hp">手机</label></th>
            <td>
                <input type="text" name="qa_hp" value="<?php echo $write['qa_hp']; ?>" id="qa_hp" <?php echo $req_hp; ?> class="<?php echo $req_hp.' '; ?>frm_input" size="30">
                <?php if($qaconfig['qa_use_sms']) { ?>
                <input type="checkbox" name="qa_sms_recv" value="1" <?php if($write['qa_sms_recv']) echo 'checked="checked"'; ?>> 接收短信回复
                <?php } ?>
            </td>
        </tr>
        <?php } ?>

        <tr>
            <th scope="row"><label for="qa_subject">主题<strong class="sound_only">必选项</strong></label></th>
            <td>
                <input type="text" name="qa_subject" value="<?php echo $write['qa_subject']; ?>" id="qa_subject" required class="frm_input required" size="50" maxlength="255">
            </td>
        </tr>

        <tr>
            <th scope="row"><label for="qa_content">内容<strong class="sound_only">必选项</strong></label></th>
            <td class="wr_content">
                <?php echo $editor_html; // 开启编辑器时直接显示编辑器否则显示输入框textarea ?>
            </td>
        </tr>

        <tr>
            <th scope="row">附件 #1</th>
            <td>
                <input type="file" name="bf_file[1]" title="附件 1 :  大小请勿超过 <?php echo $upload_max_filesize; ?>" class="frm_file frm_input">
                <?php if($w == 'u' && $write['qa_file1']) { ?>
                <input type="checkbox" id="bf_file_del1" name="bf_file_del[1]" value="1"> <label for="bf_file_del1"><?php echo $write['qa_source1']; ?> 删除附件</label>
                <?php } ?>
            </td>
        </tr>

        <tr>
            <th scope="row">附件 #2</th>
            <td>
                <input type="file" name="bf_file[2]" title="附件 2 :  大小请勿超过 <?php echo $upload_max_filesize; ?>" class="frm_file frm_input">
                <?php if($w == 'u' && $write['qa_file2']) { ?>
                <input type="checkbox" id="bf_file_del2" name="bf_file_del[2]" value="1"> <label for="bf_file_del2"><?php echo $write['qa_source2']; ?> 删除附件</label>
                <?php } ?>
            </td>
        </tr>

        </tbody>
        </table>
    </div>

    <div class="btn_confirm">
        <input type="submit" value="编辑完成" id="btn_submit" accesskey="s" class="btn_submit">
        <a href="<?php echo $list_href; ?>" class="btn_cancel">目录</a>
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

        <?php if ($is_hp) { ?>
        var hp = f.qa_hp.value.replace(/[0-9\-]/g, "");
        if(hp.length > 0) {
            alert("手机号码只能使用数字及(-)输入");
            return false;
        }
        <?php } ?>

        document.getElementById("btn_submit").disabled = "disabled";

        return true;
    }
    </script>
</section>
<!-- } 编辑主题/修改主题结束 -->