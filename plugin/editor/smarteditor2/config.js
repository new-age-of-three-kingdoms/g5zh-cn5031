(function($){
    $(document).ready(function() {
        $(".smarteditor2").each( function(index){
            var get_id = $(this).attr("id");

            if( !get_id || $(this).prop("nodeName") != 'TEXTAREA' ) return true;

            nhn.husky.EZCreator.createInIFrame({
                oAppRef: oEditors,
                elPlaceHolder: get_id,
                sSkinURI: g5_editor_url+"/SmartEditor2Skin.html",	
                htParams : {
                    bUseToolbar : true,				// 是否使用工具条 (true:使用/ false:不使用)
                    bUseVerticalResizer : true,		// 是否允许调整输入框 (true:使用/ false:不使用)
                    bUseModeChanger : true,			// 模式tabs(Editor | HTML | TEXT) 是否使用 (true:使用/ false:不使用)
                    //aAdditionalFontList : aAdditionalFontSet,		// 附加字体目录
                    fOnBeforeUnload : function(){
                        //alert("完成!");
                    }
                }, //boolean
                fOnAppLoad : function(){
                    //代码范例
                    //oEditors.getById["ir1"].exec("PASTE_HTML", ["完成读取后需要在原文加入的文字内容"]);
                },
                fCreator: "createSEditor2"
            });
        });
    });
})(jQuery);