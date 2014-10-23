<?php
include_once dirname(__FILE__)."/auth.php";

$ver = $_GET['ver'] ? $_GET['ver'] : 'cn';

//===========开发调试时可注释掉，自行配置$www_path=============
$www_path = "/www/qa.fanta.91dena.com/";
$Lang = $ver;
switch($ver){
	case 'cn':
		$www_path .= 'CN/fanta_cn_sandbox';
		$Lang = "cn";
		break;
	case 'cn2':
		$www_path .= 'CN/fanta_cn2_sandbox';
		$Lang = "cn";
		break;
	case 'cht':
		$www_path = '/www/fanta.91dena.com/fanta_tw_sandbox';
		$ServerUrl = "http://twqa.fanta.91dena.com";
		break;
	case 'kr':
		$www_path .= 'KR/fanta_kr_sandbox';
		break;
	default:
		exit('error parameters ver');
}
$ServerUrl = $ServerUrl ? $ServerUrl : "http://{$Lang}qa.fanta.91dena.com";

if (strpos($ServerUrl,$_SERVER['HTTP_HOST']) === false){
	$addurl = "";
	if ($_GET){
		foreach($_GET as $k => $v){
			$addurl .= $addurl ? "&" : "?";
			$addurl .= "$k=$v";
		}
	}
	$url = $ServerUrl."/DataFanta/GameInfo.php";
	$url .= $addurl;
	Header( "HTTP/1.1 302 Moved Permanently" );
	Header( "Location: $url" );
	exit;
}
//================================================================

function getAttackType($AttackType){
	$str = '';
	switch($AttackType){
		case 100:
		case 101:
		case 102:
		case 103:
		case 104:
		case 105:
		case 106:
			$str = '近距离';
		break;

		case 200:
		case 210:
		case 220:
		case 240:
			$str = '远距离';
		break;

		case 201:
		case 211:
			$str = '3连';
		break;

		case 230:
			$str = '10连';
		break;
		
		case 300:
		case 301:
		case 302:
		case 303:
			$str = '魔法';
		break;
		
		case 400:
			$str = '范围攻击';
		break;
		
		case 500:
			$str = '击中效果';
		break;
	};
	return $str;
}

function getTskillAttack($AtkSea,$AtkGround,$AtkAir,$Rare,$level)
{
	$atk= 0;
	if( $AtkAir < $AtkGround )
	{
		$atk += $AtkGround;
		if( $AtkAir < $AtkSea ){
			$atk += $AtkSea;
		}
		else{
			$atk += $AtkAir;
		}
	}
	else
	{
		$atk += $AtkAir;
		if( $AtkGround < $AtkSea ){
			$atk += $AtkSea;
		}
		else{
			$atk += $AtkGround;
		}
	}
	if($Rare>6)
	{
		$Rare = 6;
	}
	$tSkillAttack = round($atk*(100-pow($Rare,2)*1.5)/10000*$level);
	return $tSkillAttack;
}

function GetUnitAttackPoint($AtkSea,$AtkGround,$AtkAir,$Rare,$level)
{
		$atk = 0;
		if( $AtkSea < $AtkGround )
		{
			$atk += $AtkGround;
			if( $AtkAir < $AtkSea ){
				$atk += $AtkSea;
			}
			else{
				$atk += $AtkAir;
			}
		}
		else
		{
			$atk += $AtkAir;
			if( $AtkGround < $AtkSea ){
				$atk += $AtkSea;
			}
			else{
				$atk += $AtkGround;
			}
		}
		$atk = (int)($atk*(100-pow($Rare,2)*1.5)/10000*$level) + 10000;
		return $atk;
}
function GetEnemyAttackPoint($DefSea,$DefGround,$DefAir,$Rare,$level,$Size,$HP,$MaxHP)
{
	if( $DefAir < $DefGround )
	{
		$atk += $DefGround;
		if( $DefAir < $DefSea ){
			$atk += $DefSea;
		}
		else{
			$atk += $DefAir;
		}
	}
	else
	{
		$atk += $DefAir;
		if( $DefGround < $DefSea ){
			$atk += $DefSea;
		}
		else
		{
			$atk += $DefGround;
		}
	}
	$life = $HP + floor( (($MaxHP - $HP) / 1000.0) * ($level - 1) );
	$atk = (int)(($life*($atk/2))/($Size*1800)*$Rare) + 10000;
	return $atk;
}


