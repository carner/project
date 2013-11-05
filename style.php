<html>
<head>
	<style>
	/*代码框*/
		.dp-highlighter
		{
		  font-family: Monaco, DejaVu Sans Mono, Bitstream Vera Sans Mono, Consolas, Courier New, monospace;
		  font-size: 12px;
		  background-color: transparent;
		  width: 97%;
		  overflow: auto;
		  margin-left: 9px;
		  padding: 1px; /* adds a little border on top when controls are hidden */
		  word-break: break-all;
		  word-wrap: break-word;
		}
	</style>
</head>
<body>
	<!电影演员表效果!>
	<div style="position:absolute;width:240px;height:200px;visibility:show; z-index:10;left:310px;top:30px;background-color:lightyellow; filter:alpha(opacity=85);"> 
		<table border=1 background="bg_100.jpg"> 
			<tr> 
				<td width=240> 
					<marquee style="height:200px;text-align:center;left:0px;top:1px;color:red; margin-left:1" scrollamount=1 behavior=loop direction=up> 
						网站建设单位：国瑞公司<br><br> 
						网页制作：张 庆<br><br> 
						时 间:二○○○年五月二十二日 
					</marquee> 
				</td> 
			</tr> 
		</table> 
	</div>
</body>
</html>

//长网址缩短
<?php
	$ch=curl_init();
	curl_setopt($ch,CURLOPT_URL,"http://dwz.cn/create.php");
	curl_setopt($ch,CURLOPT_POST,true);
	curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);
	$data=array('url'=>'http://www.baidu.com/');
	curl_setopt($ch,CURLOPT_POSTFIELDS,$data);
	$strRes=curl_exec($ch);
	curl_close($ch);
	$arrResponse=json_decode($strRes,true);
	if($arrResponse['status']==0)
	{
	/**错误处理*/
	echo iconv('UTF-8','GBK',$arrResponse['err_msg'])."\n";
	}
	/** tinyurl */
	echo$arrResponse['tinyurl']."\n";
?>