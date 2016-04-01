<?php 

class Model_DB extends PhalApi_Model_NotORM {




	protected function getTableName($id){
		return 'base';
	}
	public function select ($id){
		$data   = array();
    $data[] = DI()->notorm->base->select('id,nickname,email')->where('id', $this->id)->fetch();
    return $data;
}
}