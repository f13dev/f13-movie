<?php namespace F13\Movies\Controllers;

class Control
{
    public function __construct()
    {
        add_shortcode('movie', array($this, 'movie_shortcode'));
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
            $console = '<script>console.log("Loading movie image from local file");</script>';
            $file_url = wp_get_attachment_url($image_id);
        } else {
            require_once(ABSPATH.'wp-admin/includes/media.php');
            require_once(ABSPATH.'wp-admin/includes/file.php');
            require_once(ABSPATH.'wp-admin/includes/image.php');

            media_sideload_image($file, get_the_ID(), ' - Poster');
            $console = '<script>console.log("Side loading movie image remoate file");</script>';
            $file_url = wp_get_attachment_url($this->get_attachment_id($file_name));
        }

        return (object) array(
            'console' => $console,
            'file_url' => $file_url,
        );
    }

    public function movie_shortcode($atts = array())
    {
        extract(shortcode_atts(array(
            'imdb' => '',
            'title' => '',
            'type' => '',
            'year' => '',
            'plot' => 'full',
            'cachetime' => '1440',
            'information' => '0',
            'disable' => '',
        ), $atts));

        if (empty($title) && empty($imdb)) {
            return '<div class="f13-movies-error">'.__('Please provide either an "imdb" or "title" attribute', 'f13-movies').'</div>';
        }

        $disable = explode(',', $disable);

        $cachetime = $this->_check_cache($cachetime);

        $cache_key = 'f13-movies-'.sha1(serialize($atts));
        $transient = get_transient($cache_key);
        if ($transient) {
            $v = '<script>console.log("Building movie information from transient: '.$cache_key.'");</script>';
            $v .= $transient;

            return $v;
        }

        if ($plot != 'short') {
            $plot = 'full';
        }

        $m = new \F13\Movies\Models\OMDB();
        $data = $m->retrieve_movie_data(array(
            'i' => $imdb,
            't' => $title,
            'type' => $type,
            'y' => $year,
            'plot' => $plot,
        ));

        if (property_exists($data, 'Error')) {
            return '<div class="f13-movies-error"><strong>'.__('Error', 'f13-movies').': </strong>'.$data->Error.'</div>';
        }

        $cover = $this->get_cover($data->Poster);

        $v = new \F13\Movies\Views\Movies(array(
            'data' => $data,
            'disable' => $disable,
            'local_image' => $cover->file_url,
            'information' => (int) $information,
        ));

        $console = '<script>console.log("Building movie information from API, setting: '.$cache_key.'");</script>';

        $return = $v->movie_shortcode();

        set_transient($cache_key, $return, $cachetime);

        return $cover->console.$console.$return;
    }
}