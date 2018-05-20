<?php

/**
 * cmd php createTemplate.php  -t user -m user -c user
 * Class createTemplate
 */
class createTemplate {

    private $controller = null;
    private $table      = null;
    private $fields     = null;
    private $model      = null;
    private $dir        = null;
    private $tableInfo  = null;

    public function __construct() {
        $cmdParams = getopt('t:m:c:d:f:s:');
        $cmdParams['d'] = !isset($cmdParams['d']) ? "./" : $cmdParams['d'];

        $this->controller = $cmdParams['c'];
        $this->dir = $cmdParams['d'];
        $this->table = trim($cmdParams['t']);
        $this->getComment();
    }

    protected function getEditUrl() {
        $temp = explode("_", $this->table);
        $this->model = end($temp);
        return '/' . $this->controller . "/edit" . ucfirst($this->model);
    }

    protected function getAddUrl() {
        $temp = explode("_", $this->table);
        $this->model = end($temp);
        return '/' . $this->controller . "/add" . ucfirst($this->model);
    }

    protected function getDelUrl() {
        $temp = explode("_", $this->table);
        $model = end($temp);
        return '/' . $this->controller . "/delete" . ucfirst($model);
    }

    protected function getListUrl() {
        $temp = explode("_", $this->table);
        $model = end($temp);
        return '/' . $this->controller . "/" . $model . "List";
    }

    public function run() {
        $code = $this->getShowFields();


        $code = $this->listTemplate($code['title'], $code['field']);
        $res = file_put_contents($this->dir . "/" . strtolower($this->controller) . "list.html", $code);
        echo ".......create listTemplate \t", ($res > 0 ? " success" : ' fail'), "\n";

        $code = $this->addTemplate();
        $res = file_put_contents($this->dir . "/" . 'add' . strtolower($this->controller) . '.html', $code);
        echo ".......create addTemplate \t", ($res > 0 ? " success" : ' fail'), "\n";

        $code = $this->editTemplate();
        $res = file_put_contents($this->dir . "/" . 'edit' . strtolower($this->controller) . '.html', $code);
        echo ".......create editTemplate \t", ($res > 0 ? " success" : ' fail'), "\n";
    }

    private function getComment() {
        $config = (array)require_once 'config.php';
        require 'mysql.php';
        $db = new mysqlConnect($config['host'], $config['user'], $config['password'], $config['db'], $config['port']);
        $sql = "show full fields from {$this->table}";
        $result = $db->query($sql);
        $comment = array();
        foreach ($result as $val) {
            $comment[$val[0]] = $val[8];
        }
        $this->fields = $comment;
        $sql = 'show table status';

        $result = $db->query($sql);
        foreach ($result as $item) {
            $this->tableInfo[$item[0]] = $item[17];
        }
    }

    protected function getActionName() {
        $comment = $this->tableInfo[$this->table];
        return str_replace('表', '', $comment);
    }

    private function getShowFields() {
        $titleList = $fieldList = array();
        $needFieldList = array_keys($this->fields);
        foreach ($this->fields as $key => $val) {
            $assignList = '';
            if (strpos($val, '1')) {
                echo $val;
                $temp = explode(" ", $val);
                $val = array_shift($temp);
                $temp = array_filter($temp);
                $assignList = PHP_EOL . "                            <?php $" . $key . "List= array(";
                foreach ($temp as $item) {
                    $assign = explode(":", $item);
                    $assignList .= "{$assign[0]}=>'" . trim(end($assign)) . "',";
                }
                $assignList .= "); ";
            }
            $titleList[$key] = "                        <th tabindex=\"0\" aria-controls=\"example1\" rowspan=\"1\" colspan=\"1\">{$val}</th>";
            if (empty($assignList)) {
                $fieldList[$key] = '                        <td><?php echo $item["' . $key . '"]?></td>';
            } else {
                $fieldList[$key] = '                        <td>' . $assignList . "echo $" . $key . "List[" . '$item["' . $key . '"]];?>' . PHP_EOL . '                        </td>';
            }
        }
        $titleHtml = $fieldHtml = array();
        foreach ($needFieldList as $val) {
            $titleHtml[$val] = $titleList[$val];
            $fieldHtml[$val] = $fieldList[$val];
        }
        $titleHtml['_edit_'] = "                        <th tabindex=\"0\" aria-controls=\"example1\" rowspan=\"1\" colspan=\"1\">编辑</th>";
        $fieldHtml['_edit_'] = "                        <td>
                            <a href=\"" . $this->getEditUrl() . "/id/<?php echo \$item['id']?>\">编辑</a>
                            <a onclick=\"return confirm('确定删除吗？')\" href=\"" . $this->getDelUrl() . "/id/<?php echo \$item['id']?>\">删除</a>
                        </td>";
        $title = "<tr>\n";
        $title .= implode("\n", $titleHtml);
        $title .= "\n                    </tr>";
        $field = "<tr>\n";
        $field .= implode("\n", $fieldHtml);
        $field .= "\n                    </tr>";
        return array("title" => $title, "field" => $field);
    }

