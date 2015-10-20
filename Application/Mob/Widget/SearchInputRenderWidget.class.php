<?php

/**
 * Created by JetBrains PhpStorm.
 * User: 95
 * Date: 13-7-7
 * Time: 下午7:30
 * To change this template use File | Settings | File Templates.
 */

namespace Mob\Widget;

use Think\Controller;

class SearchInputRenderWidget extends Controller
{
    public function render($data)
    {
        $field = $data;
        switch ($field['input_type']) {
            case IT_SINGLE_TEXT:
            case IT_MULTI_TEXT;
            case IT_EDITOR:
                $file = 'text';
                break;
            case IT_SELECT:

                $file = 'select';
                break;
            case IT_RADIO:
                $file = 'radio';
                break;
            case IT_CHECKBOX:
                $file = 'checkbox';
                break;
            default:
                $file = 'text';
        }

        $this->assign('field', $data);
        $this->display('Widget/SearchInputRender/' . $file);

    }
}