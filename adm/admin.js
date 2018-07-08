function check_all(f)
{
    var chk = document.getElementsByName("chk[]");

    for (i=0; i<chk.length; i++)
        chk[i].checked = f.chkall.checked;
}

function btn_check(f, act)
{
    if (act == "update") // 编辑所选
    {
        f.action = list_update_php;
        str = "修改";
    }
    else if (act == "delete") // 删除所选
    {
        f.action = list_delete_php;
        str = "删除";
    }
    else
        return;

    var chk = document.getElementsByName("chk[]");
    var bchk = false;

    for (i=0; i<chk.length; i++)
    {
        if (chk[i].checked)
            bchk = true;
    }

    if (!bchk)
    {
        alert(请选择需要+str+"内容");
        return;
    }

    if (act == "delete")
    {
        if (!confirm("点击确定删除所选内容"))
            return;
    }

    f.submit();
}

function is_checked(elements_name)
{
    var checked = false;
    var chk = document.getElementsByName(elements_name);
    for (var i=0; i<chk.length; i++) {
        if (chk[i].checked) {
            checked = true;
        }
    }
    return checked;
}

function delete_confirm()
{
    if(confirm("删除操作执行后将不能进行恢复\n\n点击确定执行操作"))
        return true;
    else
        return false;
}

function delete_confirm2(msg)
{
    if(confirm(msg))
        return true;
    else
        return false;
}