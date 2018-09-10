<?php
/**
 * Plugin Name:     Update PHP Gutenberg Blocks
 * Plugin URI:      https://github.com/nerrad/update-php
 * Description:     A plugin adding Gutenberg blocks for use on the WordPress.org Update PHP page.
 * Author:          Darren Ethier
 * Author URI:      https://darrenethier.com
 * Text Domain:     update-php
 * Domain Path:     /languages
 * Version:         0.1.0
 *
 * @package        WP\update_php
 */

use WP\update_php\Bootstrap;

// define version and this file.
define( 'WP_UPDATE_PHP_VERSION', '0.1.0' );
define( 'WP_UPDATE_PLUGIN_FILE', __FILE__ );
require 'vendor/autoload.php';
Bootstrap::instance( WP_UPDATE_PHP_VERSION, WP_UPDATE_PLUGIN_FILE );
