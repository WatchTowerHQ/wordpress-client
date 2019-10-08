<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInit1ed00d4e8b35b8ee2068965c940c3d00
{
    public static $files = array (
        '320cde22f66dd4f5d3fd621d3e88b98f' => __DIR__ . '/..' . '/symfony/polyfill-ctype/bootstrap.php',
        'ef6802c8a38664a4b1e8712ed25377fb' => __DIR__ . '/..' . '/shuber/curl/curl.php',
    );

    public static $prefixLengthsPsr4 = array (
        'W' => 
        array (
            'WhatArmy\\Watchtower\\' => 20,
        ),
        'S' => 
        array (
            'Symfony\\Polyfill\\Ctype\\' => 23,
            'Symfony\\Component\\Process\\' => 26,
            'Symfony\\Component\\Finder\\' => 25,
            'Symfony\\Component\\Filesystem\\' => 29,
        ),
        'D' => 
        array (
            'Doctrine\\Common\\Collections\\' => 28,
        ),
        'C' => 
        array (
            'ClaudioSanches\\WPAutoloader\\' => 28,
        ),
        'B' => 
        array (
            'Barryvdh\\Composer\\' => 18,
        ),
        'A' => 
        array (
            'Alchemy\\Zippy\\' => 14,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'WhatArmy\\Watchtower\\' => 
        array (
            0 => __DIR__ . '/../..' . '/src',
        ),
        'Symfony\\Polyfill\\Ctype\\' => 
        array (
            0 => __DIR__ . '/..' . '/symfony/polyfill-ctype',
        ),
        'Symfony\\Component\\Process\\' => 
        array (
            0 => __DIR__ . '/..' . '/symfony/process',
        ),
        'Symfony\\Component\\Finder\\' => 
        array (
            0 => __DIR__ . '/..' . '/symfony/finder',
        ),
        'Symfony\\Component\\Filesystem\\' => 
        array (
            0 => __DIR__ . '/..' . '/symfony/filesystem',
        ),
        'Doctrine\\Common\\Collections\\' => 
        array (
            0 => __DIR__ . '/..' . '/doctrine/collections/lib/Doctrine/Common/Collections',
        ),
        'ClaudioSanches\\WPAutoloader\\' => 
        array (
            0 => __DIR__ . '/..' . '/claudiosanches/wp-autoloader/src',
        ),
        'Barryvdh\\Composer\\' => 
        array (
            0 => __DIR__ . '/..' . '/barryvdh/composer-cleanup-plugin/src',
        ),
        'Alchemy\\Zippy\\' => 
        array (
            0 => __DIR__ . '/..' . '/alchemy/zippy/src',
        ),
    );

    public static $prefixesPsr0 = array (
        'P' => 
        array (
            'Puc_v4p8_' => 
            array (
                0 => __DIR__ . '/..' . '/yahnis-elsts/plugin-update-checker',
            ),
            'Puc_v4_' => 
            array (
                0 => __DIR__ . '/..' . '/yahnis-elsts/plugin-update-checker',
            ),
            'PucReadmeParser' => 
            array (
                0 => __DIR__ . '/..' . '/yahnis-elsts/plugin-update-checker/vendor',
            ),
            'Parsedown' => 
            array (
                0 => __DIR__ . '/..' . '/yahnis-elsts/plugin-update-checker/vendor',
            ),
        ),
    );

    public static $classMap = array (
        'MySQLDump' => __DIR__ . '/..' . '/dg/mysql-dump/src/MySQLDump.php',
        'MySQLImport' => __DIR__ . '/..' . '/dg/mysql-dump/src/MySQLImport.php',
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInit1ed00d4e8b35b8ee2068965c940c3d00::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInit1ed00d4e8b35b8ee2068965c940c3d00::$prefixDirsPsr4;
            $loader->prefixesPsr0 = ComposerStaticInit1ed00d4e8b35b8ee2068965c940c3d00::$prefixesPsr0;
            $loader->classMap = ComposerStaticInit1ed00d4e8b35b8ee2068965c940c3d00::$classMap;

        }, null, ClassLoader::class);
    }
}
