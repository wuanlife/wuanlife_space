<?php

namespace Admin\Controller;

use Admin\Builder\AdminListBuilder;
use Admin\Builder\AdminConfigBuilder;

/**
 * Class ConfigController   后台表情管理
 * @package Admin\Controller
 * @author:xjw129xjt xjt@ourstu.com
 */
class ExpressionController extends AdminController
{
    protected $ROOT_PATH = '';
    protected $expressionModel = '';

    public function _initialize()
    {
        parent:: _initialize();
        $this->ROOT_PATH = str_replace('/Application/Admin/Controller/ExpressionController.class.php', '', str_replace('\\', '/', __FILE__));
        $this->expressionModel = D('Core/Expression');
    }

    public function index()
    {
        $pkgList = $this->expressionModel->getPkgList(0);
        $admin_config = new AdminConfigBuilder();
        $data = $admin_config->handleConfig();
        $tab = array();
        foreach ($pkgList as $key => $v) {
            $tab[] = array('data-id' => $v['name'], 'title' => $v['title']);
        }
        $default = array( array('data-id' => 'disable', 'title' => '禁用', 'items' =>array() ),array('data-id' => 'enable', 'title' => '启用', 'items' => $tab));
        $data['PKGLIST'] = $admin_config->parseKanbanArray($data['PKGLIST'],$tab,$default);

        $admin_config->title('表情基本设置')
             ->keyKanban('PKGLIST','表情包状态并排序')
            ->buttonSubmit('', '保存')->data($data);
        $admin_config->display();
    }

    public function add()
    {
        $this->display('add');
    }


    public function upload()
    {

        $aTitle = I('post.title', '', 'op_t');
        if (empty($aTitle)) {
            $this->error('请输入中文名称');
        }


        $config = array(
            'maxSize' => 3145728,
            'rootPath' => './Uploads/',
            'savePath' => 'Expression/',
            'saveName' => '',
            'exts' => array('zip', 'rar'),
            'autoSub' => true,
            'subName' => '',
            'replace' => true,
        );
        $upload = new \Think\Upload($config); // 实例化上传类
        $info = $upload->upload($_FILES);
        if (!$info) { // 上传错误提示错误信息
            $this->error($upload->getError());
        } else { // 上传成功
            $this->unCompression($info['pkg']['savename']);
            $this->writeInfo($info['pkg']['savename'], $aTitle);
            $this->success('上传成功！', U('admin/expression/package'));
        }

    }



    private function writeInfo($filename, $aTitle)
    {

        // $aTitle = I('post.title','','op_t');
        $ExpressionPkg = $this->ROOT_PATH . "/Uploads/Expression/";
        $filename = explode('.', $filename);
        array_pop($filename);
        $filename = implode('.', $filename);
        $pkg = $ExpressionPkg . $filename;
        file_put_contents($pkg . '/info.txt', json_encode(array('title' => $aTitle)));
        return true;
    }
    private function unCompression($filename)
    {
        $ExpressionPkg = $this->ROOT_PATH . "/Uploads/Expression/";
        require_once("./ThinkPHP/Library/OT/PclZip.class.php");
        $pcl = new \PclZip($ExpressionPkg . $filename);
        if ($pcl->extract($ExpressionPkg)) {
            $result = $this->delFile($ExpressionPkg . $filename);
            if ($result) {
                return true;
            }
        }
        return false;
    }

    private function delFile($path)
    {
        $result = @unlink($path);
        if ($result) {
            return true;
        } else {
            return false;
        }

    }


    public function package()
    {
        $pkgList = $this->expressionModel->getPkgList(0);
        $this->assign('list', $pkgList);
        $this->display();
    }


    public function editPackage()
    {
        if (IS_POST) {
            $aName = I('post.name', '', 'op_t');
            $aTitle = I('post.title', '', 'op_t');
            if ($aName == 'miniblog') {
                $this->error('默认项不能编辑');
            }
            $this->write($aName, array('title' => $aTitle));

            $this->success('修改成功');
        } else {
            $aName = I('get.name', '', 'op_t');
            if (empty($aName)) {
                $this->error('参数错误');
            }
            if ($aName == 'miniblog') {
                $this->error('默认项不能编辑');
            }

            $file = $this->read($aName);
            $this->assign('title', $file['title']);
            $this->assign('name', $aName);
            $this->display('edit');
        }
    }

    private function  read($file)
    {
        $ExpressionPkg = $this->ROOT_PATH . "/Uploads/Expression/";
        $file = file_get_contents($ExpressionPkg . $file . '/info.txt');
        return json_decode($file, true);
    }

    private function  write($file, $data)
    {
        $ExpressionPkg = $this->ROOT_PATH . "/Uploads/Expression/";
        $pkg = $ExpressionPkg . $file;
        file_put_contents($pkg . '/info.txt', json_encode($data));
    }



    public function expressionList()
    {
        $aName = I('get.name', '', 'op_t');
        if (empty($aName)) {
            $this->error('参数错误');
        }
        $list = $this->expressionModel->getExpression($aName);
        foreach ($list as &$v) {
            $v['image'] = '<img src="' . $v['src'] . '"/>';
        }
        unset($v);
        $builder = new AdminListBuilder();
        $builder
            ->title('表情列表')
            ->keyText('title', '标题')
            ->keyText('image', '表情图片')->keyDoAction('Admin/Expression/delExpression?name={$filename}&pkg=' . $aName, '删除')
            ->data($list)
            ->display();

    }

    public function delPackage()
    {
        $aName = I('get.name', '', 'op_t');
        if (empty($aName)) {
            $this->error('参数错误');
        }
        if ($aName == 'miniblog') {
            $this->error('默认项不能删除');
        }
        $path = $this->ROOT_PATH . "/Uploads/Expression/" . $aName . '/';
        $res = $this->deldir($path);
        if ($res) {
            $this->success('删除成功');
        } else {
            $this->error('删除失败');
        }
    }

    public function delExpression()
    {
        $aName = I('get.name', '', 'op_t');
        $pkg = I('get.pkg', '', 'op_t');

        if (empty($aName) || empty($pkg)) {
            $this->error('参数错误');
        }
        if ($pkg == 'miniblog') {
            $path = $this->ROOT_PATH . '/Application/Core/Static/images/expression/miniblog/' . $aName;
        } else {
            $path = $this->ROOT_PATH . "/Uploads/Expression/" . $pkg . '/' . $aName;
        }
        $res = $this->delFile($path);
        if ($res) {
            $this->success('删除成功');
        } else {
            $this->error('删除失败');
        }


    }


    private function deldir($dir)
    {
        //先删除目录下的文件：
        $dh = opendir($dir);
        while ($file = readdir($dh)) {
            if ($file != "." && $file != "..") {
                $fullpath = $dir . "/" . $file;
                if (!is_dir($fullpath)) {
                    unlink($fullpath);
                } else {
                    $this->deldir($fullpath);
                }
            }
        }

        closedir($dh);
        //删除当前文件夹：
        if (rmdir($dir)) {
            return true;
        } else {
            return false;
        }
    }


}
