<?php
	set_time_limit(0);

	//获取源目录
	//$Path = $_GET['Path'];
	//获取卡牌ID
	$cardId = intval($_GET['cardId']);
	if($cardId < 10000)
	{
		exit("error cardId!");
	}
	//获取类型
	$imgType = $_GET['imgType'];
	if($imgType != 'card' && $imgType != 'face')
	{
		exit("require error！");
	}
	//获取版本ver
	$ver = $_GET['ver'];
	if(!$ver)
	{
		exit("ver null！");
	}
	//获取版本ver
	$Type = $_GET['type'];
	if($Type != 'unit' && $Type != 'monster')
	{
		exit("error type！");
	}
	//获取相对目录
	//switch($imgType){
	//	case 'face':$newPath = "Face/Unit";break;
	//	case 'card':$newPath = "Card/Large";break;
	//}

	getImage($cardId,$imgType,$ver,$Type);

	function getImage($cardId,$imgType,$ver,$Type,$type = false){
		switch($ver){
			case 'cn':
				$www_path .= '/www/qa.fanta.91dena.com/CN/fanta_cn_sandbox';
				$ServerUrl = "http://cnqa.fanta.91dena.com/CN/android/Data/Chara/";//QA服务器资源链接
				$NewCardPath = "/www/qa.fanta.91dena.com/CN/android/Data/Chara/";//QA服务器资源路径
				break;
			case 'cn2':
				$www_path .= '/www/qa.fanta.91dena.com/CN/fanta_cn2_sandbox';
				$ServerUrl = "http://cnqa.fanta.91dena.com/CN/android/Data/Chara/";//QA服务器资源链接
				$NewCardPath = "/www/qa.fanta.91dena.com/CN/android/Data/Chara/";//QA服务器资源路径
				break;
			/*case 'cht':
				$www_path = '/www/fanta.91dena.com/fanta_tw_sandbox';
				$ServerUrl = "http://twqa.fanta.91dena.com";
				break;
			case 'kr':
				$www_path .= 'KR/fanta_kr_sandbox';
				break;*/
			default:
				exit('error parameters ver');
		}

		require_once($www_path."/config/config.inc");
		require_once($www_path."/config/Stage/config.inc");

		$Path = RESOURCE_ANDROID_URL."android/Data/Chara/";//资源更新服务器路径
		
		//检查cardId是否存在
		require_once($www_path.'/data/Unit/UnitData_cn.php');
		require_once($www_path.'/data/Unit/Monster_cn.php');
		if($cardId >= 10000 && $cardId < 20000){
			if(!array_key_exists($cardId, $UnitData)){
				exit("该卡牌不存在！");
			}
		}elseif($cardId > 20000) {
			if(!array_key_exists($cardId, $MonsterData)){
				exit("该卡牌不存在！");
			}
		}

		//图片后缀格式
		switch($Type){
			case 'unit':
				switch($imgType){
					case 'face':$center = "Face/Unit/";$Format = "u".$cardId."_f.png";break;
					case 'card':$center = "Card/Large/";$Format = "u".$cardId."_c.jpg";break;
				}
				break;
			case 'monster':
				switch($imgType){
					case 'face':$center = "Face/Enemy/";$Format = "m".$cardId."_f.png";break;
					case 'card':$center = "Enemy/Large/";$Format = "m".$cardId."_c.jpg";break;
				}
				break;
		}

		//创建目录并修改权限
		readFileFromDir($NewCardPath.$center);

		$flg = $NewCardPath.$center.$Format;
		$filemtime = filemtime($flg);
		//header('Last-Modified: '.$filemtime);

		if(!file_exists($flg) || $imgType != 'face')
		{
			//**********文件比较是否更新**************
			$url = $Path.$center.$Format;
			$url2 = $NewCardPath.$center.$Format;
			//$start_time = time();
			$headInf = get_headers($url,1);
			//$end_time = time();
			//echo "CARD:{$cardname}   ".($end_time - $start_time)."s<BR>";
			$file = strtotime($headInf['Last-Modified']);
			$file2 = filectime($url2);
			
			if ($file > $file2)
			{
				//下载图片
				if($type){
					$ch = curl_init();
					$timeout = 5;
					curl_setopt($ch, CURLOPT_URL, $url);
					curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
					curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
					$file = curl_exec($ch);
					curl_close($ch);
				}else{
					//ob_start();
					//readfile($url);
					//$file = ob_get_contents();
					//ob_end_clean();
					$opts = array(
						'http'=>array(
							'method'=>"GET",
							'timeout'=>6,
						)
					);
				
					$context = stream_context_create($opts);
					$file = file_get_contents($url,false,$context);
					if(!$file){
						return ;
					}
				}
				//保存文件
				$res = fopen($NewCardPath.$center.$Format,'w');
				if(!chmod($NewCardPath.$center.$Format,0777)){
					exit('change fail');
				}
				if (!fwrite($res,$file)){
					exit('write error');
				}
				fclose($res);
				
				//设置缓存文件时间
				$flg = $NewCardPath.$center.$Format;
				$filemtime = filemtime($flg);
				header('Last-Modified: '.$filemtime);
				//exit($flg);
				header('Content-Type: image/jpeg');
				echo file_get_contents($ServerUrl.$center.$Format);
			}else{
				$lastmodtime = $_SERVER["HTTP_IF_MODIFIED_SINCE"];
				if($filemtime <= $lastmodtime){
					header('HTTP/1.1 304 Not Modified');
					exit;
				}else{
					header('Last-Modified: '.$filemtime);
					//exit($ServerUrl.$center.$Format);
					header('Content-Type: image/jpeg');
					echo file_get_contents($ServerUrl.$center.$Format);
				}
			}
			//**********文件比较是否更新**************
		}else{
			$lastmodtime = $_SERVER["HTTP_IF_MODIFIED_SINCE"];
			if($filemtime <= $lastmodtime){
				header('HTTP/1.1 304 Not Modified');
				exit;
			}else{
				header('Last-Modified: '.$filemtime);
				//exit($ServerUrl.$center.$Format);
				header('Content-Type: image/jpeg');
				echo file_get_contents($ServerUrl.$center.$Format);
			}
		}
	}

	function readFileFromDir($dir)
	{
		if(!is_dir($dir))
		{
			if(!readFileFromDir(dirname($dir)))
			{
				return false;
			}
			if(!mkdir($dir))
			{
				return false;
			}
			if(!chmod($dir,0777))
			{
				return false;
			}
		}
		return true;
	}
?>