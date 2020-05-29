<?php


// composer
if (!file_exists(BASE_PATH . '/vendor/autoload.php')) {
    die('The project needs Composer, please check vendor directory');
}
include_once BASE_PATH . '/vendor/autoload.php';
include_once APP_PATH . '/Core/Common.php';


// autoload
$autoload_path = [
    APP_PATH . '/Bootstrap/',
];
spl_autoload_register(function ($file_name) use ($autoload_path) {
    foreach ($autoload_path as $path) {
        $file = $path . $file_name . '.php';
        if (is_file($file)) {
            include $file;
            break;
        }
    }
});


include APP_PATH . "/Bootstrap/Services.php";