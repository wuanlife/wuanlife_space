<?php 
/**
* 星球相关DB操作
*/
class Model_Group extends PhalApi_Model_NotORM
{
	public function checkName($name)
	{
		return $this->getORM()->select('id')->where('name = ?', $name)->fetchOne();
	}


    protected function getTableName($id)
    {
        return 'group_base';
    }
	
}






 ?>