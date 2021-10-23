<?php
/*
Plugin Name: F13 Movies
Plugin URI: https://f13.dev/wordpress-plugins/wordpress-plugin-movies/
Description: Display Movie and TV Show information on your blog with a shortcode
Version: 2.0.0
Author: F13Dev
Author URI: https://f13.dev
Text Domain: f13-movies
*/

namespace F13\Movies;

if (!function_exists('get_plugins')) require_once(ABSPATH.'wp-admin/includes/plugin.php');
if (!defined('F13_MOVIES')) define('F13_MOVIES', get_plugin_data(__FILE__, false, false));
if (!defined('F13_MOVIES_PATH')) define('F13_MOVIES_PATH', plugin_dir_path( __FILE__ ));
if (!defined('F13_MOVIES_URL')) define('F13_MOVIES_URL', plugin_dir_url(__FILE__));

class Plugin
{
    public function init()
    {
        spl_autoload_register(__NAMESPACE__.'\Plugin::loader');

        add_action('wp_enqueue_scripts', array($this, 'enqueue'));

        $c = new Controllers\Control();

        if (is_admin()) {
            $a = new Controllers\Admin();
        }
    }

    public static function loader($name)
    {
        $name = trim(ltrim($name, '\\'));
        if (strpos($name, __NAMESPACE__) !== 0) {
            return;
        }
        $file = str_replace(__NAMESPACE__, '', $name);
        $file = ltrim(str_replace('\\', DIRECTORY_SEPARATOR, $file), DIRECTORY_SEPARATOR);
        $file = plugin_dir_path(__FILE__).strtolower($file).'.php';

        if ($file !== realpath($file) || !file_exists($file)) {
            wp_die('Class not found: '.htmlentities($name));
        } else {
            require_once $file;
        }
    }

    public function enqueue()
    {
        wp_enqueue_style('f13-movies', F13_MOVIES_URL.'css/f13-movies.css', array(), F13_MOVIES['Version']);
        wp_enqueue_script('dashicons');
    }
}

$p = new Plugin();
$p->init();