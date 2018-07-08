<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="zh-cn" xml:lang="zh-cn">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Smart Editor&#8482; WYSIWYG Mode</title>
<link href="../../css/compiled/open/ko_KR/smart_editor2_in.css" rel="stylesheet" type="text/css">
</head>
<body class="smartOutput se2_inputarea">
	<p>
		<b><u>编辑器内容:</u></b>
	</p>

	<div style="width:736px;">
	<?php
		$postMessage = $_POST["ir1"]; 
		echo $postMessage;
	?>
	</div>
	
	<hr>
	<p>
		<b><span style="color:#FF0000">注意: </span>sample.php是演示文档，并非包含所有功能及最终效果</b>
	</p>
	
	<?php echo(htmlspecialchars_decode('&lt;img id="test" width="0" height="0"&gt;'))?>
	
<script>
	if(!document.getElementById("test")) {
		alert("php文件未能执行，请确认是否向服务器正确发送数据");
	}
</script>
</body>
</html>