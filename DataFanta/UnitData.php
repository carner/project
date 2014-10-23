<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<?php

include_once dirname(__FILE__)."/auth.php";

header ("content-type: text/html; charset: utf-8");
//ini_set('default_charset','utf-8')

$ver = $_GET['ver'] ? $_GET['ver'] : 'cn';
$www_path = "/www/qa.fanta.91dena.com/";
$ServerUrl = "http://{$ver}qa.fanta.91dena.com";
switch($ver){
	case 'cn':
		$www_path .= 'CN/fanta_cn_sandbox';
		break;
	case 'cn2':
		$www_path .= 'CN/fanta_cn2_sandbox';
		$ServerUrl = "http://cnqa.fanta.91dena.com";
		break;
	case 'cht':
	case 'tw':
		$www_path = '/www/fanta.91dena.com/fanta_tw_sandbox';
		$ServerUrl = "http://twqa.fanta.91dena.com";
		break;
	case 'kr':
		$www_path .= 'KR/fanta_kr_sandbox';
		break;
	default:
		exit('error parameters ver');
}
if (strpos($ServerUrl,$_SERVER['HTTP_HOST']) === false){
	$addurl = "";
	if ($_GET){
		foreach($_GET as $k => $v){
			$addurl .= $addurl ? "&" : "?";
			$addurl .= "$k=$v";
		}
	}
	$url = $ServerUrl."/DataFanta/UnitData.php";
	$url .= $addurl;
	Header( "HTTP/1.1 302 Moved Permanently" );
	Header( "Location: $url" );
	exit;
}
require_once($www_path."/config/config.inc");

require $www_path.'/data/Unit/UnitData_cn.php';
$UnitDatacn = $UnitData;

require $www_path.'/data/Unit/UnitData_cn.php';
$UnitDatajp = $UnitData;

require $www_path.'/data/Unit/Monster_cn.php';
$MonsterDatacn = $MonsterData;
require $www_path.'/data/Unit/Monster_jp.php';
$MonsterDatajp = $MonsterData;

switch($ver){
	case 'cn':
	case 'cn2':
		require $www_path.'/data/Unit/UnitData_cn.php';
		require $www_path.'/data/Unit/Monster_cn.php';
		break;
	case 'cht':
		require $www_path.'/data/Unit/UnitData_cht.php';
		require $www_path.'/data/Unit/Monster_cht.php';
		break;
	case 'kr':
		require $www_path.'/data/Unit/UnitData_kr.php';
		require $www_path.'/data/Unit/Monster_kr.php';
		break;
	default:
		exit('error parameters ver');
}

$ver = $_REQUEST['ver'];
switch ($ver)
{
	case 'cn':
	case 'cn2':
		echo "insert into fanta_UnitData(`UnitId`,`Name_cn`,`Name`,`Rare`,`Type`,`Cost`,`AtkGround`,`AtkAir`,`AtkSea`,`Range`,`AttackWait`,`Skill`,`SkillLv`) values";
		break;
	case 'kr':
		echo "insert into fanta_UnitData(`UnitId`,`Name_kr`,`Name`,`Rare`,`Type`,`Cost`,`AtkGround`,`AtkAir`,`AtkSea`,`Range`,`AttackWait`,`Skill`,`SkillLv`) values";
		break;
	case 'cht':
		echo "insert into fanta_UnitData(`UnitId`,`Name_cht`,`Name`,`Rare`,`Type`,`Cost`,`AtkGround`,`AtkAir`,`AtkSea`,`Range`,`AttackWait`,`Skill`,`SkillLv`) values";
		break;
	default:
		echo "ver is not exact";
		break;
}

//echo "$UnitData";
//echo "insert into fanta_UnitData(`UnitId`,`Name_cn`,`Name`,`Rare`,`Type`,`Cost`,`AtkGround`,`AtkAir`,`AtkSea`,`Range`,`AttackWait`,`Skill`,`SkillLv`) values";
echo "<br />";

foreach($UnitData as $k => $v){
	//
	$x++;
	$namecn = array_key_exists($k,$UnitDatacn)?$UnitDatacn[$k]['Name']:$UnitDatajp[$k]['Name'];
	if ($v['Skill'] == '' || $v['Skill'] == null){
		$v['Skill'] = 0;
		$v['SkillLv'] = 0;
	}
	echo "($k,'".$v['Name']."','$namecn',".$v['Rare'].",'".$v['Type']."',".$v['Cost'].",".$v['AtkGround'].",".$v['AtkAir'].",".$v['AtkSea'].",".$v['Range'].",".$v['AttackWait'].",".$v['Skill'].",".$v['SkillLv'].")";
	if($x < count($UnitData)){
		echo ",<br />";
	}else{
		echo ";<br />";
	}
}




echo "<br />";
echo "<br />";


//echo "insert into fanta_MonsterData(`UnitId`,`Name_cn`,`Name`,`Rare`,`Type`,`HP`,`DefGround`,`DefAir`,`DefSea`,`Speed`)values";
echo "<br />";

//$x=0;
$x=0;
$y =null;
//$z = null;
switch ($ver)
{
	case 'cn':
		echo "insert into fanta_MonsterData(`UnitId`,`Name_cn`,`Name`,`Rare`,`Type`,`HP`,`DefGround`,`DefAir`,`DefSea`,`Speed`)values";
		break;
	case 'kr':
		echo "insert into fanta_MonsterData(`UnitId`,`Name_kr`,`Name`,`Rare`,`Type`,`HP`,`DefGround`,`DefAir`,`DefSea`,`Speed`)values";
		break;
	case 'cht':
		echo "insert into fanta_MonsterData(`UnitId`,`Name_cht`,`Name`,`Rare`,`Type`,`HP`,`DefGround`,`DefAir`,`DefSea`,`Speed`)values";
		break;
	default:
		echo "ver is not exact";
		break;
}


foreach($MonsterData as $k => $v){
	$x++;
	$namecn = array_key_exists($k,$MonsterDatacn)?$MonsterDatacn[$k]['Name']:$MonsterDatajp[$k]['Name'];
	echo "($k,'".$v['Name']."','$namecn',".$v['Rare'].",'".$v['Type']."',".$v['HP'].",".$v['DefGround'].",".$v['DefAir'].",".$v['DefSea'].",".$v['Speed'].")";
	if($x < count($MonsterData)){
		echo ",<br />";
	}else{
		echo ";<br />";
	}
}
/*
for($i=1;$i<300;$i++){
	require("./data/Mission_Wave/Stage$i.php");
	$arr = 0;
	$time = "";
	foreach($WaveData as $value){
		if($value["Event"] == "Enemy"){
			$arr += 10; 
			$time = $value["TimeLine"];
		}
	}
	echo "stage$i === $arr === $time <br />" ;
}
*/



?>
