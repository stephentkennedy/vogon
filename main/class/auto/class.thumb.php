<?php
class thumb extends PDO{
	public $error = false;
	public function __construct($file = __DIR__.DIRECTORY_SEPARATOR.'config.ini'){
		$settings = parse_ini_file($file, true);
		if($settings == false){
			die('Unable to read settings file.');
		}
		$dns = $settings['database']['driver'].':host='.$settings['database']['host'];
		if(!empty($settings['database']['port'])){
			$dns .= ';port='.$settings['database']['port'];
		}
		$dns .= ';dbname='.$settings['database']['name'];
		parent::__construct($dns, $settings['database']['user'], $settings['database']['pass']);
		$this->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	}
	
	public function query($sql, $params = []){
		//Always safe rather than sorry
		$this->beginTransaction();
		$trans = $this->prepare($sql);
		//$trans = $this->binder($trans, $params);
		try{
			$trans->execute($params);
			$this->commit();
			return $trans;
		}catch(PDOException $e){
			$this->rollBack();
			$this->error = $e;
			return false;
		}
	}
	
	//This function allows us to cast ints as ints, rather than the default PDO prepared behavior of strings. This is useful for Limits and Offsets
	public function binder($trans, $params){
		foreach($params as $key => $value){
			if(is_int($value)){
				$trans->bindParam($key, $value, PDO::PARAM_INT);
			}else{
				$trans->bindParam($key, $value, PDO::PARAM_STR);
			}
		}
		return $trans;
	}
}
?>