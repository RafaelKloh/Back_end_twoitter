<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInitbc0646d98fca80b339df8e7410b46fb3
{
    public static $prefixLengthsPsr4 = array (
        'P' => 
        array (
            'PHPMailer\\PHPMailer\\' => 20,
        ),
        'A' => 
        array (
            'App\\' => 4,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'PHPMailer\\PHPMailer\\' => 
        array (
            0 => __DIR__ . '/..' . '/phpmailer/phpmailer/src',
        ),
        'App\\' => 
        array (
            0 => __DIR__ . '/../..' . '/src',
        ),
    );

    public static $classMap = array (
        'Composer\\InstalledVersions' => __DIR__ . '/..' . '/composer/InstalledVersions.php',
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInitbc0646d98fca80b339df8e7410b46fb3::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInitbc0646d98fca80b339df8e7410b46fb3::$prefixDirsPsr4;
            $loader->classMap = ComposerStaticInitbc0646d98fca80b339df8e7410b46fb3::$classMap;

        }, null, ClassLoader::class);
    }
}
