<?php

$container->loadFromExtension('app', 'config', array(
    'charset'       => 'UTF-8',
    'error_handler' => null,
    'csrf-secret'   => 'xxxxxxxxxx',
    'router'        => array('resource' => '%kernel.root_dir%/config/routing.php'),
    'validation'    => array('enabled' => true, 'annotations' => true),
    'templating'    => array(
        #'assets_version' => "SomeVersionScheme",
    ),
    'session' => array(
        'default_locale' => "en",
        'lifetime'       => "3600",
        'auto_start'     => true,
    ),
));

// Twig Configuration
$container->loadFromExtension('twig', 'config', array(
    'debug'            => '%kernel.debug%',
    'strict_variables' => '%kernel.debug%',
));

// Doctrine Configuration
/*
$container->loadFromExtension('doctrine', 'dbal', array(
    'dbname'   => 'xxxxxxxx',
    'user'     => 'xxxxxxxx',
    'password' => '',
));
$container->loadFromExtension('doctrine', 'orm', array(
    'auto_generate_proxy_classes' => '%kernel.debug%',
    'mappings' => array('HelloBundle' => array()),
));
*/

// Swiftmailer Configuration
/*
$container->loadFromExtension('swiftmailer', 'config', array(
    'transport'  => "smtp",
    'encryption' => "ssl",
    'auth_mode'  => "login",
    'host'       => "smtp.gmail.com",
    'username'   => "xxxxxxxx",
    'password'   => "xxxxxxxx",
));
*/
