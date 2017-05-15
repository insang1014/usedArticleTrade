<?php
    $application_folder = '../application';

    define('SELF', pathinfo(__FILE__, PATHINFO_BASENAME));

    if (is_dir($application_folder)) {
        if (($_temp = realpath($application_folder)) !== FALSE) {
            $application_folder = $_temp;
        } else {
            $application_folder = strtr(
                rtrim($application_folder, '/\\'),
                '/\\',
                DIRECTORY_SEPARATOR.DIRECTORY_SEPARATOR
            );
        }
    } else {
        header('HTTP/1.1 503 Service Unavailable.', TRUE, 503);
        echo 'application 폴더를 찾을 수 없습니다. application 폴더 경로를 확인하시기 바랍니다. : '.SELF;
        exit(3);
    }

    define('APPPATH', $application_folder . DIRECTORY_SEPARATOR);

    if ( ! isset($view_folder[0]) && is_dir(APPPATH.'views'.DIRECTORY_SEPARATOR)) {
        $view_folder = APPPATH.'views';
    } else {
        header('HTTP/1.1 503 Service Unavailable.', TRUE, 503);
        echo 'view 폴더를 찾을 수 없습니다. view 폴더 경로를 확인하시기 바랍니다. : '.SELF;
        exit(3);
    }

    define('VIEWPATH', $view_folder.DIRECTORY_SEPARATOR);

    if (session_status() !== PHP_SESSION_ACTIVE) {
        session_start();
    }
    
    require_once(APPPATH.'config/config.php');
    require_once(APPPATH.'config/helper.php');
    require_once(APPPATH.'libs/application.php');
    require_once(APPPATH.'libs/controller.php');

    $app = new Application();
