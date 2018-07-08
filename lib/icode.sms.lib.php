<?php
if (!defined('_GNUBOARD_')) exit;
// icode服务使用的函数及参数

///////////////////////////////////////////////////////////////////////////////////////////
// 此代码无需改动

function spacing($text,$size) {
	for ($i=0; $i<$size; $i++) $text.=" ";
	$text = substr($text,0,$size);
	return $text;
}

function cut_char($word, $cut) {
//	$word=trim(stripslashes($word));
	$word=substr($word,0,$cut);						// 获取所需长度
	for ($k=$cut-1; $k>1; $k--) {
		if (ord(substr($word,$k,1))<128) break;		// 中文文字160
	}
	$word=substr($word,0,$cut-($cut-$k+1)%2);
	return $word;
}

function CheckCommonType($dest, $rsvTime) {
	//$dest=eregi_replace("[^0-9]","",$dest);
	$dest=preg_replace("/[^0-9]/i","",$dest);
	if (strlen($dest)<10 || strlen($dest)>11) return "手机号码错误";
	$CID=substr($dest,0,3);
	//if ( eregi("[^0-9]",$CID) || ($CID!='010' && $CID!='011' && $CID!='016' && $CID!='017' && $CID!='018' && $CID!='019') ) return "手机号码前三位错误";
	if ( preg_match("/[^0-9]/i",$CID) || ($CID!='010' && $CID!='011' && $CID!='016' && $CID!='017' && $CID!='018' && $CID!='019') ) return "手机号码前三位错误";
	//$rsvTime=eregi_replace("[^0-9]","",$rsvTime);
	$rsvTime=preg_replace("/[^0-9]/i","",$rsvTime);
	if ($rsvTime) {
		if (!checkdate(substr($rsvTime,4,2),substr($rsvTime,6,2),substr($rsvTime,0,4))) return "预约日期错误";
		if (substr($rsvTime,8,2)>23 || substr($rsvTime,10,2)>59) return "预约时间错误";
	}
}

class SMS {
	var $ID;
	var $PWD;
	var $SMS_Server;
	var $port;
	var $SMS_Port;
	var $Data = array();
	var $Result = array();

	function SMS_con($sms_server,$sms_id,$sms_pw,$port) {
		$this->ID=$sms_id;		// 签订协议后指定
		$this->PWD=$sms_pw;		// 签订协议后指定
		$this->SMS_Server=$sms_server;
		$this->SMS_Port=$port;
		$this->ID = spacing($this->ID,10);
		$this->PWD = spacing($this->PWD,10);
	}

	function Init() {
		$this->Data = "";
		$this->Result = "";
	}

	function Add($dest, $callBack, $Caller, $msg, $rsvTime="") {
        global $g5;

		// 内容检查 1
		$Error = CheckCommonType($dest, $rsvTime);
		if ($Error) return $Error;
		// 内容检查 2
		//if ( eregi("[^0-9]",$callBack) ) return "回信号码设置错误";
		if ( preg_match("/[^0-9]/i",$callBack) ) return "回信号码设置错误";

        $msg=cut_char($msg,80); // 80字限制
		// 添加到发送内容队列
		$dest = spacing($dest,11);
		$callBack = spacing($callBack,11);
		$Caller = spacing($Caller,10);
		$rsvTime = spacing($rsvTime,12);
		$msg = spacing($msg,80);

		$this->Data[] = '01144 '.$this->ID.$this->PWD.$dest.$callBack.$Caller.$rsvTime.$msg;
		return "";
	}

	function AddURL($dest, $callBack, $URL, $msg, $rsvTime="") {
		// 内容检查 1
		$Error = CheckCommonType($dest, $rsvTime);
		if ($Error) return $Error;
		// 内容检查 2
		//$URL=str_replace("http://","",$URL);
		if (strlen($URL)>50) return "URL超过50字";
		switch (substr($dest,0,3)) {
			case '010': //20字节
                $msg=cut_char($msg,20);
				break;
			case '011': //80字节
                $msg=cut_char($msg,80);
				break;
			case '016': // 80字节
				$msg=cut_char($msg,80);
				break;
			case '017': // URL包括80字节
				$msg=cut_char($msg,80-strlen($URL));
				break;
			case '018': // 20字节
				$msg=cut_char($msg,20);
				break;
			case '019': // 20字节
				$msg=cut_char($msg,20);
				break;
			default:
				return "不支持URL CallBack";
				break;
		}
		// 添加到发送内容队列
		$dest = spacing($dest,11);
		$URL = spacing($URL,50);
		$callBack = spacing($callBack,11);
		$rsvTime = spacing($rsvTime,12);
		$msg = spacing($msg,80);
		$this->Data[] = '05173 '.$this->ID.$this->PWD.$dest.$callBack.$URL.$rsvTime.$msg;
		return "";
	}

	function Send () {
		$fp=@fsockopen(trim($this->SMS_Server),trim($this->SMS_Port));
		if (!$fp) return false;
		set_time_limit(300);

		## php4.3.10版本时
        ## 请跟新最新版本zend
        ## 或则更改122行this->Data as $tmp => $puts

		foreach($this->Data as $puts) {
			$dest = substr($puts,26,11);
			fputs($fp,$puts);
			while(!$gets) { $gets=fgets($fp,30); }
			if (substr($gets,0,19)=="0223  00".$dest) $this->Result[]=$dest.":".substr($gets,19,10);
			else $this->Result[$dest]=$dest.":Error";
			$gets="";
		}
		fclose($fp);
		$this->Data="";
		return true;
	}
}
?>