    private function listTemplate($title, $field) {
        $code = '<div class="row">
    <div class="col-xs-12">
        <div class="table-header">
            <div class="row" style="margin-right: 0">
                <div class="col-md-6">' . $this->getActionName() . '列表</div>
                <div class="col-md-6" style="padding-right: 10px">
                    <div class="pull-right tableTools-container no-margin ">
                        <a href="' . $this->getAddUrl() . '" class="btn btn btn-danger btn-xs " style="margin-bottom: 2px; width: 80px ">添加' . $this->getActionName() . '</a>
                    </div>
                </div>
            </div>
        </div>';


        $search = '';
        foreach ($this->fields as $key => $val) {
            $search .= '                                <!--' . $val . ':<input name="' . $key . '" value="<?php echo $search[\'' . $key . '\']?>" type="text" class="form-control input-sm">-->' . PHP_EOL;
        }
        $search .= '                                <!--<button type="submit" class="btn btn-primary  btn-minier" style="height: 27px">&nbsp;&nbsp;搜 索 &nbsp;&nbsp;</button>-->';
        $code .= '
        <div>
            <div id="dynamic-table_wrapper" class="dataTables_wrapper form-inline no-footer">
                <div class="row">
                    <div class="col-xs-12">
                        <form role="form" class="form-horizontal" action="' . $this->getListUrl() . '" method="post">
                            <div id="dynamic-table_filter" class="dataTables_filter">
' . $search . '   
                            </div>
                        </form>
                    </div>
                </div>

                <table class="table table-striped table-bordered table-hover dataTable no-footer" id="dynamic-table" role="grid" aria-describedby="dynamic-table_info">
                    <thead>
                    #title#
                    </thead>

                    <tbody>
                    <?php foreach($dataList as $key=>$item){ ?>
                    #field#
                    <?php } ?>
                    </tbody>
                </table>
                <div class="row">
                    <div class="col-xs-6">
                        <div class="dataTables_info" id="dynamic-table_info" role="status" aria-live="polite">显示 <?php echo  $page->firstRow+1; ?>
                            到 <?php $showCount =$page->firstRow+$page->listRows; echo $showCount >$page->totalRows ? $page->totalRows:$showCount; ?>
                            , 共 <?php echo  $page->totalRows; ?> 条记录
                        </div>
                    </div>
                    <div class="col-xs-6">
                        <div class="dataTables_paginate paging_simple_numbers" id="dynamic-table_paginate">
                            <ul class="pagination">
                                <?php echo $page->show();?>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
    
';
        return str_replace(array("#title#", "#field#"), array($title, $field), $code);
    }

