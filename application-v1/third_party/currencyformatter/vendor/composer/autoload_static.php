<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInit5d4b4327a5e646796234f904b54d5b91
{
    public static $files = array (
        '5a29f2abde115bb0e1aa502d691e2e50' => __DIR__ . '/..' . '/gerardojbaez/money/src/helpers.php',
    );

    public static $prefixLengthsPsr4 = array (
        'G' => 
        array (
            'Gerardojbaez\\Money\\' => 19,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'Gerardojbaez\\Money\\' => 
        array (
            0 => __DIR__ . '/..' . '/gerardojbaez/money/src',
        ),
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInit5d4b4327a5e646796234f904b54d5b91::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInit5d4b4327a5e646796234f904b54d5b91::$prefixDirsPsr4;

        }, null, ClassLoader::class);
    }
}