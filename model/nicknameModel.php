<?php
require_once("../config.php");
require_once("../lib/database.class.php");
$nicknameFile=fopen("../static/nickname/nickname.txt","r");
$nickNames=fread($nicknameFile,filesize("../static/nickname/nickname.txt"));
$nickNameExplode=explode("\n",$nickNames);
$dataObj=new Mysql();
$dataObj->delete('nick','1=1');
$i=0;
foreach ($nickNameExplode as $nick) {
	$insertArray=array('nick_id'=>$i,'nick_name'=>$nick);
	$dataObj->insert('nick',$insertArray);
	$i++;
}
echo "昵称更新成功！";