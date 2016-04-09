<?php
class CodeClass extends Mysql{

	public function codeScan($codeType,$codeContent){
		$codeHash=md5($codeContent);
		$codeQuery=$this->get_one('*','code',"code_hash='$codeHash'");
		if($codeQuery){
			$this->updateCodeInfo($codeQuery);
			$codeId=$codeQuery['code_id'];	
			$queryType='Have';
		}else{
			$this->newCodeInfo($codeType,$codeContent,$codeHash);
			$codeId=$this->get_one('code_id','code',"code_hash='$codeHash'")['code_id'];
			$queryType='New';
		}
		return array('codeId'=>$codeId,'queryType'=>$queryType);
	}

	private function updateCodeInfo($codeQuery){
		$now=time();
		$scanNum=$codeQuery['code_scannum']+1;
		$codeUpdateArr=array(
			'code_lastscantime'=>$now,
			'code_scannum'=>$scanNum,
			);
		$this->update('code',$codeUpdateArr,"code_id='{$codeQuery['code_id']}'");
	}

	private function newCodeInfo($codeType,$codeContent,$codeHash){
		$now=time();
		$codeInsertArr=array(
			'code_type'=>$codeType,
			'code_content'=>$codeContent,
			'code_hash'=>$codeHash,
			'code_establishtime'=>$now,
			'code_lastscantime'=>$now,
			);
		$this->insert('code',$codeInsertArr);
	}

	public function nameCode($codeId,$codeName){
		$codeNameState=$this->get_one('code_name_state','code',"code_id='$codeId'")['code_name_state'];
		if($codeNameState==0){
			$codeUpdateArr=array('code_name'=>$codeName,'code_name_state'=>'1');
			$this->update('code',$codeUpdateArr,"code_id='$codeId'");
			return true;
		}else{
			return false;
		}
	}
}