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
			$avatar=$dataArray['user_avatar'];
			$avatarId=explode('_',$avatar)[1];
			$avatarUrl=$this->avatar->getBuildInAvatar($avatarId);
			$result = array(
					"userName" => $dataArray["user_name"],
					"userPasswdHash" => $dataArray["user_passwdhash"],
					"userAvatar" => $avatarUrl,
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
	public function msgSend($codeId, $username, $msgContent, $msgCoordinate, $msgLocation, $redUserName=NULL, $redMsgId=NULL){
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
		if(isset($redUserName)&&isset($redMsgId))
			$this->msgRes($userName, $redUserName, $redMsgId);
	}
	public function getUserMsgs($username){
		$msg= $this->get_one("*", "user", "user_name='$username'");
			$avatar=$msg['user_avatar'];
			$avatarId=explode('_',$avatar)[1];
			$avatarUrl=$this->avatar->getBuildInAvatar($avatarId);
			$userMsg=array(
				'userName'=>$msg['user_name'],
				'userAvatar'=>$avatarUrl,
				'userNickName'=>$msg['user_nickname'],
				'userPasswdHash'=>$msg['user_passwdhash'],
				);
		return $userMsg;
	}
	public function getCodeMsgs($codeId){
		$codeMsgs=array();
		$msgs = $this->get_all("*", "msg", "code_id='$codeId'");
		$i=0;
		foreach($msgs as $msg){
			$user=$this->get_one('*','user',"user_id='{$msg['user_id']}'");	
			$avatar=$user['user_avatar'];
			@$avatarId=explode('_',$avatar)[1];
			if(!$avatarId){
				$avatarId=1;
			}
			$avatarUrl=$this->avatar->getBuildInAvatar($avatarId);
			$nick=$user['user_nickname'];
			$nick=substr($nick,0,strlen($nick)-1);
			$codeMsg=array(
				//'userName'=>$user['user_name'],
				'userAvatar'=>$avatarUrl,
				'userNickName'=>$nick,
				'msgId'=>$msg['msg_id'],
				'msgContent'=>$msg['msg_content'],
				//'msgCoordinate'=>$msg['msg_coordinate'],
				//'msgLocation'=>$msg['msg_location'],
				'msgTime'=>$msg['msg_time'],
				//'reUserId'=>$msg['re_user_id'],
				);
			$codeMsgs["$i"]=$codeMsg;
			$i++;
		}
		return $codeMsgs;
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
		$this->update('user',$avatarUpdateArr,"user_name='$userName'");
		$avatarUrl=$this->avatar->getBuildInAvatar($avatarId);	
		return $avatarUrl;
	}
	public function changeNickName($userName,$nickName){
		$nickUpdateArr=array('user_nickname'=>$nickName);
		$this->update('user',$nickUpdateArr,"user_name='$userName'");
	}

}
