<?php
/**
 * +----------------------------------------------------------------------
 * | 璧合科技
 * +----------------------------------------------------------------------
 * | Copyright (c) 2014-2015  All rights reserved.
 * +----------------------------------------------------------------------
 * | Author: 李锦 <lijin@behe.com>
 * +----------------------------------------------------------------------
 * | Desc: Analyse.php
 * +----------------------------------------------------------------------
 * | Create Date: 2018/05/19 10:31:14
 * +----------------------------------------------------------------------
 */
 
class AnalyseController extends CommonController{
    
    /**
     * @var \Svc\Analyse
     */
    protected $service;
    protected $address     = "analyseList";

    protected function setService() {
        return \Svc\Analyse::getInstance();
    }

    public function analyseListAction() {
        $like = array(); //模糊查询的字段 
        $this->showDataList($where = array(), $like);
    }
    
    public function editAnalyseAction() {
        $id = intval($this->getRequest()->getParam('id'));
        $dataInfo = $this->service->getDataInfo(array('id'=>$id));
        $this->_view->assign("info", $dataInfo);
    }

    public function saveAnalyseAction() {
        \Yaf\Dispatcher::getInstance()->disableView();
        $data = $this->getRequest()->getPost();
        $result = $this->service->updateData(array('id' => intval($data['id'])), $data);
        $this->notice($result, $this->address);
    }

    public function deleteAnalyseAction() {
        \Yaf\Dispatcher::getInstance()->disableView();
        $id = intval($this->getRequest()->getParam('id'));
        $result = $this->service->deleteData(array("id" => $id));
        $this->notice($result, $this->address);
    }

    public function addAnalyseAction() {

    }

    public function insertAnalyseAction() {
        \Yaf\Dispatcher::getInstance()->disableView();
        $data = $this->getRequest()->getPost();
        $result = $this->service->addData($data);
        $this->notice($result, $this->address);
    }
}