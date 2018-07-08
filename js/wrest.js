var wrestMsg = "";
var wrestFld = null;
var wrestFldDefaultColor = "";
//var wrestFldBackColor = "#ff3061";

// 获取subject 属性后 return, 如无讲tag name进行传递
function wrestItemname(fld)
{
    //return fld.getAttribute("title") ? fld.getAttribute("title") : ( fld.getAttribute("alt") ? fld.getAttribute("alt") : fld.name );
    var id = fld.getAttribute("id");
    var labels = document.getElementsByTagName("label");
    var el = null;

    for(i=0; i<labels.length; i++) {
        if(id == labels[i].htmlFor) {
            el = labels[i];
            break;
        }
    }

    if(el != null) {
        var text =  el.innerHTML.replace(/[<].*[>].*[<]\/+.*[>]/gi, "");

        if(text == '') {
            return fld.getAttribute("title") ? fld.getAttribute("title") : ( fld.getAttribute("placeholder") ? fld.getAttribute("placeholder") : fld.name );
        } else {
            return text;
        }
    } else {
        return fld.getAttribute("title") ? fld.getAttribute("title") : ( fld.getAttribute("placeholder") ? fld.getAttribute("placeholder") : fld.name );
    }
}

// 除去两端空白
function wrestTrim(fld)
{
    var pattern = /(^\s+)|(\s+$)/g; // \s 空白文字
    return fld.value.replace(pattern, "");
}

// 必须输入项目检查
function wrestRequired(fld)
{
    if (wrestTrim(fld) == "") {
        if (wrestFld == null) {
            // 如果是选框设定为强制选择时也进行检查
            wrestMsg = wrestItemname(fld) + " : 必须"+(fld.type=="select-one"?"选择":"输入")+"项目\n";
            wrestFld = fld;
        }
    }
}

// sir 金善勇 2006.3 - 电话号码(手机) 格式检查 : 123-123(4)-5678
function wrestTelNum(fld)
{
    if (!wrestTrim(fld)) return;

    var pattern = /^1[1-9][0-9]{3}[0-9]{4}[0-9]{4}$/; //中国大陆手机号码正则判断
    if(!pattern.test(fld.value)){
        if(wrestFld == null){
            wrestMsg = wrestItemname(fld)+" : 电话号码格式错误\n\n 请输入正确的中国大陆地区手机号码\n";
            wrestFld = fld;
            fld.select();
        }
    }
}

// 邮件地址格式检查
function wrestEmail(fld)
{
    if (!wrestTrim(fld)) return;

    //var pattern = /(\S+)@(\S+)\.(\S+)/; 如果邮件地址中包含双字节字母
    var pattern = /([0-9a-zA-Z_-]+)@([0-9a-zA-Z_-]+)\.([0-9a-zA-Z_-]+)/;
    if (!pattern.test(fld.value)) {
        if (wrestFld == null) {
            wrestMsg = wrestItemname(fld) + " : 邮件地址格式错误\n";
            wrestFld = fld;
        }
    }
}

// 中文判断
function wrestHangul(fld)
{
    if (!wrestTrim(fld)) return;

    //var pattern = /([^一-龥豈-鶴\x20])/i; 韩文[^一-龥豈-鶴\x20]或[ᄀ-ᇹㄱ-ㆎ一-龥豈-鶴]日文[ぁ-ヾ]
    var pattern = /([^一-龥豈-鶴\x20])/;

    if (pattern.test(fld.value)) {
        if (wrestFld == null) {
            wrestMsg = wrestItemname(fld) + ' : 请输入中文\n';
            wrestFld = fld;
        }
    }
}

// 双字节文字检查
function wrestHangul2(fld)
{
    if (!wrestTrim(fld)) return;

    var pattern = /([^x00-xff\x20])/i;
    //var pattern = /([^x00-xff\x20])/;

    if (pattern.test(fld.value)) {
        if (wrestFld == null) {
            wrestMsg = wrestItemname(fld) + ' : 请使用数字\n';
            wrestFld = fld;
        }
    }
}

