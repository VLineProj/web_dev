<?php
require_once('config.php');
require_once('lib/database.class.php');
require_once('model/databaseModel.php');
require_once('model/fileModel.php');
require_once('model/codeModel.php');

$avatarObj=new AvatarClass();
$databaseObj=new databaseModelClass();
$codeObj=new CodeClass();


//数据字段：func--方法名  para--方法参数的json

//方法的说明 func:para:return
//userLogin:userName/userPasswd:userAvatar/userNickName/userRedNum
//userValid:userName/userPasswdHash:None
//userFalseReg:None:userName/userPasswdHash
//!userTrueReg:userName/userPass/oldUserName/oldUserPasswdHash:true
//getAllBuildInAvatars:None:allAvatars
//!scanCode:codeType/codeContent:CodeId/QueryType
//!nameCode:codeId/codeName:true
//!msgSend:codeId/userName/msgContent/msgCorrdinate/msgLocation:true
//!getUserMsgs:userName:userMsgs
//!getCodeMsgs:codeId:codeMsgs
//!reMsg:reUser/redUser/msgId:true
//!changeUserAvatar:userName/avatarId:avatarUrl
//!changeUserNickName:userName/nickName:true

@$func=$_REQUEST['func'];
@$para=json_decode($_REQUEST['para'],true);
if(!isset($func)){
	halt(4);
}

switch($func){
	case 'userLogin':
		userLogin($databaseObj,$avatarObj,$para);
		break;
	case 'userValid';
		userValid($databaseObj,$para);
	case 'userFalseReg':
		userFalseReg($databaseObj);
	 	break; 
	case 'userTrueReg':
		userValid($databaseObj,$para);
		userTrueReg($databaseObj,$para);
		break;
	case 'getAllBuildInAvatars':
		getAllBuildInAvatars($avatarObj);
		break;
	case 'getBuildInAvatar':
		userValid($databaseObj,$para);
		getBuildInAvatar($avatarObj,$para);
		break;
	case 'scanCode':
		userValid($databaseObj,$para);
		scanCode($codeObj,$para);
		break;
	case 'nameCode':
		userValid($databaseObj,$para);
		nameCode($codeObj,$para);
		break;
	case 'msgSend':
		userValid($databaseObj,$para);
		msgSend($databaseObj,$para);
		break;
	case 'getUserMsgs':
		userValid($databaseObj,$para);
		getUserMsgs($databaseObj,$para);
		break;
	case 'getCodeMsgs':
		userValid($databaseObj,$para);
		getCodeMsgs($databaseObj,$para);
		break;
	case 'reMsg':
		userValid($databaseObj,$para);
		reMsg($databaseObj,$para);
		break;
	case 'changeUserAvatar':
		changeUserAvatar($databaseObj,$para);
		break;
	case 'changeUserNickName':
		changeUserNickName($databaseObj,$para);
		break;
	default:
		halt(5);
		break;
}

function userLogin($databaseObj,$avatarObj,$para){
	@$userName=$para['userName'];
	@$userPasswd=$para['userPasswd'];
	if(!isset($userName)){
		halt(0);
	}
	if(!isset($userPasswd)){
		halt(1);
	}
	$userInfo=$databaseObj->userLoginOp($userName,$userPasswd);
	if($userInfo){
		$avatar=split('_',$userInfo['user_avatar']);
		$avatar=$avatar[1];
		$avatarUrl=$avatarObj->getBuildInAvatar($avatar);
		$reply=array('userAvatar'=>$avatarUrl,'userNickName'=>$userInfo['user_nickname'],'userRedNum'=>$userInfo['user_rednum']);
	}else{
		halt(2);
	}
	reply($reply);
}

function userValid($databaseObj,$para){
	@$userName=$para['userName'];
	@$userPasswdHash=$para['userPasswdHash'];
	if(!isset($userName)){
		halt(7);
	}
	if(!isset($userPasswdHash)){
		halt(8);
	}
	$valid=$databaseObj->loginValid($userName,$userPasswdHash);
	if(!$valid)
	{
		halt(9);
	}

}

