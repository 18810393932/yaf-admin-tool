<?php
/**
 * +----------------------------------------------------------------------
 * | 璧合科技
 * +----------------------------------------------------------------------
 * | Copyright (c) 2014-2015  All rights reserved.
 * +----------------------------------------------------------------------
 * | Author: 李锦 <lijin@behe.com>
 * +----------------------------------------------------------------------
 * | Create Date: 2018/4/16 17:44
 * +----------------------------------------------------------------------
 */


//fwrite(STDOUT, 'table name :');
//$table = trim(fgets(STDIN));

$table='bt_analyse';

fwrite(STDOUT, 'Ues the same name to create script ? (y/n):');
//if (trim(fgets(STDIN)) == "y") {
if ("y") {
    $temp = explode('_', $table);
    $controllerName = $modelName = $serviceName = end($temp);
} else {
    fwrite(STDOUT, 'controller name :');
    $controllerName = trim(fgets(STDIN));

    fwrite(STDOUT, 'model name :');
    $modelName = trim(fgets(STDIN));

    fwrite(STDOUT, 'service name :');
    $serviceName = trim(fgets(STDIN));
}

delDirAndFile(__DIR__ . '/script');

$dir = array(
    'controllerDir' => __DIR__ . '/script/controller',
    'modelDir'      => __DIR__ . '/script/model',
    'serviceDir'    => __DIR__ . '/script/svc',
    'templateDir'   => __DIR__ . '/script/views',
);
foreach ($dir as $item) {
    is_dir($item) || mkdir($item, 0777, true);
}

system("php ".__DIR__."/createModel.php -t {$table} -n {$modelName}  -d {$dir['modelDir']}");
system("php ".__DIR__."/createService.php  -m {$modelName} -n {$serviceName} -d {$dir['serviceDir']} ");
system("php ".__DIR__."/createController.php -c {$controllerName} -s {$serviceName} -d {$dir['controllerDir']} ");
system("php ".__DIR__."/createTemplate.php  -t {$table} -c {$controllerName} -d {$dir['templateDir']}");

echo 'Move  the script to your web site！！ ba la ba la  !!!! '.PHP_EOL.PHP_EOL;

//循环删除目录和文件函数
function delDirAndFile($dirName) {
    if ($handle = opendir($dirName)) {
        while (false !== ($item = readdir($handle))) {
            if ($item != "." && $item != "..") {
                if (is_dir("$dirName/$item")) {
                    delDirAndFile("$dirName/$item");
                } else {
                    if (unlink("$dirName/$item"))
                        echo "成功删除文件： $dirName/$item\n";
                }
            }
        }
        closedir($handle);
        if (rmdir($dirName)) echo "成功删除目录： $dirName\n";
    }
}