// 检测中文、英文、数字
function wrestHangulAlNum(fld)
{
    if (!wrestTrim(fld)) return;

    var pattern = /([^一-龥豈-鶴\x20^a-z^A-Z^0-9])/i;

    if (pattern.test(fld.value)) {
        if (wrestFld == null) {
            wrestMsg = wrestItemname(fld) + ' : 请使用中、英及数字\n';
            wrestFld = fld;
        }
    }
}

// 中英文检测
function wrestHangulAlpha(fld)
{
    if (!wrestTrim(fld)) return;

    var pattern = /([^一-龥豈-鶴\x20^a-z^A-Z])/i;

    if (pattern.test(fld.value)) {
        if (wrestFld == null) {
            wrestMsg = wrestItemname(fld) + ' : 请使用中文、英文\n';
            wrestFld = fld;
        }
    }
}

// 数字检查
// sir会员 吃饱了的猪 添加 (http://dasir.com) 2003-06-24
function wrestNumeric(fld)
{
    if (fld.value.length > 0) {
        for (i = 0; i < fld.value.length; i++) {
            if (fld.value.charAt(i) < '0' || fld.value.charAt(i) > '9') {
                wrestMsg = wrestItemname(fld) + " : 请使用数字\n";
                wrestFld = fld;
            }
        }
    }
}

// 英文字母检查
// sir会员 吃饱了的猪 添加 (http://dasir.com) 2003-06-24
function wrestAlpha(fld)
{
    if (!wrestTrim(fld)) return;

    var pattern = /(^[a-zA-Z]+$)/;

    if (!pattern.test(fld.value)) {
        if (wrestFld == null) {
            wrestMsg = wrestItemname(fld) + " : 请使用英文字母\n";
            wrestFld = fld;
        }
    }
}

// 英文及数字检测
// sir会员 吃饱了的猪 添加 (http://dasir.com) 2003-07-07
function wrestAlNum(fld)
{
   if (!wrestTrim(fld)) return;

   var pattern = /(^[a-zA-Z0-9]+$)/;

   if (!pattern.test(fld.value)) {
       if (wrestFld == null) {
           wrestMsg = wrestItemname(fld) + " : 请使用英文、数字\n";
           wrestFld = fld;
       }
   }
}

// 英文、数字及下划线
function wrestAlNum_(fld)
{
   if (!wrestTrim(fld)) return;

   var pattern = /(^[a-zA-Z0-9\_]+$)/;

   if (!pattern.test(fld.value)) {
       if (wrestFld == null) {
           wrestMsg = wrestItemname(fld) + " : 请使用英文、数字、下划线\n";
           wrestFld = fld;
       }
   }
}

// 最少长度检查
function wrestMinLength(fld)
{
    if (!wrestTrim(fld)) return;

    var minlength = fld.getAttribute("minlength");

    if (wrestFld == null) {
        if (fld.value.length < parseInt(minlength)) {
            wrestMsg = wrestItemname(fld) + " : 请至少输入"+minlength+"字以上\n";
            wrestFld = fld;
        }
    }
}

// 图片格式扩展名
function wrestImgExt(fld)
{
    if (!wrestTrim(fld)) return;

    var pattern = /\.(gif|jpg|png)$/i; // 排除jpeg
    if(!pattern.test(fld.value)){
        if(wrestFld == null){
            wrestMsg = wrestItemname(fld)+" : 不是支持的图片格式\n请使用gif/jpg/png格式图片\n";
            wrestFld = fld;
            fld.select();
        }
    }
}

// 扩展名
function wrestExtension(fld, css)
{
    if (!wrestTrim(fld)) return;

    var str = css.split("="); // ext=?? <-- str[1]
    var src = fld.value.split(".");
    var ext = src[src.length - 1];

    if (wrestFld == null) {
        if (ext.toLowerCase() < str[1].toLowerCase()) {
            wrestMsg = wrestItemname(fld) + " : 仅允许使用."+str[1]+"格式文件\n";
            wrestFld = fld;
        }
    }
}

