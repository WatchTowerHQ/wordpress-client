<?php
defined('ABSPATH') or die('No script kiddies please!');

/**
 * Plugin Name: WhatArmy Watchtower
 * Plugin URI: https://github.com/WhatArmy/WatchtowerWpClient
 * Description: The WhatArmy WordPress plugin allows us to monitor, backup, upgrade, and manage your site!
 * Author: WhatArmy
 * Version: 3.2.7
 * Author URI: https://whatarmy.com
 **/

/**
 * Constants
 */
define('MP_LARGE_DOWNLOADS', true);

define('WHT_MIN_PHP', "7.2");
define('WHT_MAIN', __FILE__);
define('WHT_MAIN_URI', plugin_dir_url(__FILE__));
define('WHT_DB_VERSION', '1.0');

define('WHT_CLIENT_USER_NAME', 'WatchTowerClient');
define('WHT_CLIENT_USER_EMAIL', 'wpdev@whatarmy.com');

define('WHT_BACKUP_DIR_NAME', 'watchtower_backups');
define('WHT_BACKUP_EXCLUSIONS_ENDPOINT', '/backupExclusions');
define('WHT_BACKUP_DIR', wp_upload_dir()['basedir'].'/'.WHT_BACKUP_DIR_NAME);
define('WHT_BACKUP_FILES_PER_QUEUE', 470);
define('WHT_DB_RECORDS_MAX', 6000);

define('WHT_REPO_URL', 'https://github.com/WatchTowerHQ/wordpress-client');

use ClaudioSanches\WPAutoloader\Autoloader;
use WhatArmy\Watchtower\Watchtower;

if (version_compare(PHP_VERSION, WHT_MIN_PHP) >= 0) {
    /**
     * Run App
     */
    require_once(plugin_dir_path(WHT_MAIN).'/vendor/woocommerce/action-scheduler/action-scheduler.php');
    require __DIR__.'/vendor/autoload.php';

    $autoloader = new Autoloader();
    $autoloader->addNamespace('WhatArmy\Watchtower', __DIR__.'/src');
    $autoloader->addNamespace('WhatArmy\Watchtower\Files', __DIR__.'/src/Files');
    $autoloader->addNamespace('WhatArmy\Watchtower\Mysql', __DIR__.'/src/Mysql');
    $autoloader->register();

    new Watchtower();
} else {
    function wht_admin_notice__error()
    {
        $class = 'notice notice-error';
        $message = __('Woops! Your current PHP version ('.PHP_VERSION.') is not supported by WatchTower. Please upgrade your PHP version to at least v'.WHT_MIN_PHP.'. Older than '.WHT_MIN_PHP.' versions of PHP can cause security and performance problems.',
            'wht-notice');

        printf('<div class="%1$s"><p>%2$s</p></div>', esc_attr($class), esc_html($message));
    }

    add_action('admin_notices', 'wht_admin_notice__error');
}

function wht_admin_notice__deprecation()
{
    ?>
    <div class="error"
         style="padding-top:15px;padding-bottom:15px;display:flex;flex-direction:row">
        <div>
            <img src="<?php echo WHT_MAIN_URI.'assets/images/logo1x.png'; ?>"
                 style="height:100px;padding-right:15px;" alt="">
        </div>
        <div>
            <h1>We’ve moved our plugin codebase to <a href="https://wordpress.org/plugin/watchtowerhq">wordpress.org</a>. <br>
                Use the link below to install the updated plugin (3.3.X).<br>
                Plugin version 3.2.X is no longer supported, please deactivate and uninstall.
            </h1>
            <h4 style="margin-bottom:0;">
                <a href="/wp-admin/plugin-install.php?s=Watchtowerhq&tab=search&type=term">Click Here</a> to install new
                version from wordpress.org.
            </h4>
        </div>
    </div>
    <?php
}

add_action('admin_notices', 'wht_admin_notice__deprecation');


