<?php namespace F13\Movies\Controllers;

class Control
{
    public $cache_timeout;

    public function __construct()
    {
        add_shortcode('movie', array($this, 'movie_shortcode'));
        add_shortcode('actor', array($this, 'actor_shortcode'));
        
        $this->cache_timeout = 1440;
    }

    public function _check_cache( $timeout )
    {
        if ( empty($timeout) ) {
            $timeout = (int) $this->cache_timeout;
        }
        if ( (int) $timeout < 1 ) {
            $timeout = 1;
        }

        $timeout = $timeout * 60;

        return $timeout;
    }

    public function get_attachment_id($file_name)
    {
        $m = new \F13\Movies\Models\OMDB();
        return $m->get_image_id($file_name);
    }

    public function get_cover($file)
    {
        $file_name = explode('/', $file);
        $file_name = end($file_name);
        $image_id = $this->get_attachment_id($file_name);
        if ($image_id) {
            $console = (F13_MOVIES_DEV) ? '<script>console.log("Loading movie image from local file");</script>' : '';
            $file_url = wp_get_attachment_url($image_id);
        } else {
            require_once(ABSPATH.'wp-admin/includes/media.php');
            require_once(ABSPATH.'wp-admin/includes/file.php');
            require_once(ABSPATH.'wp-admin/includes/image.php');

            media_sideload_image($file, get_the_ID(), ' - Poster');
            $console = (F13_MOVIES_DEV) ? '<script>console.log("Side loading movie image remoate file");</script>' : '';
            $file_url = wp_get_attachment_url($this->get_attachment_id($file_name));
        }

        return (object) array(
            'console'  => $console,
            'file_url' => $file_url,
        );
    }

    public function actor_shortcode($atts = array())
    {
        extract(shortcode_atts(array(
            'tmdb'        => '',
            'name'        => '',
            'credits'     => 1,
            'cachetime'   => '1440',
        ), $atts));

        if ($cachetime > 0) {
            $cachetime = $this->_check_cache($cachetime);
        }

        $cache_key = 'f13-movies-actor-'.sha1(serialize($atts).F13_MOVIES['Version']);
        $transient = ($cachetime == 0) ? false : get_transient($cache_key);
        if (!F13_MOVIES_DEV && $transient) {
            $v = (F13_MOVIES_DEV) ? '<script>console.log("Building actor information from transient: '.$cache_key.'");</script>' : '';
            $v .= $transient;

            return $v;
        }

        if (empty($tmdb) && empty($name)) {
            return '<div class="f13-movies-error">'.__('Please provide either a "tmdb" or "name" attribute', 'f13-movies').'</div>';
        }

        $m = new \F13\Movies\Models\TMDB();
        $data = $m->retrieve_actor_data(array(
            'id' => $tmdb,
            'name' => $name,
            'credits' => (int) $credits,
        ));

        $cover = $this->get_cover($data->profile_path);

        $v = new \F13\Movies\Views\Actors(array(
            'data' => $data,
            'credits' => $credits,
            'local_image' => $cover->file_url,
        ));

        $console = (F13_MOVIES_DEV) ? '<script>console.log("Building actor information from API, setting: '.$cache_key.'");</script>' : '';
        $return = $v->actor_shortcode();

        set_transient($cache_key, $return, $cachetime);

        return $cover->console.$console.$return;
    }

    public function movie_shortcode($atts = array())
    {
        extract(shortcode_atts(array(
            'imdb'        => '',
            'title'       => '',
            'type'        => '',
            'year'        => '',
            'plot'        => 'full',
            'cachetime'   => '1440',
            'information' => '0',
            'disable'     => '',
            'trailer'     => '',
            'image_size'  => '1200',
            'api'         => ''
        ), $atts));

        if (empty($title) && empty($imdb)) {
            return '<div class="f13-movies-error">'.__('Please provide either an "imdb" or "title" attribute', 'f13-movies').'</div>';
        }

        // Work out which API to use
        $settings = \F13\Movies\Controllers\Admin::_get_settings();
        if (!($api && $settings[$api.'_enable'])) {
            $api = $settings['preferred_api'];
        }        

        $disable = explode(',', $disable);

        if ($cachetime > 0) {
            $cachetime = $this->_check_cache($cachetime);
        } 
        $cache_key = 'f13-movies-'.sha1(serialize($atts).$settings['preferred_api'].F13_MOVIES['Version']);
        $transient = ($cachetime == 0) ? false : get_transient($cache_key);
        if (!F13_MOVIES_DEV && $transient) {
            $v =  (F13_MOVIES_DEV) ? '<script>console.log("Building movie information from transient: '.$cache_key.'");</script>' : '';
            $v .= $transient;

            return $v;
        }

        if ($plot != 'short') {
            $plot = 'full';
        }

        if ($api == 'omdb') {
            $m = new \F13\Movies\Models\OMDB();
            $data = $m->retrieve_movie_data(array(
                'i'    => $imdb,
                't'    => $title,
                'type' => $type,
                'y'    => $year,
                'plot' => $plot,
            ));
        } else 
        if ($api == 'tmdb') {
            $m = new \F13\Movies\Models\TMDB();
            $data = $m->retrieve_movie_data(array(
                'imdb'  => $imdb,
                'title' => $title,
                'type'  => $type,
                'year'  => $year,
                'plot'  => $plot,
            ));
            if ($data && property_exists($data, 'poster_path') && $data->poster_path) {
                $data->poster_path = 'https://www.themoviedb.org/t/p/w600_and_h900_bestv2'.$data->poster_path;
            }
        }

        if (is_wp_error($data)) {
            return '<div class="f13-movies-error"><strong>'.__('Error', 'f13-movies').': </strong>'.$data->get_error_message().'</div>';
        }

        if (property_exists($data, 'Error')) {
            return '<div class="f13-movies-error"><strong>'.__('Error', 'f13-movies').': </strong>'.$data->Error.'</div>';
        }

        $cover = $this->get_cover(($api == 'tmdb' ? $data->poster_path : $data->Poster));

        $v = new \F13\Movies\Views\Movies(array(
            'data'        => $data,
            'disable'     => $disable,
            'local_image' => $cover->file_url,
            'information' => (int) $information,
            'trailer'     => $trailer,
            'image_size'  => $image_size,
            'api'         => $api,
        ));

        $console = (F13_MOVIES_DEV) ? '<script>console.log("Building movie information from API, setting: '.$cache_key.'");</script>' : '';

        $return = $v->movie_shortcode();

        set_transient($cache_key, $return, $cachetime);

        return $cover->console.$console.$return;
    }
}