// 检测空白后讲空白转换成“”
function wrestNospace(fld)
{
    var pattern = /(\s)/g; // \s 空白文字

    if (pattern.test(fld.value)) {
        if (wrestFld == null) {
            wrestMsg = wrestItemname(fld) + " : 请勿输入空格\n";
            wrestFld = fld;
        }
    }
}

// 提交（submit）检测属性
function wrestSubmit()
{
    wrestMsg = "";
    wrestFld = null;

    var attr = null;

    // 仅运行所需
    for (var i=0; i<this.elements.length; i++) {
        var el = this.elements[i];

        // 只有在input tag type是text，file，password时
        // 如果是选框也进行检查 select-one
        if (el.type=="text" || el.type=="hidden" || el.type=="file" || el.type=="password" || el.type=="select-one" || el.type=="textarea") {
            if (el.getAttribute("required") != null) {
                wrestRequired(el);
            }

            if (el.getAttribute("minlength") != null) {
                wrestMinLength(el);
            }

            var array_css = el.className.split(" "); // 使用空格分隔class

            el.style.backgroundColor = wrestFldDefaultColor;

            // 仅运行排列值
            for (var k=0; k<array_css.length; k++) {
                var css = array_css[k];
                switch (css) {
                    case "required"     : wrestRequired(el); break;
                    case "trim"         : wrestTrim(el); break;
                    case "email"        : wrestEmail(el); break;
                    case "hangul"       : wrestHangul(el); break;
                    case "hangul2"      : wrestHangul2(el); break;
                    case "hangulalpha"  : wrestHangulAlpha(el); break;
                    case "hangulalnum"  : wrestHangulAlNum(el); break;
                    case "nospace"      : wrestNospace(el); break;
                    case "numeric"      : wrestNumeric(el); break;
                    case "alpha"        : wrestAlpha(el); break;
                    case "alnum"        : wrestAlNum(el); break;
                    case "alnum_"       : wrestAlNum_(el); break;
                    case "telnum"       : wrestTelNum(el); break; // sir 金善勇 2006.3 - 电话号码格式检查
                    case "imgext"       : wrestImgExt(el); break;
                    default :
                        if (/^extension\=/.test(css)) {
                            wrestExtension(el, css); break;
                        }
                } // switch (css)
            } // for (k)
        } // if (el)
    } // for (i)

    // 如果字段不是null，显示错误信息后移动到错误字段
    // 错误字段更换背景颜色
    if (wrestFld != null) {
        // 警告信息显示
        alert(wrestMsg);

        if (wrestFld.style.display != "none") {
            var id = wrestFld.getAttribute("id");

            // 为了错误信息添加element
            var msg_el = document.createElement("strong");
            msg_el.id = "msg_"+id;
            msg_el.className = "msg_sound_only";
            msg_el.innerHTML = wrestMsg;
            wrestFld.parentNode.insertBefore(msg_el, wrestFld);

            var new_href = document.location.href.replace(/#msg.+$/, "")+"#msg_"+id;

            document.location.href = new_href;

            //wrestFld.style.backgroundColor = wrestFldBackColor;
            if (typeof(wrestFld.select) != "undefined")
                wrestFld.select();
            wrestFld.focus();
        }
        return false;
    }

    if (this.oldsubmit && this.oldsubmit() == false)
        return false;

    return true;
}


// 初始化时填充onsubmit
function wrestInitialized()
{
    for (var i = 0; i < document.forms.length; i++) {
        // 如有onsubmit event则储存
        if (document.forms[i].onsubmit) {
            document.forms[i].oldsubmit = document.forms[i].onsubmit;
        }
        document.forms[i].onsubmit = wrestSubmit;
    }
}

// 数据表检测
$(document).ready(function(){
    // onload
    wrestInitialized();
});