$type = $_GET['type'] ? $_GET['type'] : 'unit';

$ver_list = array('cn' => '简中','cn2' => '简中新','kr' => '韩版','cht' => '繁中',/*'jp' => '日版'*/);
$type_list = array('unit' => '星灵','monster' => '魔兽','item' => '物品','gacha' => '扭蛋', 'recycle' => '幸运福袋','Transmigration' => '魔兽转生',);

$unit_db_path_cn = "{$www_path}/data/Unit/UnitData_cn.php";
$monster_db_path_cn = "{$www_path}/data/Unit/Monster_cn.php";

$unit_db_path = "{$www_path}/data/Unit/UnitData_{$Lang}.php";
$monster_db_path = "{$www_path}/data/Unit/Monster_{$Lang}.php";
$item_db_path =  "{$www_path}/data/Item/Item.php";

$recycle_init_path = "{$www_path}/data/Recycle/RecycleList.php";
$recycle_db_path = "{$www_path}/design/RecycleList.php";

$formula_path = "{$www_path}/design/GamingFormula.php";
$GrowParameter_path = "{$www_path}/data/Unit/GrowParameter.php";

require_once($www_path."/config/config.inc");
require_once($www_path."/config/Stage/config.inc");
switch($ver){
	case 'cn':
	case 'cn2':
		$_REQUEST['totalPay'] > 0 && $param['totalPay'] = intval($_REQUEST['totalPay']);
		break;
	case 'kr':
		break;
	case 'cht':
		//unset($gacha_typelist['Limited4']);
		break;
	default:
		exit('error parameters ver');
}

