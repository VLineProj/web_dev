<?php
class AvatarClass{
	function getAllBuildInAvatars(){
		$avatarDir='static/avatar/';
		$avatarNames=scandir($avatarDir);
		$avatarArr=array();
		array_shift($avatarNames);
		array_shift($avatarNames);
		foreach($avatarNames as $index => $name){
			$avatarUrl='http://vline.zhengzi.me/static/avatar'.$name;
			$avatarIndex=explode('.',$name);
			$avatarIndex=$avatarIndex[0];
			$avatarArr[$avatarIndex]=$avatarUrl;
		}
		return $avatarArr;
	}

	function getBuildInAvatar($avatarId){
		$avatarUrl='http://vline.zhengzi.me/static/avatar'.$avatarId.'.jpg';
		return $avatarUrl;
	}
}