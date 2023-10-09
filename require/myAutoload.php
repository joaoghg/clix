<?php

function autoload($className) {
    $className = str_replace('\\', '/', $className);

    $classFile = $_SERVER['DOCUMENT_ROOT'].'/require/'.$className.'.php';

    if (file_exists($classFile)) {
        require_once $classFile;
    }
}

spl_autoload_register('autoload');
