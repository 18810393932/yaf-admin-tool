<?php

/**
 * Created by PhpStorm.
 *
 * @file   createController.php
 * @author 李晓龙 <lixiaolong05@baidu.com>
 * @date   15/12/4 13:12
 * @desc   createController.php
 */
date_default_timezone_set('PRC');

class CreateController {

    private $controller = null;
    private $service    = null;
    private $redis      = null;
    private $tableModel = null;
    private $online     = null;
    private $dir        = null;
    private $search     = null;

    public function __construct() {
        $cmdParams = getopt('c:s:d:');
        $this->controller = $cmdParams['c'];
        $this->service = $cmdParams['s'];
        $this->dir = empty($cmdParams['d']) ? "./" : $cmdParams['d'];
    }

    public function create() {
        $res = file_put_contents($this->dir . "/" .  ucfirst(strtolower($this->controller)) . ".php", $this->createCode($this->redis == 'y'));
        echo ".......create controller \t", ($res > 0 ? " success" : ' fail'), "\n";
    }

    private function createCode() {

        $like = '';
        if ($this->search != 'n' && !empty($this->search)) {
            $like = "array('{$this->search}')";
        }
        $code = '<?php
/**
 * +----------------------------------------------------------------------
 * | 璧合科技
 * +----------------------------------------------------------------------
 * | Copyright (c) 2014-2015  All rights reserved.
 * +----------------------------------------------------------------------
 * | Author: 李锦 <lijin@behe.com>
 * +----------------------------------------------------------------------
 * | Desc: ' . ucfirst($this->controller) . '.php
 * +----------------------------------------------------------------------
 * | Create Date: ' . date("Y/m/d H:i:s") . '
 * +----------------------------------------------------------------------
 */
 
class ' . ucfirst($this->controller) . 'Controller extends CommonController{
    
    /**
     * @var \Svc\\' . ucfirst($this->service) . '
     */
    protected $service;
    protected $address     = "' . $this->service . 'List";

    protected function setService() {
        return \Svc\\' . ucfirst($this->service) . '::getInstance();
    }

    public function ' . $this->service . 'ListAction() {
        $like = array(); //模糊查询的字段 
        $this->showDataList($where = array(), $like);
    }
    
    public function edit' . ucfirst($this->service) . 'Action() {
        $id = intval($this->getRequest()->getParam(\'id\'));
        $dataInfo = $this->service->getDataInfo(array(\'id\'=>$id));
        $this->_view->assign("info", $dataInfo);
    }

    public function save' . ucfirst($this->service) . 'Action() {
        \Yaf\Dispatcher::getInstance()->disableView();
        $data = $this->getRequest()->getPost();
        $result = $this->service->updateData(array(\'id\' => intval($data[\'id\'])), $data);
        $this->notice($result, $this->address);
    }

    public function delete' . ucfirst($this->service) . 'Action() {
        \Yaf\Dispatcher::getInstance()->disableView();
        $id = intval($this->getRequest()->getParam(\'id\'));
        $result = $this->service->deleteData(array("id" => $id));
        $this->notice($result, $this->address);
    }

    public function add' . ucfirst($this->service) . 'Action() {

    }

    public function insert' . ucfirst($this->service) . 'Action() {
        \Yaf\Dispatcher::getInstance()->disableView();
        $data = $this->getRequest()->getPost();
        $result = $this->service->addData($data);
        $this->notice($result, $this->address);
    }
}';
        return $code;
    }
}

$createController = new CreateController();

$createController->create();