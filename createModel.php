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

class CreateModel {

    private $table = null;
    private $mark  = null;
    private $dir   = null;
    private $name  = null;

    public function __construct() {
        $cmdParams = getopt('t:m:d:n:');
        $cmdParams['d'] = !isset($cmdParams['d']) ? "./" : $cmdParams['d'];
        $this->dir = $cmdParams['d'];
        $this->table = trim($cmdParams['t']);
        $this->name = trim($cmdParams['n']);
        $this->mark = empty($cmdParams['m']) ? $this->table : $cmdParams['m'];
    }

    public function writeFile() {
        $res = file_put_contents($this->dir . "/" . ucfirst($this->name) . ".php", $this->getModelCode());
        echo ".......create model \t", ($res > 0 ? " success" : ' fail'), "\n";
    }

    private function getModelCode() {
        $parentModel = "Common";
        $fileName = explode("_", $this->mark);
        $first = array_shift($fileName);
        $end = end($fileName);
        $modeName = str_replace(array('_', $first), '', $this->mark);
        $modeName = str_replace($end, ucfirst($end), $modeName);

        $content = '<?php
/**
 * +----------------------------------------------------------------------
 * | 璧合科技
 * +----------------------------------------------------------------------
 * | Copyright (c) 2014-2015  All rights reserved.
 * +----------------------------------------------------------------------
 * | Author: 李锦 <lijin@behe.com>
 * +----------------------------------------------------------------------
 * | Create Date: ' . date("Y/m/d H:i:s") . '
 * +----------------------------------------------------------------------
 */
 
namespace Model;
 
class ' . ucfirst($modeName) . ' extends ' . ucfirst($parentModel) . '{
';
        $content .= '
    protected $trueTableName = \'' . $this->table . '\';
    ';
        $content .= "
}";
        return $content;
    }
}

$createModel = new  CreateModel();

$createModel->writeFile();

