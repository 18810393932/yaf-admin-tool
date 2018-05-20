
<?php
/**
 * Created by PhpStorm.
 * @file   autoCreate.php
 * @author 李晓龙 <lixiaolong05@baidu.com>
 * @date   15/12/22 13:06
 * @desc   autoCreate.php
 */
$modelDir = '/Volumes/Develop/web/betime/test/';
$serviceDir='/Volumes/Develop/web/betime/test/';
$controllerDir='/Volumes/Develop/web/betime/test/';
$viewDir='/Volumes/Develop/web/betime/test/';

$table="bt_menu";

$model='menu';
$serviceName='smenu';
$controllerName='cmenu';





system("php createModel.php -t {$table}  -d {$modelDir}");
system("php createService.php  -m {$model} -n {$serviceName} -d {$serviceDir} ");
system("php createController.php -c {$controllerName} -s {$serviceName} -d {$controllerDir} ");
system("php createTemplate.php  -t {$table} -c {$controllerName} -d {$viewDir}");

