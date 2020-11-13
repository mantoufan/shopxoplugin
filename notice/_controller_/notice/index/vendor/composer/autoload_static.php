<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInitcfb34fab6fdd5e75c4ff1f0475babb66
{
    public static $prefixLengthsPsr4 = array (
        't' => 
        array (
            'tinymeng\\tools\\' => 15,
            'tinymeng\\code\\' => 14,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'tinymeng\\tools\\' => 
        array (
            0 => __DIR__ . '/..' . '/tinymeng/tools/src',
        ),
        'tinymeng\\code\\' => 
        array (
            0 => __DIR__ . '/..' . '/tinymeng/code/src',
        ),
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInitcfb34fab6fdd5e75c4ff1f0475babb66::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInitcfb34fab6fdd5e75c4ff1f0475babb66::$prefixDirsPsr4;

        }, null, ClassLoader::class);
    }
}
