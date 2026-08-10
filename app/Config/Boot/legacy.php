<?php

/**
 * Custom autoloader for global-namespace (legacy CI3) classes that live
 * in app/Libraries and app/Models. Class file name must match the class
 * name, except for the MY_* families that share a single file.
 */
spl_autoload_register(function (string $class): void {
    if (str_contains($class, '\\')) {
        return; // namespaced classes are handled by the framework autoloader
    }

    $multi = [
        'MY_Controller'       => 'MY_Controller.php',
        'Portal_Controller'   => 'MY_Controller.php',
        'Admin_Controller'    => 'MY_Controller.php',
        'Admin_CRUD_Controller' => 'MY_Controller.php',
        'MY_Model'            => 'MY_Model.php',
    ];

    if (isset($multi[$class])) {
        require_once APPPATH . 'Libraries/' . $multi[$class];

        return;
    }

    foreach ([APPPATH . 'Libraries', APPPATH . 'Models'] as $dir) {
        $file = $dir . DIRECTORY_SEPARATOR . $class . '.php';
        if (is_file($file)) {
            require_once $file;

            return;
        }
    }
});
