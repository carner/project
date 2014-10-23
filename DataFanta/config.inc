<?php
/**
 * 2012-5-16 韩丞
 *  修改 MBGAAPPID/GADGET_ROOT/SERVER_ROOT 配置 
 *  参照 https://developer.denachina.com/d/view/document/documentSandBoxHostName.htm
 * 2012-5-22/23 林志苏
 *  服务器时区 jp -> cn
 * 2012-5-28 吴文长
 *  LANGUAGE 修改为 cn
**/

//Engine Name: Polyhymnia
//Constraint Version 1.3.0.0 for NGSDK

require_once("opensocial.inc");				// 定数定義を読み込み
require_once("define.inc");					// 定数定義を読み込み
require_once("uri.inc");					// Uri定義を読み込み
require_once("version.inc");				// バージョン定義を読み込み
require_once("usersetting.inc");			// ユーザー定義を読み込み
require_once("maintenance.inc");			// メンテナンス定義を読み込み
require_once("cachestore.inc");				// キャッシュ定義を読み込み
date_default_timezone_set("Asia/Shanghai");	//5.3からはタイムゾーンの設定をしないとWarningになる。

//5.3からはタイムゾーンの設定をしないとWarningになる。
// パス
define("PROJECT_PATH", dirname(__FILE__)."/../../");
define("DATA_ROOT", dirname(__FILE__)."/../../data/");
define("DESIGN_PATH", dirname(__FILE__)."/../../design");
define("DATABASE_PATH", dirname(__FILE__)."/../../databases/");

// ゲーム依存定数なのでframeworkを
// 別プロジェクトで使用する場合は以下3行を消してください
require_once(PROJECT_PATH."/contents/function/Const.php");
require_once(PROJECT_PATH."/contents/commander/Const.php");
require_once(PROJECT_PATH."/contents/Define.php");

// メンテナンスモードのタイマー設定を有効にするか
define("TIMER_UPDATE_MAINTENANCE", false);		// false: 従来の設定方法を踏襲 ／ true: タイマー設定に切り替える
if( TIMER_UPDATE_MAINTENANCE == false ) {
	// メンテナンスキー
	define("IS_MAINTENANCE_IPHONE", false);
	define("IS_MAINTENANCE_ANDROID", false);
}
//動作環境(fanta2/test)
define("WORKING_STYLE", FRAMEWORK_DEVELOP);
define("APIMODE", APIMODE_SANDBOX);
//動作環境(release)
//define("WORKING_STYLE", FRAMEWORK_RELEASE);
//define("APIMODE", APIMODE_RELEASE);

define("TIMER_UPDATE_RESOURCE_VERSION", false);		// false: 従来の設定方法を踏襲 ／ true: タイマー更新に切り替える
if( TIMER_UPDATE_RESOURCE_VERSION == false ) {
	// リソースURL(Contentsの場合はDeveloper'sサーバー、またはローカルフォルダを見ます)
	/*
	define("RESOURCE_IPHONE_URL", "http://upfanta1.dev.91.com/3405/");
	define("RESOURCE_IPHONE_LOW_URL", "http://upfanta1.dev.91.com/3405/");
	define("RESOURCE_IPHONE_JUDGE_URL", "Contents");
	define("RESOURCE_IPHONE_JUDGE_LOW_URL", "Contents");
	define("RESOURCE_ANDROID_URL", "http://upfanta1.dev.91.com/3405/");
	*/
	define("RESOURCE_IPHONE_URL", "Contents");
	define("RESOURCE_IPHONE_LOW_URL", "Contents");
	define("RESOURCE_IPHONE_JUDGE_URL", "Contents");
	define("RESOURCE_IPHONE_JUDGE_LOW_URL", "Contents");
	define("RESOURCE_ANDROID_URL", "http://fanta.sb.91dena.com/updateCN/4400/");
}

// 通信の暗号化
define("URL_ENCRYPTED", false);