$param = array('ver' => $ver, 'type' => $type);
switch($type){
	case 'unit':
	case 'monster':

		//******缓存UnitData文件*******
		if($type == 'unit'){
			$filemtime = filemtime($unit_db_path);
		}else{
			$filemtime = filemtime($monster_db_path);
		}
		
		$lastmodtime = $_SERVER["HTTP_IF_MODIFIED_SINCE"];
		if($filemtime <= $lastmodtime){
			header('HTTP/1.1 304 Not Modified');
			exit;
		}else{
			header('Last-Modified: '.$filemtime);
		}
		//******缓存UnitData文件*******

		$array_keys['Face'] = '';
		$array_keys['ID'] = 'ID';
		//unset($ver_list['tw']);

		$SkillData = array(1 => '击退', 2 => '减速', 3 => '毒', 4 => '范围攻击', 5 => '攻击力上升（男性）', 6 => '攻击力上升（女性）', 7 => '攻击力上升（精灵）', 8 => '攻击力上升（骑士）', 9 => '攻击力上升（帝国兵）');

		/*
		日版全部技能
		$BattleSkillData = array(0 => '毒',1 => '减速', 2 => '击退', 3 => '攻击力上升', 4 => '铜墙铁壁', 5 => '暴击', 6 => '二连击', 7 => '三连击', 8 => '四连击', 9 => '五连击', 10 => '奋力一击', 11 => '生命流失', 12 => '生命偷取', 13 => '鬼神連撃', 14 => '速度王', 15 => '攻击王', 16 => '防守王', 17 => '治愈王', 18 => '冻结', 19 => '燃烧', 20 => '狂暴');
		*/
		$BattleSkillData = array(
			1 => '击退',
			2 => '减速',
			3 => '毒',
			4 => '范围攻击',
			5 => '攻击力上升',
			101 => '铜墙铁壁',
			102 => '奋力一击',
			103 => '二连击',
			104 => '三连击',
			105 => '四连击',
			106 => '五连击',
			107 => '暴击',
			108 => '生命汲取',
			109 => '灵魂盗取',
			110 => '鬼神连击',
			111 => '速度王',
			112 => '战意鼓舞',
			113 => '战神装甲',
			114 => '圣光祈祷',
			115 => '冻结',
			116 => '炎戒',
			117 => '狂暴',
			118 => '背后突袭',
			121 => '重击',
			123 => '石化',
			124 => '狙击',
			129 => '时空护甲',
		);
		if ($type == 'unit'){
			if ($Lang == 'kr'){
				include_once ($unit_db_path_cn);
				$array_keys['Name_cn'] = '中文名';
				foreach($UnitData as $k => $v){
					$NameData[$k] = $v['Name'];
				}
			}
			$UnitType = array('近距離','遠距離','魔法');
			//$FacePath = "http://fanta.sb.dev.91.com/updateCN/face/u";
			$FacePath = RESOURCE_ANDROID_URL."android/Data/Chara/Face/Unit/u";
			$CardPath = RESOURCE_ANDROID_URL."android/Data/Chara/Card/Large/u";

			//$UnitData 星灵列表数据
			include_once ($unit_db_path);
			$array_keys['Name'] = '名称';
			$array_keys['Rare'] = '稀有度';
			$array_keys['Type'] = '类型';
			$array_keys['Cost'] = '召唤值';
			$array_keys['AtkGround'] = '初始对陆';
			$array_keys['AtkAir'] = '初始对空';
			$array_keys['AtkSea'] = '初始对海';
			$array_keys['_MaxGround'] = "满级对陆";
			$array_keys['_MaxAir'] = "满级对空";
			$array_keys['_MaxSea'] = "满级对海";
			$array_keys['AttackWait'] = '攻击间隔';
			$array_keys['AttainmentTime'] = '弹道';
			$array_keys['Range'] = '攻击范围';
			$array_keys['HP'] = 'HP';
			$array_keys['_MaxHP'] = "_MaxHP";
			$array_keys['TSkillAttack'] = '竞技场伤害';
			$array_keys['RDUAttack'] = '育成伤害';
			$array_keys['Skill'] = '技能';
			$array_keys['TSkill'] = '竞技场技能';
			//$array_keys['AttackType'] = '攻击方式';

			$array_data = $UnitData;
		}else if ($type == 'monster'){
			if ($Lang == 'kr'){
				include_once ($monster_db_path_cn);
				$array_keys['Name_cn'] = '中文名';
				foreach($MonsterData as $k => $v){
					$NameData[$k] = $v['Name'];
				}
			}
			//$FacePath = "http://fanta.sb.dev.91.com/updateCN/face/m";
			$FacePath = RESOURCE_ANDROID_URL."android/Data/Chara/Face/Enemy/m";
			$CardPath = RESOURCE_ANDROID_URL."android/Data/Chara/Enemy/Large/m";

			//$MonsterData 魔兽列表数据
			include_once ($monster_db_path);
			$PropertyArray = array('L' => '陆', 'A' => '空', 'S' => '海');
			$array_keys['Name'] = '名称';
			$array_keys['Rare'] = '稀有度';
			$array_keys['Property'] = '种类';
			$array_keys['Type'] = '类型';
			$array_keys['HP'] = 'HP';
			//$array_keys['Cost'] = '召唤值';
			$array_keys['DefGround'] = '近战防御';
			$array_keys['DefAir'] = '远战防御';
			$array_keys['DefSea'] = '魔法防御';
			$array_keys['AttackSpeed'] = '攻击速度';
			$array_keys['Speed'] = '移动速度';
			$array_keys['RDMAttack'] = '育成伤害';
			$array_keys['Transmigration'] = '是否可转生';

			$array_data = $MonsterData;
		}

		require($formula_path);
		require($GrowParameter_path);

		foreach($array_data as $k => $v){
			$array_data[$k]['ID'] = $k;
			//$array_data[$k]['Face'] = "<a target='_blank' href='".$CardPath.$k."_c.jpg'>"."<img width=30 height=30 datasrc='".$FacePath.$k."_f.png"."' /></a>";
			$array_data[$k]['Face'] = "<a target='_blank' href='./picture.php?cardId={$k}&imgType=card&ver={$ver}&type={$type}'>"."<img width=30 height=30 datasrc='./picture.php?cardId={$k}&imgType=face&ver={$ver}&type={$type}' /></a>";
			$NameData[$k] && $array_data[$k]['Name_cn'] = $NameData[$k];
			$array_data[$k]['AttainmentTime'] = $array_data[$k]['AttackWait'] + $array_data[$k]['AttainmentTime'];
			$array_data[$k]['Property'] = $PropertyArray[$array_data[$k]['Property']];
			//$array_data[$k]['Skill'] = $SkillData[$array_data[$k]['Skill']] ? $SkillData[$array_data[$k]['Skill']] . $array_data[$k]['SkillLv'] : '';
			$skill = strpos($array_data[$k]['Skill'],',') === false ? array($array_data[$k]['Skill']) : explode(',',$array_data[$k]['Skill']);
			$skill_lv = strpos($array_data[$k]['SkillLv'],',') === false ? array($array_data[$k]['SkillLv']) : explode(',',$array_data[$k]['SkillLv']);
			foreach($skill as $key => $val){
				$txt_skill[$key] = $SkillData[$val] . $skill_lv[$key];
			}
			$array_data[$k]['Skill'] = implode('<br>',$txt_skill);
			
			$array_data[$k]['TSkill'] = $BattleSkillData[$array_data[$k]['TSkill']] ? $BattleSkillData[$array_data[$k]['TSkill']] . $array_data[$k]['TSkillLv'] : $array_data[$k]['TSkill'];
			unset($txt_skill);
			//$array_data[$k]['AttackType'] = getAttackType($array_data[$k]['AttackType']);
			
			$GrowList = $GrowParameter[$array_data[$k]["GrowType"]];
			$level = $Fomulation["CardRareInfo"][$array_data[$k]["Rare"]]["CardLevelMax"];
			$GrowParam = $GrowList[$level];
			$GrowParam_break = $GrowList[$level+20];
			
			$air = round($array_data[$k]['AtkAir'] + ($array_data[$k]['MaxAir'] - $array_data[$k]['AtkAir']) * $GrowParam / 2000);
			$maxAir = round($array_data[$k]['AtkAir'] + ($array_data[$k]['MaxAir'] - $array_data[$k]['AtkAir']) * $GrowParam_break / 2000);
			$ground = round($array_data[$k]['AtkGround'] + ($array_data[$k]['MaxGround'] - $array_data[$k]['AtkGround']) * $GrowParam / 2000);
			$maxGround = round($array_data[$k]['AtkGround'] + ($array_data[$k]['MaxGround'] - $array_data[$k]['AtkGround']) * $GrowParam_break / 2000);
			$sea = round($array_data[$k]['AtkSea'] + ($array_data[$k]['MaxSea'] - $array_data[$k]['AtkSea']) * $GrowParam / 2000);
			$maxSea = round($array_data[$k]['AtkSea'] + ($array_data[$k]['MaxSea'] - $array_data[$k]['AtkSea']) * $GrowParam_break / 2000);
			
			$array_data[$k]['_MaxHP'] = round($array_data[$k]['HP'] + ($array_data[$k]['MaxHP'] - $array_data[$k]['HP']) * $GrowParam / 2000);
			$array_data[$k]['_MaxHP'] .= "<br>(".round($array_data[$k]['HP'] + ($array_data[$k]['MaxHP'] - $array_data[$k]['HP']) * $GrowParam_break / 2000).")";
			$array_data[$k]['_MaxAir'] = $air."<br>(".$maxAir.")";
			$array_data[$k]['_MaxGround'] = $ground."<br>(".$maxGround.")";
			$array_data[$k]['_MaxSea'] = $sea."<br>(".$maxSea.")";
			$array_data[$k]['TSkillAttack'] = getTskillAttack($sea,$ground,$air,$array_data[$k]['Rare'],$level);
			$array_data[$k]['TSkillAttack'] .= "<br />(".getTskillAttack($sea,$ground,$air,$array_data[$k]['Rare'],$level+20).")";
			if($k < 20000)
			{
				$array_data[$k]['RDUAttack'] = GetUnitAttackPoint($sea,$ground,$air,$array_data[$k]['Rare'],$level);
				$array_data[$k]['RDUAttack'] .= "<br />(".GetUnitAttackPoint($sea,$ground,$air,$array_data[$k]['Rare'],$level+20).")";
			}
			else
			{
				$array_data[$k]['RDMAttack'] = GetEnemyAttackPoint($array_data[$k]['DefSea'],$array_data[$k]['DefGround'],$array_data[$k]['DefAir'],$array_data[$k]['Rare'],$level,$array_data[$k]['Size'],$array_data[$k]['HP'],$array_data[$k]['MaxHP']);
				$array_data[$k]['RDMAttack'] .= "<br />(".GetEnemyAttackPoint($array_data[$k]['DefSea'],$array_data[$k]['DefGround'],$array_data[$k]['DefAir'],$array_data[$k]['Rare'],$level+20,$array_data[$k]['Size'],$array_data[$k]['HP'],$array_data[$k]['MaxHP']).")";
			}
			$array_data[$k]['Rare'] = $array_data[$k]['Rare'] . '★';
			
		}
		break;
	case 'item':
		//$ItemData 物品列表数据
		include_once ($item_db_path);
		$array_keys['ID'] = 'ID';
		if ($Lang == 'kr'){
			$array_keys['Name_cn'] = '中文名';
		}
		$array_keys['Name_'.$Lang] = '名称';
		$array_keys['RMPrice'] = 'M币';
		$array_keys['Explation'] = '说明';
		
		if ($_POST['id']){
			foreach($ItemData as $k => $v){
				$k == $_POST['id'] && $array_data[$k] = $v;
			}
		}else if($_POST['name']){
			foreach($ItemData as $k => $v){
				strpos($v['Name_'.$Lang],$_POST['name']) !== false && $array_data[$k] = $v;
			}
		}else{
			$array_data = $ItemData;
		}
		foreach($array_data as $k => $v){
			$array_data[$k]['ID'] = $k;
			$NameData[$k] && $array_data[$k]['Name_cn'] = $NameData[$k];
		}
		break;
	case 'gacha':
		//GoldTicket 1张   LimitRare4 8张   Limited1 10张7次 系列扭蛋 Limited4 4星保底
		//unset($ver_list['jp']);

		//$gacha_typelist = array('GoldTicket' => '扭蛋礼券','Limited1' => '系列扭蛋（10张7次）','Limited4' => '日式4星','LimitRare4' => '神扭');
		$gacha_typelist = array('GoldTicket' => '扭蛋礼券','Limited1' => '系列扭蛋（10张7次）','Limited4' => '保底4星');
		$gacha_type = $_GET['gacha_type'] ? $_GET['gacha_type'] : 'GoldTicket';
		$param['gacha_type'] = $gacha_type;

		require_once(PROJECT_PATH."/framework/Common.php");
		require_once(PROJECT_PATH."/framework/Utility.php");
		require_once(PROJECT_PATH."/contents/Define.php");
		require(DESIGN_PATH."/GamingFormula.php");
		require_once(PROJECT_PATH.'/contents/function/Gacha.php');

		include_once ($unit_db_path);
		include_once ($monster_db_path);
		$unit_data = $UnitData + $MonsterData;
		
		$table = new \func\GachaTable($gacha_type);
		$amount = 100000;
		for($i = 0; $i < $amount; $i++){
			$unit_id = $table->Draw();
			if (!$unit_data[$unit_id]){
				continue;
			}
			$gacha_data[] = $unit_data[$unit_id];
			$RareNumArray[$unit_data[$unit_id]['Rare']]++;
			$gacha_list[$unit_data[$unit_id]['Rare']][$unit_id]++;
		}
		ksort($RareNumArray);
		$array_keys['Rare'] = '星级';
		$array_keys['Num'] = '数量';
		$array_keys['Rate'] = '概率';
		$array_keys['content'] = '';
		foreach($RareNumArray as $k => $v){
			$list_html = '<table cellpadding="0" cellspacing="0" class="tbl" style="font-size:12px">';
			foreach($gacha_list[$k] as $key => $val){
				$list_html .= "<tr><td>{$key}</td><td>{$unit_data[$key]['Name']}</td><td>{$val}次</td><td>".(round($val/$amount,4)*100)."%</td></tr>";
			}
			$list_html .= '</table>';
			$array_data[] = array('Rare' => $k,'Num' => $v,'Rate' => round($v/$amount,4) * 100 . '%','content' => '<a href="javascript:;" onclick="this.style.display = \'none\';document.getElementById(\'Tab_'.$k.'\').style.display = \'block\'">详细</a><div id="Tab_'.$k.'" style="display:none">'.$list_html.'</div>');
		}
		break;
	case 'recycle':
		$array_keys['ID'] = 'ID';
		if ($Lang == 'kr'){
			include_once ($unit_db_path_cn);
			include_once ($monster_db_path_cn);
			$array_keys['Name_cn'] = '中文名';
			$UnitList_cn = $UnitData + $MonsterData;
		}

		//$UnitData 星灵列表数据
		include_once ($unit_db_path);
		//$MonsterData 魔兽列表数据
		include_once ($monster_db_path);
		//$ItemData 物品列表数据
		include_once ($item_db_path);
		$array_keys['Name'] = '名称';
		$array_keys['Rate'] = '概率';

		//$RecycleList 神秘商店兑换物品数据
		include_once($recycle_db_path);
		//$GachaTable 神秘商店兑换概率数据
		include_once($recycle_init_path);
		foreach($GachaTable as $k => $v){
			$SumRate += $v;
		}
		$UnitList = $UnitData + $MonsterData;
		//$RecycleType = array('Card' => '卡牌');
		foreach($RecycleList as $k => $v){
			//$array_data[$k]['Type'] = $v['Type'];
			$array_data[$k]['ID'] = $v['Type'] == 'Card' ? $v['UnitID'] : $v['ItemId'];
			$array_data[$k]['Name'] = $v['Type'] == 'Card' ? $UnitList[$v['UnitID']]['Name'] : $ItemData[$v['ItemId']]['Name_'.$Lang];
			$array_keys['Name_cn'] && ($array_data[$k]['Name_cn'] = $v['Type'] == 'Card' ? $UnitList_cn[$v['UnitID']]['Name'] : $ItemData[$v['ItemId']]['Name_cn']);
			$array_data[$k]['Rate'] = round($GachaTable[$k] / $SumRate,4) * 100 . '%';
		}
		break;
	case 'Transmigration':
		//$Fomulation
		include_once($formula_path);
		$Transmigration = $Fomulation['Transmigration'];
		$low_amount = intval($_REQUEST['low_amount']);
		$high_amount = intval($_REQUEST['high_amount']);
		!$low_amount && !$high_amount && $low_amount = 1;
		$level = max(intval($_REQUEST['level']),1);
		$rare = max(intval($_REQUEST['rare']),1);
		$rate = round(($Transmigration['LowCoefficient'] * $low_amount + $Transmigration['HighCoefficient'] * $high_amount) * (($level / 3.0 + 100.0) / 100.0) / $Transmigration['RareCoefficient'][$rare] * $Transmigration['EventCoefficient'],2) . '%';
		break;
	default:
		exit('error parameters type');
}

