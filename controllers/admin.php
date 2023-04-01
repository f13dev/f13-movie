<?php namespace F13\Movies\Controllers;

class Admin
{
    public function __construct()
    {
        add_action('admin_menu', array($this, 'admin_menu'));
        add_action('admin_init', array($this, 'register_settings'));
    }

    public static function _update_settings()
    {
        global $f13_movie_settings;

        $f13_movie_settings = array(
            'omdb_api_key'  => get_option('omdb_api_key'),
            'tmdb_api_key'  => get_option('tmdb_api_key'),
            'preferred_api' => get_option('f13_movie_preferred_api'),
        );

        $f13_movie_settings['omdb_enable']   = (!empty($f13_movie_settings['omdb_api_key']));
        $f13_movie_settings['tmdb_enable']   = (!empty($f13_movie_settings['tmdb_api_key']));
        $f13_movie_settings['preferred_api'] = ($f13_movie_settings['preferred_api']) ? $f13_movie_settings['preferred_api'] : 'omdb';        

        return $f13_movie_settings;
    }

    public static function _get_settings()
    {
        global $f13_movie_settings;

        if (empty($f13_movie_settings)) {
            return \F13\Movies\Controllers\Admin::_update_settings();
        }

        return $f13_movie_settings;
    }

    public function admin_menu()
    {
        global $menu;
        $exists = false;
        foreach ($menu as $item) {
            if (strtolower($item[0]) == strtolower('F13 Admin')) {
                $exists = true;
                break;
            }
        }

        if (!$exists) {
            add_menu_page( 'F13 Settings', 'F13 Admin', 'manage_options', 'f13-settings', array($this, 'f13_settings'), 'dashicons-embed-generic', 4);
            add_submenu_page( 'f13-settings', 'Plugins', 'Plugins', 'manage_options', 'f13-settings', array($this, 'f13_settings'));
        }
        add_submenu_page( 'f13-settings', 'Movie (OMDB) Settings', 'Movie (OMDB)', 'manage_options', 'f13-movies', array($this, 'f13_movies_settings'));
    }

    public function f13_movies_settings()
    {
        $v = new \F13\Movies\Views\Admin();

        echo $v->movies_settings();
    }

    public function f13_settings()
    {
        $response = wp_remote_get('https://f13.dev/wp-json/v1/f13-plugins');
        $data     = json_decode(wp_remote_retrieve_body( $response ));

        $v = new \F13\Movies\Views\Admin(array(
            'data' => $data,
        ));

        echo $v->f13_settings();
    }

    public function register_settings()
    {
        register_setting('f13-movies-settings-group', 'omdb_api_key');
        register_setting('f13-movies-settings-group', 'tmdb_api_key');
        register_setting('f13-movies-settings-group', 'f13_movie_preferred_api');
    }
}