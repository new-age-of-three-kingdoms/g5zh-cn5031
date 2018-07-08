// 临时储存时间以秒为时间单位
var AUTOSAVE_INTERVAL = 60; // 秒

// 用于检测标题或内容是否有变动的函数参数
var save_wr_subject = null;
var save_wr_content = null;

function autosave() {
    $("form#fwrite").each(function() {
        if (g5_editor.indexOf("ckeditor4") != -1 && typeof(CKEDITOR.instances.wr_content)!="undefined") {
            this.wr_content.value = CKEDITOR.instances.wr_content.getData();
        } else if (g5_editor.indexOf("cheditor5") != -1 && typeof(ed_wr_content)!="undefined") {
            this.wr_content.value = ed_wr_content.outputBodyHTML();
        } else if (g5_editor.indexOf("smarteditor2") != -1 && typeof(oEditors.getById['wr_content'])!="undefined" ) {
            this.wr_content.value = oEditors.getById['wr_content'].getIR();
        }
        // 只有与储存的参数不一致时临时记录
        if (save_wr_subject != this.wr_subject.value || save_wr_content != this.wr_content.value) {
            $.ajax({
                url: g5_bbs_url+"/ajax.autosave.php",
                data: {
                    "uid" : this.uid.value,
                    "subject": this.wr_subject.value,
                    "content": this.wr_content.value
                },
                type: "POST",
                success: function(data){
                    if (data) {
                        $("#autosave_count").html(data);
                    }
                }
            });
            save_wr_subject = this.wr_subject.value;
            save_wr_content = this.wr_content.value;
        }
    });
}

$(function(){

    if (g5_is_member) {
        setInterval(autosave, AUTOSAVE_INTERVAL * 1000);
    }

    // 获取临时标题
    $("#btn_autosave").click(function(){
        if ($("#autosave_pop").is(":hidden")) {
            $.get(g5_bbs_url+"/ajax.autosavelist.php", function(data){
                //alert(data);
                //console.log( "Data: " + data);
                $("#autosave_pop ul").empty();
                if ($(data).find("list").find("item").length > 0) {
                    $(data).find("list").find("item").each(function(i) {
                        var id = $(this).find("id").text();
                        var uid = $(this).find("uid").text();
                        var subject = $(this).find("subject").text();
                        var datetime = $(this).find("datetime").text();
                        $("#autosave_pop ul")
                            .append('<li><a href="#none" class="autosave_load">'+subject+'</a><span>'+datetime+' <button type="button" class="autosave_del">删除</button></span></li>')
                            .find("li:eq("+i+")")
                            .data({ as_id: id, uid: uid });
                    });
                }
            }, "xml");
            $("#autosave_pop").show();
        } else {
            $("#autosave_pop").hide();
        }
    });

    // 获取临时标题与内容添加到文本输入框
    $(".autosave_load").live("click", function(){
        var $li = $(this).parents("li");
        var as_id = $li.data("as_id");
        var as_uid = $li.data("uid");
        $("#fwrite input[name='uid']").val(as_uid);
        $.get(g5_bbs_url+"/ajax.autosaveload.php", {"as_id":as_id}, function(data){
            var subject = $(data).find("item").find("subject").text();
            var content = $(data).find("item").find("content").text();
            $("#wr_subject").val(subject);
            if (g5_editor.indexOf("ckeditor4") != -1 && typeof(CKEDITOR.instances.wr_content)!="undefined") {
                CKEDITOR.instances.wr_content.setData(content);
            } else if (g5_editor.indexOf("cheditor5") != -1 && typeof(ed_wr_content)!="undefined") {
                ed_wr_content.putContents(content);
            } else if (g5_editor.indexOf("smarteditor2") != -1 && typeof(oEditors.getById['wr_content'])!="undefined" ) {
                oEditors.getById["wr_content"].exec("SET_CONTENTS", [""]);
                //oEditors.getById["wr_content"].exec("SET_IR", [""]);
                oEditors.getById["wr_content"].exec("PASTE_HTML", [content]);
            } else {
                $("#fwrite #wr_content").val(content);
            }
        }, "xml");
        $("#autosave_pop").hide();
    });

    $(".autosave_del").live("click", function(){
        var $li = $(this).parents("li");
        var as_id = $li.data("as_id");
        $.get(g5_bbs_url+"/ajax.autosavedel.php", {"as_id":as_id}, function(data){
            if (data == -1) {
                alert("删除草稿箱时发生了错误！");
            } else {
                $("#autosave_count").html(data);
                $li.remove();
            }
        });
    });

    $(".autosave_close").click(function(){ $("#autosave_pop").hide(); });
});
