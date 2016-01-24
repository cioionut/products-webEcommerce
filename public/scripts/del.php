<?php

define('ROOT', dirname(dirname(__FILE__)));
define('UPLOADS_DATA', ROOT.'/uploads/data');
define('UPLOADS_IMAGES', ROOT.'/uploads/images');

if(isset($_POST['id']) && isset($_POST['file'])){
    if($_POST['id'] == 1){
        $file = UPLOADS_DATA . '/' . $_POST['file'];
    }
    else{
        $file = UPLOADS_IMAGES . '/' .$_POST['file'];
    }
    echo $file;
    if(file_exists($file)){
        unlink($file);
    }
}