function userFalseReg($databaseObj){
	$userInfo=$databaseObj->falseReg();
	reply($userInfo);	
}
function userTrueReg($databaseObj,$para){
	@$userName=$para['userName'];
	@$userPasswd=$para['userPasswd'];
	@$oldUserName=$para['oleUserName'];
	@$oldUserPasswdHash=$para['oldUserPasswdHash'];
	if(!isset($userName)){
		halt(10);
	}
	if(!isset($userPasswd)){
		halt(11);
	}
	if(!isset($oldUserName)){
		halt(12);
	}
	if(!isset($oldUserPasswdHash)){
		halt(13);
	}
	$userInfo=$databaseObj->trueReg($userName,$userPasswd,$oldUserName,$oldUserPasswdHash);
	if($userInfo){
		reply($userInfo);
	}else{
		halt(14);
	}
}

function getAllBuildInAvatars($avatarObj){
	$avatars=$avatarObj->getAllBuildInAvatars();
	reply($avatars);
}
function getBuildInAvatar($avatarObj,$para){
	$avatar=$avatarObj->getBuildInAvatar(1);	
	reply($avatar);
}

function scanCode($databaseObj,$para){
	@$codeType=$para['codeType'];
	$codeType=0;
	@$codeContent=$para['codeContent'];
	if(!isset($codeType)||!isset($codeContent)){
		halt(15);
	}
	$codeMsg=$databaseObj->codeScan($codeType,$codeContent);
	reply($codeMsg);
}
function nameCode($databaseObj,$para){
	@$codeId=$para['codeId'];
	@$codeName=$para['codeName'];
	if(!isset($codeId)||!isset($codeName)){
		halt(15);
	}
	$codeMsg=$databaseObj->nameCode($codeId,$codeName);
	reply($codeMsg);
}

function msgSend($databaseObj,$para){
	@$codeId=$para['codeId'];
	@$userName=$para['userName'];
	@$msgContent=$para['msgContent'];
	@$msgCorrdinate=$para['msgCorrdinate'];
	$msgCorrdinate='test';
	@$msgLocation=$para['msgLocation'];
	$msgLocation='test';
	@$redMsgId=$para['redMsgId'];
	if(!isset($codeId)||!isset($userName)||
		!isset($msgContent)||!isset($msgCorrdinate)||!isset($msgLocation)){
		halt(16);
	}
	$databaseObj->msgSend($codeId,$userName,$msgContent,$msgCorrdinate,$msgLocation,$redMsgId);
	reply(true);
}
function getUserMsgs($databaseObj,$para){
	@$userName=$para['userName'];
	if(!isset($userName)){
		halt(17);
	}
	$userMsgs=$databaseObj->getUserMsgs($userName);
	reply($userMsgs);
}
function getCodeMsgs($databaseObj,$para){
	@$codeId=$para['codeId'];
	if(!isset($codeId)){
		halt(19);
	}
	$codeMsgs=$databaseObj->getCodeMsgs($codeId);
	reply($codeMsgs);
}
function reMsg($databaseObj,$para){
	@$reUser=$para['reUser'];
	@$redUser=$para['redUser'];
	@$msgId=$para['msgId'];
	if(!isset($reUser)||!isset($redUser)||!isset($msgId)){
		halt(18);
	}
	$databaseObj->msgRes($reUser,$redUser,$msgId);
	reply(true);
}

function changeUserAvatar($databaseObj,$para){
	@$userName=$para['userName'];
	@$avatarId=$para['avatarId'];
	if(!isset($userName)||!isset($avatarId)){
		halt(20);
	}
	$avatarUrl=$databaseObj->changeAvatar($userName,$avatarId);
	reply($avatarUrl);
}
function changeUserNickName($databaseObj,$para){
	@$userName=$para['userName'];
	@$nickName=$para['nickName'];
	if(!isset($userName)||!isset($nickName)){
		halt(21);
	}
	$databaseObj->changeNickName($userName,$nickName);
	reply(true);
}


function reply($reply){
	if(isset($reply)){
		echo $replyJson=json_encode($reply,JSON_UNESCAPED_SLASHES);
	}else{
		halt(6);
	}
	exit();
}

function halt($errMark){
	require_once('controller/controlError.php');
	$errType=$controlError[$errMark][0];
	$errName=$controlError[$errMark][1];
	$reply=array('errType'=>$errType,'errMsg'=>$errName,'errMark'=>$errMark);
	reply($reply);
}
