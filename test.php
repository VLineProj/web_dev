<?php
// $arr=array('userName'=>'f179ff3a942bffdb2d21e47141198134','userPasswd'=>'cdb81d66c64980d4201d2f726c7b9f3f');
// $arr=array('codeType'=>'1','codeContent'=>'fjaidfjoaifjioad');
$arr=array('userName'=>'111','userPasswdHash'=>'222','codeId'=>'0','msgContent'=>'llalalalalatatata','redUserName'=>'111','redMsgId'=>'13');
$json=json_encode($arr);
echo $json;

// var_dump($_GET);
// var_dump($_FILES);
// if ($_FILES["file"]["error"] > 0)
//   {
//   echo "Error: " . $_FILES["file"]["error"] . "<br />";
//   }
// else
//   {
//   echo "Upload: " . $_FILES["file"]["name"] . "<br />";
//   echo "Type: " . $_FILES["file"]["type"] . "<br />";
//   echo "Size: " . ($_FILES["file"]["size"] / 1024) . " Kb<br />";
//   echo "Stored in: " . $_FILES["file"]["tmp_name"];
//   }