if (is_array($array_data)){
	foreach($array_keys as $key => $val){
		if ($_REQUEST['Search_'.$key]){
			$Search_key[$key] = $_REQUEST['Search_'.$key];
			$param['Search_'.$key] = urlencode($_REQUEST['Search_'.$key]);
		}
	}
	if (is_array($Search_key)){
		foreach($array_data as $k => $v){
			foreach($Search_key as $key => $val){
				if (strpos($v[$key],$val) === false){
					unset($array_data[$k]);
				}
			}
		}
	}

	if ($array_keys[$_REQUEST['sort_key']]){
		foreach($array_data as $k => $v){
			$sort_arr[$k] = $v[$_REQUEST['sort_key']];
		}
		$sort_type = $_REQUEST['sort_type'] == 'asc' ? 'asc' : 'desc';
		//$param['sort_type'] = $sort_type == 'asc' ? 'desc' : 'asc';
		$sort_type == 'asc' ? asort($sort_arr) : arsort($sort_arr);
		foreach($sort_arr as $k => $v){
			$sort_array_data[$k] = $array_data[$k];
		}
		$array_data = $sort_array_data;
	}
}

function format_param($param = array(),$istop = false)
{
	$top_param = array('type','ver','gacha_type');
	foreach($param as $k => $v)
	{
		if ($istop === false || ($istop && in_array($k,$top_param)))
		{
			$arr[] = "$k=$v";
		}
	}
	return implode('&',$arr);
}
?>
<!doctype html>
<html>
<head>
<meta charset="utf-8"/>
<style>
ul,li { list-style:none;padding:0px;margin:0px; }
.tbl th a{ color:#666; font-weight:normal; text-align:left; font-size:13px;}
.tbl td{ font-size:13px;}
.tbl th,.tbl td { border:1px solid #ccc; padding:5px 10px; }
img{ border:none; }
.in_list,.in_list li { display:inline; }
.in_list a { color:#666; }
.in_list a.current { font-weight:bold; }
</style>
</head>
<body>
<div>
  类型：
  <ul class="in_list">
    <?php foreach($type_list as $k => $v){ ?>
    <li><a <?php if ($k == $type){ ?>class="current"<?php } ?> href="?<?php echo format_param(array('type' => $k) + $param,true); ?>"><?php echo $v; ?></a></li>
    <?php } ?>
  </ul>
</div>
<div>
  版本：
  <ul class="in_list">
    <?php foreach($ver_list as $k => $v){ ?>
    <li><a <?php if ($k == $ver){ ?>class="current"<?php } ?> href="?<?php echo format_param(array('ver' => $k) + $param,true); ?>"><?php echo $v; ?></a></li>
    <?php } ?>
  </ul>
</div>
<?php if ($type == 'unit' || $type == "monster"){ ?>
<form method="POST">
  <table style="margin-bottom:10px;">
    <tr>
      <td>ID：<input type="text" name="Search_ID" value="<?php echo $_REQUEST['Search_ID']; ?>" /></td>
      <td>名称：<input type="text" name="Search_Name" value="<?php echo $_REQUEST['Search_Name']; ?>" /></td>
      <td>类型：<select name="Search_Type"><option value="">不限</option><?php foreach($UnitType as $v){ ?><option <?php if ($v == $_REQUEST['Search_Type']){ ?>selected="selected"<?php }?>><?php echo $v; ?></option><?php } ?></td>
      <td>稀有度：<input type="text" name="Search_Rare" value="<?php echo $_REQUEST['Search_Rare']; ?>" /></td>
      <td><input type="hidden" name="Search" value="1" /><input type="submit" value="查询" /></td>
    </tr>
  </table>
</form>
<?php }else if ($type == 'gacha'){ ?>
<form method="GET">
  <?php foreach($param as $k => $v){ ?>
  <input type="hidden" name="<?php echo $k; ?>" value="<?php echo $v; ?>" />
  <?php } ?>
  <?php if ($gacha_typelist){ ?>
  <div>
    抽取方式：
    <ul class="in_list">
      <?php foreach($gacha_typelist as $k => $v){ ?>
      <li><a <?php if ($k == $gacha_type){ ?>class="current"<?php } ?> href="?<?php echo format_param(array('gacha_type' => $k) + $param,true); ?>"><?php echo $v; ?></a></li>
      <?php } ?>
      <li style="margin-left:10px;">其他：<input name="gacha_type" type="text" /> （如：Limited1、StepUpGacha1等）<input type="submit" value="查询" /></li>
    </ul>
  </div>
  <?php } ?> 
  <?php if ($Lang == 'cn'){ ?>
  <table style="margin-bottom:10px;">
    <tr>
	  <td>消费金额：<input type="text" id="totalPay" name="totalPay" value="<?php echo $_REQUEST['totalPay']; ?>" /></td>
	  <td><input type="submit" value="查询" /></td>
	  <td>分为0-300，300-1000，1000-6000，6000以上几个档次（300以下只能抽3星和4星）</td>
    </tr>
  </table>
  <?php } ?>
</form>
<?php }else if ($type == 'Transmigration'){ ?>
<form method="POST">
  <table style="margin-bottom:10px;">
    <tr>
      <td>下级转生石：<input type="text" name="low_amount" value="<?php echo $low_amount; ?>" /></td>
      <td>上级转生石：<input type="text" name="high_amount" value="<?php echo $high_amount; ?>" /></td>
      <td>魔兽等级：<input type="text" name="level" value="<?php echo $level; ?>" /></td>
      <td>魔兽稀有度：<input type="text" name="rare" value="<?php echo $rare; ?>" /></td>
      <td><input type="submit" value="查询" /></td>
    </tr>
  </table>
</form>
转生概率为<?php echo $rate; ?>
<?php } ?>
<form method="POST">
  <table cellpadding="0" cellspacing="0" class="tbl">
    <tr id="tbl_header">
	  <?php foreach($array_keys as $k => $v){ ?>
      <th><a href="?<?php echo format_param(array('sort_key' => $k) + ($k == $_REQUEST['sort_key'] ? $param + array('sort_type' => $sort_type == 'asc' ? 'desc' : 'asc') : $param)); ?>"><?php echo $v; ?></a></td>
	  <?php } ?>
    </tr>
	<?php foreach($array_data as $key => $val){ ?>
    <tr onmouseover="this.style.background='#eee'" onmouseout="this.style.background=''">
	  <?php foreach($array_keys as $k => $v){ ?>
      <td><?php echo $val[$k]; ?></td>
	  <?php } ?>
    </tr>
	<?php } ?>
  </table>
</form>

<script>
window.onscroll = function(){
	var pos = document.getElementById("pos");
	if (document.body.scrollTop > 200)
	{
		if (pos == null)
		{
			create_pos();
		}else if(!window.addEventListener){
			pos.style.top = document.body.scrollTop;
		}
	}else if (pos){
		pos.parentNode.removeChild(document.getElementById('pos'));
	}
}

function create_pos(){
	var tbl_header = document.getElementById("tbl_header");
	var init_w = [];
	var tbl_w = tbl_header.clientWidth;
	for (var i in tbl_header.childNodes)
	{
		if (tbl_header.childNodes[i].nodeType == 1)
		{
			init_w[i] = tbl_header.childNodes[i].clientWidth;
		}
	}

	var pos = document.createElement("div");
	var innerHTML = '<table cellpadding="0" cellspacing="0" class="tbl"><tbody><tr id="pos_header">';
	innerHTML += tbl_header.innerHTML;
	innerHTML += '</tr></tbody></table>';
	pos.id="pos";
	pos.innerHTML = innerHTML;
	pos.style.width = tbl_w;

	if(window.addEventListener){
		pos.style.position = 'fixed';
    }else if(window.attachEvent){
		pos.style.position = 'absolute';
    }
	pos.style.paddingTop = '5px';
	pos.style.paddingBottom = '5px';
	pos.style.top = '0px';
	pos.style.height = '30px';
	pos.style.background = '#fff';
	document.body.insertBefore(pos,document.body.firstChild);

	var pos_header = document.getElementById("pos_header");
	for (var i in pos_header.childNodes)
	{
		if (pos_header.childNodes[i].nodeType == 1)
		{
			pos_header.childNodes[i].style.paddingLeft = 0;
			pos_header.childNodes[i].style.paddingRight = 0;
			pos_header.childNodes[i].width = init_w[i];
		}
	}
}

window.onload = function(){
	var img = document.getElementsByTagName('img');
	for (i in img)
	{
		if (img[i].nodeType == 1)
		{
			var imgsrc = img[i].getAttribute('datasrc');
			if (imgsrc)
			{
				img[i].src = imgsrc;
			}
		}
	}
}
</script>
</body>
</html>