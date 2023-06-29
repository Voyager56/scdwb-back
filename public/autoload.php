<?php

spl_autoload_register(function ($className) {

    $baseNamespace = 'App\\';
    $basePath = dirname(__DIR__) . '/app';

    $class = str_replace($baseNamespace, '', $className);
    $classFile = str_replace('\\', DIRECTORY_SEPARATOR, $class) . '.php';
    $file = $basePath . DIRECTORY_SEPARATOR . $classFile;

    if (file_exists($file)) {
        require_once $file;
    }
});
