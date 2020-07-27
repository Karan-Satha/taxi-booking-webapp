<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInite8c8671be2c724019420727225541cae
{
    public static $prefixLengthsPsr4 = array (
        'P' => 
        array (
            'PHPMailer\\PHPMailer\\' => 20,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'PHPMailer\\PHPMailer\\' => 
        array (
            0 => __DIR__ . '/..' . '/phpmailer/phpmailer/src',
        ),
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInite8c8671be2c724019420727225541cae::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInite8c8671be2c724019420727225541cae::$prefixDirsPsr4;

        }, null, ClassLoader::class);
    }
}