// マニフェスト設定
define("TITLE_MANIFEST_MAX", 5);	// マニフェスト数
define("TITLE_MANIFEST_NUMBER", 0);	// ダウンロードマニフェスト番号
define("SPLIT_LOAD_IPHONE", false);	// リソース分割(iOS)
define("SPLIT_LOAD_ANDROID", false);	// リソース分割(Android)
define("TITLE_SPLIT_MANIFEST_MAX", 4);	// 分割マニフェスト数
define("TITLE_SPLIT_MANIFEST_NUMBER", 300);	// 分割ダウンロードマニフェスト番号
define("INCREMENTAL_LOAD", false);	// incremental load
define("INCREMENTAL_LOAD_MANIFEST_NAME", "webgame.incremental.ngmanifest");	// マニフェスト名

// ログ
define("LOG_LEVEL", (LEVEL_DEBUG | LEVEL_INFO | LEVEL_PAYMENT | LEVEL_WARNING | LEVEL_ERROR | LEVEL_TRACE));
define("LOG_OUTPUT_ACCESS_DATA_ENABLE", false);

// ログデータベース出力
define("LOG_OUTPUT_DB_ENABLE", true);
define("LOG_OUTPUT_DB_ACCESS_DATA_ENABLE", true);
define("LOG_OUTPUT_DB_ACCESS_DATA_REQUEST_ONLY", true);

// 認証環境
define("AUTHORIZE_INTERFACE", AUTH_USING_DATABASE);
define("TOKENCHECK_INTERFACE", TOKEN_USING_DATABASE);
define("AUTH_EXPIRE_TIME", 1800);

// 言語設定
define("LANGUAGE", "cn");

// 言語毎のユニット、データ
define("UNITDATA", DATA_ROOT."./Unit/UnitData.php");
define("MONSTERDATA", DATA_ROOT."./Unit/Monster.php");

// Memcache
define("ENABLE_MEMCACHE", false);
define("MEMCACHE_HOST", "localhost");
define("DEFAULT_EXPIRE", 1800);			// MAINにのせるEXPIREのデフォルト時間

// プラットフォーム設定
define("PLARFORM", PLATFORM_MBGA);
define("USEAPI", (MBGA_MOBILE_API | MBGA_SMARTPHONE_API));
define("MBGAAPPID", 13000161);														// モバゲーアプリID***************************************
//define("GADGET_ROOT", 'http://???//');								// ガジェットルート
//define("SERVER_ROOT", "http://???/");			// サーバールート

//文字コード設定
define("SYSTEM_CHARSET", "UTF-8");
define("SYSTEM_REQUEST_CHARSET", "UTF-8");
define("FAKE_PLATFORM_API", false);	// プラットフォームAPIをFAKEする

//DB
define("USING_DB_DRIVER", DBTYPE_MYSQLi_INNODB);	// DBタイプ
define("USING_DB_TRUNSUCTION", true);				// DBトランザクション

//fantaサーバ用実装
define("PHP_ERROR_EXEC", "SAFE");
define("FLASH_EMULATION", TRUE);
define("OUTPUTFORMAT", FORMAT_JSON);
define("RELEASE_DATE", strtotime("2012/02/25 12:00:00"));

// 各種有効設定
define("TRADE_ENABLE", true);			// トレード
define("LOGIN_BONUS_ENABLE", true);	// ログインボーナス
//服务器标示
define("SERVER_ID", 1);
/*
////
//エラーレベルを設定
error_reporting(E_ALL|E_STRICT);

//PEARで発生するエラーを無視するクラス
require_once("ErrorHandler.php");
$error_handle = ErrorHandler::singleton();

//MobileAgentのStrictエラーを無視する
$error_handle->addIgnoreError(array('errno' => E_STRICT, 'errstr' => '=Non-static method Net_UserAgent_Mobile::singleton() should not be called statically' ) );
$error_handle->addIgnoreError(array('errfile' => '#/PEAR/#i'));
$error_handle->addIgnoreError(array('errstr' => '#/PEAR/#i'));
////
*/
?>
