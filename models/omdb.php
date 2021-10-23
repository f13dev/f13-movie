<?php namespace F13\Movies\Models;

class OMDB
{
    public $wpdb;
    public $api_key;
    public $omdb_api_url;

    public function __construct()
    {
        global $wpdb;
        $this->wpdb = $wpdb;
        $this->api_key = esc_attr(get_option('omdb_api_key'));
        $this->omdb_api_url = 'http://www.omdbapi.com/?';
    }

    public function _call($url)
    {
        $response = wp_remote_get($url);
        $body     = wp_remote_retrieve_body($response);

        return (object) json_decode($body);
    }

    public function retrieve_movie_data($params)
    {
        $url = $this->omdb_api_url;
        foreach ($params as $k => $v) {
            $url .= $k.'='.$v.'&';
        }

        $url .= 'apikey='.$this->api_key;

        $return = $this->_call($url);

        if (property_exists($return, 'seriesID')) {
            $series = $this->_call($this->omdb_api_url.'i='.$return->seriesID.'&apikey='.$this->api_key);
            $return->Title = $series->Title.' - '.$return->Title;
        }

        return $return;
    }

    public function get_image_id($file_name)
    {
        $sql = "SELECT post_id
                FROM ".$this->wpdb->base_prefix."postmeta
                WHERE meta_key = '_wp_attached_file' AND meta_value LIKE %s;";
        $attachment = $this->wpdb->get_var($this->wpdb->prepare($sql, '%'.$file_name));

        return $attachment;
    }
}