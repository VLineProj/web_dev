<?php
// require_once '../lib/database.class.php';
require_once 'model/fileModel.php';
// require_once '../config.php';

class databaseModelClass extends Mysql{
	private $avatar;

	public function __construct(){
		parent::__construct();
		$this->avatar = new AvatarClass();
	}

	public function userLoginOp($username="", $passwdHash="") {
		if($username==""||$passwdHash==""){
			return NULL;
		}
		
		$result = $this->get_one("*", "user", "user_name='$username' AND user_passwdhash='$passwdHash'");
		if ($result) {
			// do some update
			$temptime = time();
			$sql = "UPDATE user set user_loginnum=user_loginnum+1 WHERE 1=1 AND user_name='$username' AND user_passwdhash='$passwdHash'";
			$this->query($sql);
			$sql = "UPDATE user set user_lastlogintime='$temptime' WHERE 1=1 AND user_name='$username' AND user_passwdhash='$passwdHash'";
			$this->query($sql);
			//login success!
			//return $result;
			return $result;
		}
		return NULL;
	}

	// false reg: make user can use the app at now!
	public function falseReg($username="", $passwd="") {

		// check that username exist!
		if($this->get_one("*", "user", "user_name='$username'")){
			return NULL;
		}
		$passwdHash = md5($passwd);
		if ($result=$this->userLoginOp($username, $passwdHash)) {
			//echo print_r($result);
			return NULL;
		} 
		// 
		else {
			$tmp = range(1, 1000);
			$avatarEntitys = array_keys($this->avatar->getAllBuildInAvatars());
			$avatarTmp = range(0, count($avatarEntitys)-1);
			$nicknameEntitys = $this->get_all("nick_name", "nick", "1=1");
			$nicknameTmp = range(0, count($nicknameEntitys)-1);
			//print_r($tmp);
			//print_r(array_rand($tmp, 1));
			$dataArray = array(
				// Q2: in the same time: two ID may be the same! wait for solved
				"user_name" => md5("vline" . time() . array_rand($tmp, 1000)[0]),
				"user_passwd" => '',
				"user_passwdhash" => md5(time() . array_rand($tmp, 999)[0]),
				"user_avatar"=> 'buildin_'.$avatarEntitys[array_rand($avatarTmp, 2)[0]],
				"user_regtime" => time(),
				"user_nickname" => $nicknameEntitys[array_rand($nicknameTmp, 2)[0]]["nick_name"],
				"user_loginnum" => 1,
				"user_lastlogintime" => time()
			);
			$this->insert("user", $dataArray);
			$result = array(
					"userName" => $dataArray["user_name"],
					"userPasswdHash" => $dataArray["user_passwdhash"],
					"userAvatar" => $dataArray["user_avatar"],
					"userNickName"=> $dataArray["user_nickname"]
				);
			return $result;
		}
	}

	public function trueReg($username, $passwd, $oldUsername, $oldPasswdHash) {
		if($this->get_one("*", "user", "user_name='$username'")){
			return NULL;
		}
		$passwdHash = md5($passwd);
		if ($result=$this->userLoginOp($username, $passwdHash)) {
			//echo print_r($result);
			return NULL;
		} 
		$dataArray = array(
			"user_name" => $username,
			"user_passwd" => $passwd,
			"user_passwdhash" => md5($passwd),
			"user_state" => 1,
			"user_lastlogintime" => time()
		);
		// check that machineId exist!
		if (($result=$this->userLoginOp($oldUsername, $oldPasswdHash))) {
			$this->update("user", $dataArray, "user_name='$oldUsername' AND user_passwdhash='$oldPasswdHash'");
			// user_loginnum ++;
			$sql = "UPDATE user set user_loginnum=user_loginnum+1 WHERE 1=1 AND user_name='$username' AND user_passwd='$passwd'";
			$this->query($sql);
			return true;
		} 
		// 
		else {
			return false;
		}
	}


	public function loginValid($username, $passwdHash){
		if(($result=$this->get_one("user_id", "user", "user_name='$username' AND user_passwdhash='$passwdHash'"))){
			$this->userId = $result["user_id"];
			return true;
		}
		return false;
	}

	public function checkUserState($username, $passwdHash){
		if(($result=$this->get_one("*", "user", "user_name='$username' AND user_passwdhash='$passwdHash'"))){
			$this->userId = $result["user_id"];
			$avatarId=split('_',$userInfo['user_avatar']);
			$avatarId=$avatarId[1];
			$avatarUrl=$this->avatar->getBuildInAvatar($avatarId);
			$dataArray = array(
					"userName" => $result["user_name"],
					"userAvatarUrl" => $avatarUrl,
					"userReNum" => $result["user_renum"],
					"userRedNum" => $result["user_rednum"],
					"reMsgId" => $result["re_msg_id"],
					"redMsgId" => $result["red_msg_id"]
				);

			return $dataArray;
		}
		return NULL;
	}


