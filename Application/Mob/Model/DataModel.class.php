<?php
namespace Mob\Model;

use Think\Model;

/**字段数据模型，通过该模型可以方便地对字段信息实现读和取
 * Class DataModel
 */
class DataModel extends Model
{
    protected $tableName = "cat_data";

    public function addData($name, $value, $info_id, $entity_id)
    {
        $map['name'] = $name;
        $map['entity_id'] = $entity_id;
        $profile = D('cat_field')->where($map)->find();
        $content_data['id'] = $profile;
        $content_data['field_id'] = $profile['id'];
        if (!$this->validateField($profile, $value)) {
            return false;
        }


        if (!is_array($value)) {
            //如果值不是数组
            if ($profile['input_type'] == IT_DATE) {
                $content_data['value'] = strtotime($value);
            } else {
                $content_data['value'] = $value;
            }
            $content_data['info_id'] = $info_id;
            return $this->add($content_data);
        } else {
            $rs = 1;
            foreach ($value as $v) {
                //如果是数组
                $content_data['value'] = op_t($v);
                $content_data['info_id'] = $info_id;
                $rs = ($rs && $this->add($content_data));
            }
            return $rs;
        }
    }




    private function validateField($profile, $value)
    {
        $args = $this->convertUrlQuery($profile['args']);

        $min = isset($args['min']) ? $args['min'] : 0;
        $max = isset($args['max']) ? $args['max'] : -1;
        $need = isset($args['need']) ? $args['need'] : 0;
        $error = isset($args['error']) ? $args['error'] : '';
        $this->error = $error;
        switch ($profile['input_type']) {
            case IT_CHECKBOX:
                if ($need) {
                    if (trim($value) == '') {
                        return false;
                    }
                }
                break;
            case IT_DATE:
                if ($need) {
                    if (trim($value) == '') {
                        return false;
                    }
                }
                break;
            case IT_EDITOR:
                return true;
            case IT_MULTI_TEXT:
            case IT_SINGLE_TEXT:
                if ($min != 0) {//如果设置了最小值
                    if (mb_strlen($value, 'utf-8') < $min) {
                        return false;
                    }
                }
                if ($max != -1) {
                    if (mb_strlen($value, 'utf-8') > $max) {
                        return false;
                    }
                }
                break;
            case IT_PIC:

                if ($need) {
                    if (intval($value) == 0) {
                        return false;
                    }
                }
                break;
            case IT_SELECT:
                if ($need) {
                    if ($value == -1) {
                        return false;
                    }
                }
                break;
        }
        return true;
    }

    /**
     * Returns the url query as associative array
     *
     * @param    string    query
     * @return    array    params
     */
    private function convertUrlQuery($query)
    {
        $queryParts = explode('&', $query);

        $params = array();
        foreach ($queryParts as $param) {
            $item = explode('=', $param);
            $params[$item[0]] = $item[1];
        }

        return $params;
    }

    private function getUrlQuery($array_query)
    {
        $tmp = array();
        foreach ($array_query as $k => $param) {
            $tmp[] = $k . '=' . $param;
        }
        $params = implode('&', $tmp);
        return $params;
    }

    /**通过信息ID获取到所有相关数据
     * @param $info_id
     * @return array
     */
    public function getByInfoId($info_id)
    {
        $map['info_id'] = $info_id;

        $data = array();
        $dataRows = $this->where($map)->order('data_id asc')->select();
        foreach ($dataRows as $v) {
            $profile = D('cat_field')->where('id=' . $v['field_id'])->find();
           if( strpos($profile['name'],'zhaopian')===0){
               if(empty($v['value'])){
                   $data[$profile['name']]['data']=NULL;
               }else{
                   $data[$profile['name']]['data'] = getThumbImageById($v['value']);
               }
           }else{
               $data[$profile['name']]['data'] = $v['value'];
           }
            $data[$profile['name']]['field'] = $profile;
            $data[$profile['name']]['values'] = $values = json_decode($profile['option'], true);
        }

        return $data;
    }


}