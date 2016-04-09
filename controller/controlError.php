<?php
//错误说明 errType:errName:errMark
//登陆时错误
//loginErr:NoUserName:0
//loginErr:NoPassword:1
//loginErr:validErr:2
//loginErr:validTimeOver:3

$controlError=array(

	'0'=>array('loginErr','NoUserName'),
	'1'=>array('loginErr','NoUserPasswd'),
	'2'=>array('loginErr','validErr'),

	'4'=>array('funcErr','noSetFunc'),
	'5'=>array('funcErr','noFunc'),

	'6'=>array('unknownErr','unknown'),

	'7'=>array('validErr','noUserName'),
	'8'=>array('validErr','noUserPasswd'),
	'9'=>array('validErr','validFaild'),

	'10'=>array('regErr','noUserName'),
	'11'=>array('regErr','noUserPass'),
	'12'=>array('regErr','noOldName'),
	'13'=>array('regErr','noOldPassHash'),
	'14'=>array('regErr','registerFailed'),

	'15'=>array('codeErr','paraIllegal'),

	'16'=>array('msgSendErr','paraIlleagle'),
	'17'=>array('getUserMsgsErr','paraIlleagle'),
	'18'=>array('reMsgErr','paraIlleagle'),
	'19'=>array('getCodeMsgErr','paraIlleagle'),

	'20'=>array('changeAvatarErr','paraIlleagle'),
	'21'=>array('changeNickNameErr'.'paraIlleagle'),
	);