<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInit83f2c126726e6660693fc5bae2a3b26d
{
    public static $prefixLengthsPsr4 = array (
        'R' => 
        array (
            'Rtbr\\' => 5,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'Rtbr\\' => 
        array (
            0 => __DIR__ . '/../..' . '/app',
        ),
    );

    public static $classMap = array (
        'Composer\\InstalledVersions' => __DIR__ . '/..' . '/composer/InstalledVersions.php',
        'Rtbr\\Controllers\\Admin\\Activation' => __DIR__ . '/../..' . '/app/Controllers/Admin/Activation.php',
        'Rtbr\\Controllers\\Admin\\AdminController' => __DIR__ . '/../..' . '/app/Controllers/Admin/AdminController.php',
        'Rtbr\\Controllers\\Admin\\AdminSettings' => __DIR__ . '/../..' . '/app/Controllers/Admin/AdminSettings.php',
        'Rtbr\\Controllers\\Admin\\Meta\\AddMetaBox' => __DIR__ . '/../..' . '/app/Controllers/Admin/Meta/AddMetaBox.php',
        'Rtbr\\Controllers\\Admin\\Meta\\MetaController' => __DIR__ . '/../..' . '/app/Controllers/Admin/Meta/MetaController.php',
        'Rtbr\\Controllers\\Admin\\Meta\\MetaOptions' => __DIR__ . '/../..' . '/app/Controllers/Admin/Meta/MetaOptions.php',
        'Rtbr\\Controllers\\Admin\\RegisterPostType' => __DIR__ . '/../..' . '/app/Controllers/Admin/RegisterPostType.php',
        'Rtbr\\Controllers\\Admin\\ScriptLoader' => __DIR__ . '/../..' . '/app/Controllers/Admin/ScriptLoader.php',
        'Rtbr\\Controllers\\Ajax\\AjaxController' => __DIR__ . '/../..' . '/app/Controllers/Ajax/AjaxController.php',
        'Rtbr\\Controllers\\Ajax\\Facebook' => __DIR__ . '/../..' . '/app/Controllers/Ajax/Facebook.php',
        'Rtbr\\Controllers\\Ajax\\Shortcode' => __DIR__ . '/../..' . '/app/Controllers/Ajax/Shortcode.php',
        'Rtbr\\Controllers\\Marketing\\Offer' => __DIR__ . '/../..' . '/app/Controllers/Marketing/Offer.php',
        'Rtbr\\Controllers\\Marketing\\Review' => __DIR__ . '/../..' . '/app/Controllers/Marketing/Review.php',
        'Rtbr\\Controllers\\Shortcodes' => __DIR__ . '/../..' . '/app/Controllers/Shortcodes.php',
        'Rtbr\\Helpers\\Functions' => __DIR__ . '/../..' . '/app/Helpers/Functions.php',
        'Rtbr\\Hooks\\Backend' => __DIR__ . '/../..' . '/app/Hooks/Backend.php',
        'Rtbr\\Models\\Api' => __DIR__ . '/../..' . '/app/Models/Api.php',
        'Rtbr\\Models\\BusinessInfo' => __DIR__ . '/../..' . '/app/Models/BusinessInfo.php',
        'Rtbr\\Models\\Field' => __DIR__ . '/../..' . '/app/Models/Field.php',
        'Rtbr\\Models\\Review' => __DIR__ . '/../..' . '/app/Models/Review.php',
        'Rtbr\\Models\\SettingsAPI' => __DIR__ . '/../..' . '/app/Models/SettingsAPI.php',
        'Rtbr\\Shortcodes\\BusinessReview' => __DIR__ . '/../..' . '/app/Shortcodes/BusinessReview.php',
        'Rtbr\\Traits\\SingletonTrait' => __DIR__ . '/../..' . '/app/Traits/SingletonTrait.php',
        'Rtbr\\Widgets\\BusinessReview' => __DIR__ . '/../..' . '/app/Widgets/BusinessReview.php',
        'Rtbr\\Widgets\\Widget' => __DIR__ . '/../..' . '/app/Widgets/Widget.php',
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInit83f2c126726e6660693fc5bae2a3b26d::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInit83f2c126726e6660693fc5bae2a3b26d::$prefixDirsPsr4;
            $loader->classMap = ComposerStaticInit83f2c126726e6660693fc5bae2a3b26d::$classMap;

        }, null, ClassLoader::class);
    }
}