    private function addTemplate() {
        $template = '<form class="form-horizontal" action="/' . $this->controller . '/insert' . ucfirst($this->model) . '" method="post">
    <div class="row">
        <div class="col-md-6 col-md-offset-1">
            <div class="box box-info no-shadow">
                <div class="box-body" style="margin-top: 10px">
        ';

        foreach ($this->fields as $key => $val) {
            if (in_array($key, array("id", "status", "update_time", "create_time"))) {
                continue;
            }

            if (strpos($val, '1')) {
                echo $val;
                $temp = explode(" ", $val);
                $val = array_shift($temp);
                $temp = array_filter($temp);
                $assignList = "$" . $key . "List= array(";
                foreach ($temp as $item) {
                    $assign = explode(":", $item);
                    $assignList .= "{$assign[0]}=>'" . trim(end($assign)) . "',";
                }
                $assignList .= "); ";

                $template .= '
                    <div class="form-group">
                        <label class="col-sm-2 control-label">' . $val . ':</label>
                        <div class="col-sm-10">
                            <select class="form-control" name="son_type">
                                <?php
                                    ' . $assignList . '
                                    foreach(' . "$" . $key . "List" . ' as $key=>$val){ ?>
                                        <option  value="<?php echo $key?>"><?php echo $val?></option>
                                <?php } ?>
                            </select>
                        </div>
                    </div>
                ';
            } else {
                $template .= '
                    <div class="form-group">
                        <label class="col-sm-2 control-label">' . $val . ':</label>
                        <div class="col-sm-10">
                            <input class="form-control" id="' . $key . '" name="' . $key . '" placeholder="请输入' . $val . '" type="text">
                        </div>
                    </div>
        ';
            }
        }
        $template .= '
               </div>
            </div>
        </div>
    </div>
    <!-- /.box-body -->
    <div class="box-footer">
        <div class="clearfix form-actions" style="padding: 13px 20px 14px">
            <div class="col-md-offset-3 col-md-9">
                 <button type="reset" class="btn btn-sm">
                    <i class="ace-icon fa fa-undo bigger-110"></i>
                    Reset
                 </button>
                    &nbsp; &nbsp; &nbsp;
                 <button type="submit" class="btn btn-info btn-primary btn-sm">
                    <i class="ace-icon fa fa-check  bigger-110"></i>
                    Submit
                </button>
            </div>
        </div>
    </div>
</form>';
        return $template;
    }

    public function editTemplate() {
        $template = '<form class="form-horizontal" action="/' . $this->controller . '/save' . ucfirst($this->model) . '" method="post">
    <input type="hidden" name="id" value="<?php echo $info[\'id\'];?>">
    <div class="row">
        <div class="col-md-6 col-md-offset-1">
            <div class="box box-info no-shadow">
                <div class="box-body" style="margin-top: 10px">';

        foreach ($this->fields as $key => $val) {
            if (in_array($key, array("id", "status", "update_time", "create_time"))) {
                continue;
            }
            if (strpos($val, '1')) {
                echo $val;
                $temp = explode(" ", $val);
                $val = array_shift($temp);
                $temp = array_filter($temp);
                $assignList = "$" . $key . "List= array(";
                foreach ($temp as $item) {
                    $assign = explode(":", $item);
                    $assignList .= "{$assign[0]}=>'" . trim(end($assign)) . "',";
                }
                $assignList .= "); ";

                $template .= PHP_EOL.'                    <div class="form-group">
                        <label class="col-sm-2 control-label">' . $val . ':</label>
                        <div class="col-sm-10">
                            <select class="form-control" name="' . $key . '">
                                <?php
                                    ' . $assignList . '
                                    foreach(' . "$" . $key . "List" . ' as $key=>$val){ ?>
                                        <option <?php if($info[\''.$key.'\'] ==$key) echo \'selected\';?>  value="<?php echo $key?>"><?php echo $val?></option>
                                <?php } ?>
                            </select>
                        </div>
                    </div>
                ';
            } else {
                $template .= '
                    <div class="form-group">
                        <label class="col-sm-2 control-label">' . $val . ':</label>
                        <div class="col-sm-10">
                            <input class="form-control" value="<?php echo $info[\'' . $key . '\'];?>" id="' . $key . '" name="' . $key . '" placeholder="请输入' . $val . '" type="text">
                        </div>
                    </div>';
            }
        }
        $template .= '
                </div>
            </div>
        </div>
    </div>
    <!-- /.box-body -->
    <div class="box-footer">
        <div class="clearfix form-actions" style="padding: 13px 20px 14px">
            <div class="col-md-offset-3 col-md-9">
                <button type="reset" class="btn btn-sm">
                    <i class="ace-icon fa fa-undo bigger-110"></i>
                    Reset
                </button>
                 &nbsp; &nbsp; &nbsp;
                 <button type="submit" class="btn btn-info btn-primary btn-sm">
                    <i class="ace-icon fa fa-check  bigger-110"></i>
                    Submit
                </button>
            </div>
        </div>
    </div>
</form>';
        return $template;
    }

}

$createTemplate = new createTemplate();
$createTemplate->run();