	// send msg should verity username and password!
	public function msgSend($codeId, $username, $msgContent, $msgCoordinate, $msgLocation){
		$userId = $this->get_one("user_id", "user", "user_name='$username'")["user_id"];
		//print_r($userId);
		$dataArray = array(
			"code_id" => $codeId,
			"user_id" => $userId,
			"msg_content" => $msgContent,
			"msg_coordinate" => $msgCoordinate,
			"msg_location" => $msgLocation,
			"msg_time" => time()
		);
		$this->insert("msg", $dataArray);
	}
	public function getUserMsgs($username){
		$userId = $this->get_one("user_id", "user", "user_name='$username'")["user_id"];
		$msgs = $this->get_all("*", "msg", "user_id='$userId'");
		return $msgs;
	}
	public function getCodeMsgs($codeId){
		$msgs = $this->get_all("*", "msg", "code_id='$codeId'");
		return $msgs;
	}
	// response msg should verify reUser's username and password!
	public function msgRes($reUser, $redUser, $msgId){
		$reUserEntity = $this->get_one("*", "user", "user_name='$reUser'");
		$redUserEntity = $this->get_one("*", "user", "user_name='$redUser'");
		$msgEntity = $this->get_one("*", "msg", "msg_id=$msgId");
		$msgDataArray = array("re_user_id" => $msgEntity["re_user_id"].','.$reUserEntity["user_id"]);
		$this->update("msg", $msgDataArray, "msg_id=$msgId");
		$reUserDataArray = array(
				"user_renum" => $reUserEntity["user_renum"]++,
				"re_msg_id" => $reUserEntity["re_msg_id"].','.$msgId
			);
		$redUserDataArray = array(
				"user_rednum" => $redUserEntity["user_rednum"]++,
				"red_msg_id" => $redUserEntity["red_msg_id"].','.$msgId
			);
	}

	public function changeAvatar($userName,$avatarId){
		$avatarUpdateArr=array('user_avatar'=>'buildin_'.$avatarId);
		$this->update('user',$avatarUpdateArr,"user_name=$userName");
		$avatarUrl=$this->avatar->getBuildInAvatar($avatarId);	
		return $avatarUrl;
	}
	public function changeNickName($userName,$nickName){
		$nickUpdateArr=array('user_nickname'=>$nickName);
		$this->update('user',$nickUpdateArr,"user_name=$userName");
	}

}
?>

<!-- <html>
<head>
	<title>A test</title>
</head>
<body> -->

<?php

// $ops = new databaseModelClass();

// if($ops->userLoginOp()){
// 	echo "<p>user login success!</p>";
// }else{
// 	echo "<p>user login error!\n</p>";
// }

// $tempFR = $ops->falseReg();
// echo '<p>'.$tempFR["userName"].'</p>';
// echo '<p>'.$tempFR["userPasswdHash"].'</p>';
// echo '<p>'.$tempFR["userAvatar"].'</p>';
// echo '<p>'.$tempFR["userNickName"].'</p>';
// if($ops->userLoginOp($tempFR["userName"], $tempFR["userPasswdHash"])){
// 	echo "<p>user login success!</p>";
// }else{
// 	echo "<p>user login error!\n</p>";
// }

// $tempTR = $ops->trueReg("xiaofeng", "xiaofeng", $tempFR["userName"], $tempFR["userPasswdHash"]);
// echo '<p>'.$tempTR.'</p>';
// if($tempTR){
// 	echo "<p>user login success!</p>";
// }else{
// 	echo "<p>user login error!\n</p>";
// }

// $ops->msgSend("xx00xx", "xiaofeng", md5("xiaofeng"), "This is a test", "beijing", "Haidian");
// $ops->msgSend("xx00xx", "xiaofeng", md5("xiaofeng"), "This is a test 2", "beijing", "Haidian");
// $ops->msgSend("xx00xx", "xiaofeng", md5("xiaofeng"), "This is a test 3", "beijing", "Haidian");
// $myMsgs = $ops->getUserMsgs("xiaofeng", md5("xiaofeng"));
// echo '<p>'.print_r($myMsgs).'</p>';

// $ops->msgRes("xiaofeng", "111", $myMsgs[0]["msg_id"]);

?>
<!-- 
</body>
</html> -->