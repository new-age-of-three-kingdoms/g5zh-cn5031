<?php
if (!defined('_GNUBOARD_')) exit; //禁止单独访问此页

/*
    网站设置中为选择使用富文本编辑器时使用的库
    富文本编辑器选项如果不是“未选择”的情况下，请检查plugin/editor的各个编辑器的/editor.lib.php文件
*/

function editor_html($id, $content)
{
    return "<textarea id=\"$id\" name=\"$id\" style=\"width:100%;\" maxlength=\"65536\">$content</textarea>";
}


// 必须使用javascript，以textarea传递参数
function get_editor_js($id)
{
    return "var {$id}_editor = document.getElementById('{$id}');\n";
}


//  检查textarea值是否为空
function chk_editor_js($id)
{
    return "if (!{$id}_editor.value) { alert(\"请编辑内容\"); {$id}_editor.focus(); return false; }\n";
}
?>