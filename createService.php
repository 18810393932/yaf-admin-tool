<?php

/**
 * 自动创建 model
 *
 * @file   createModel.php
 * @author 李晓龙 <lixiaolong05@baidu.com>
 * @date   15/12/4 10:53
 * @desc   model 自动生成
 */
date_default_timezone_set('PRC');

class createService {

    private $model = null;
    private $name  = null;
    private $dir   = null;

    public function __construct() {
        $cmdParams = getopt('m:n:d:');
        $this->dir = $cmdParams['d'];
        $this->name = $cmdParams['n'];
        $this->model = trim($cmdParams['m']);
    }

    public function writeFile() {
        $res = file_put_contents($this->dir . "/" . ucfirst($this->name) . ".php", $this->getServiceCode());
        echo ".......create service \t", ($res > 0 ? " success" : ' fail'), "\n";
    }

    private function getServiceCode() {
        $parentModel = "Common";
        $content = '<?php
/**
 * +----------------------------------------------------------------------
 * | 璧合科技
 * +----------------------------------------------------------------------
 * | Copyright (c) 2014-2015  All rights reserved.
 * +----------------------------------------------------------------------
 * | Author: 李锦 <lijin@behe.com>
 * +----------------------------------------------------------------------
 * | Desc: ' . ucfirst($this->name) . '.php
 * +----------------------------------------------------------------------
 * | Create Date: ' . date("Y/m/d H:i:s") . '
 * +----------------------------------------------------------------------
 */
 
namespace Svc;
 
class ' . ucfirst($this->name) . ' extends ' . ucfirst($parentModel) . ' {
 
    /**
     * @var \Model\\' . ucfirst($this->model) . '
     */
    protected $model;
        
    protected function setModel() {
        return \Model\\' . ucfirst($this->model) . '::getInstance();
    }
}
    ';
        return $content;
    }
}

$createModel = new  createService();

$createModel->writeFile();

