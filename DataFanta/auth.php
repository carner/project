<?php
//允许访问的IP列表
$allow_ip = array('218.5.2.219','220.250.21.82');

if (!in_array($_SERVER['REMOTE_ADDR'],$allow_ip)){
	exit('error ip address');
}

define('MANAGE_CALL',true);
?>