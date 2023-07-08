<?php namespace F13\Movies\Models;

class TMDB 
{
    public $wpdb;
    private $api_key;
    private $tmdb_api_url;

    public function __construct()
    {
        global $wpdb;
        $this->wpdb = $wpdb;
        $settings = \F13\Movies\Controllers\Admin::_get_settings();
        $this->api_key = $settings['tmdb_api_key'];
        $this->tmdb_api_url = 'https://api.themoviedb.org';
    }

    public function _call($url)
    {
        if (stripos($this->tmdb_api_url, $url) === false) {
            $url = $this->tmdb_api_url.$url;
        }
        $args = array(
            'headers' => array(
                'Authorization' => 'Bearer '.$this->api_key,
            )
        );
        $response = wp_remote_get($url, $args);
        $body     = wp_remote_retrieve_body($response);
        
        return (object) json_decode($body);
    }

    public function retrieve_movie_data($params)
    {
        $imdb  = (array_key_exists('imdb', $params))  ? $params['imdb']  : '';
        $title = (array_key_exists('title', $params)) ? $params['title'] : '';
        $type  = (array_key_exists('type', $params))  ? $params['type']  : '';
        $year  = (array_key_exists('year', $params))  ? $params['year']  : '';

        if ($imdb) {
            return $this->retrieve_movie_data_from_imdb($imdb, $type);
        }

        $url = '/3/search/multi?query='.urlencode($title);
        $results = $this->_call($url);

        if (property_exists($results, 'success') && !$results->success && $results->status_message) {
            return new \WP_Error('f13-movie', esc_attr($results->status_message));
        }

        if ($results->results) {
            switch ($results->results[0]->media_type) {
                case 'movie':
                    $url = '/3/movie/'.$results->results[0]->id;
                    return $this->_call($url);
                case 'tv':
                    $url = '/3/tv/'.$results->results[0]->id;
                    return $this->_call($url);
            }
        }

        return new \WP_Error('f13-movie', __('No results'));
    }

    public function retrieve_movie_data_from_imdb($imdb, $type = '')
    {
        $url = '/3/find/'.$imdb.'?external_source=imdb_id';
        $results = $this->_call($url);

        switch ($type) {
            case '': case 'movie':
                if (!empty($results->movie_results)) {
                    $url = '/3/movie/'.$results->movie_results[0]->id;
                    return $this->_call($url);
                }   
            case '': case 'series':
                if (!empty($results->tv_results)) {
                    $url = '/3/tv/'.$results->tv_results[0]->id;
                    return $this->_call($url);
                }
            case '': case 'episode':
                if (!empty($results->tv_episode_results)) {
                    $ep = $results->tv_episode_results[0];
                    $url = '/3/tv/'.$ep->show_id.'/season/'.$ep->season_number.'/episode/'.$ep->episode_number;
                    return $this->_call($url);
                }

            return new \WP_Error('f13-move', __('No results'));
        }
    }

    public function get_image_id($file_name)
    {
        $sql = "SELECT post_id
                FROM ".$this->wpdb->base_prefix."postmeta
                WHERE meta_key = %s AND meta_value LIKE %s;";
        return $this->wpdb->get_var($this->wpdb->prepare($sql, $this->wpdb->base_prefix.'_attached_file', '%'.$file_name));
    }

    public function retrieve_actor_data($params)
    {
        $id = (array_key_exists('id', $params)) ? $params['id'] : '';
        $name = (array_key_exists('name', $params)) ? $params['name'] : '';

        if (!$id) {
            // Do a search
            $url = '/3/search/person?query='.urlencode($name).'&page=1&include_adult=true';
            $results = $this->_call($url);
            if (!property_exists($results, 'results') || empty($results->results)) {
                return new \WP_Error('tmdb', __('No results.'));
            }
            $id = $results->results[0]->id;
            }

        $url = '/3/person/'.$id;

        $actor = $this->_call($url);

        if (property_exists($actor, 'profile_path') && $actor->profile_path) {
            $actor->profile_path = 'https://www.themoviedb.org/t/p/w300_and_h450_bestv2'.$actor->profile_path;
        }

        if ($actor && array_key_exists('credits', $params) && $params['credits']) {
            $url = '/3/person/'.$id.'/combined_credits';
            $actor->credits = $this->_call($url);
        }

        return $actor;
    }
} 