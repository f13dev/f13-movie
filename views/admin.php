<?php namespace F13\Movies\Views;

class Admin
{
    public $label_all_wordpress_plugins;
    public $label_api_key;
    public $label_by_imdb;
    public $label_by_title_year;
    public $label_copy_paste_key;
    public $label_f13_movie_settings;
    public $label_fill_in_form;
    public $label_obtain_omdb_api;
    public $label_omdb_api;
    public $label_plugins_by_f13;
    public $label_requries_omdb_api;
    public $label_visit;
    public $label_welcome;

    public function __construct($params = array())
    {
        foreach ($params as $k => $v) {
            $this->{$k} = $v;
        }

        $this->label_all_wordpress_plugins  = __('All WordPress plugins', 'f13-movies');
        $this->label_api_key                = __('API Key', 'f13-google-maps');
        $this->label_by_imdb                = __('Adding a movie by the IMDB ID', 'f13-movies');
        $this->label_by_title_year          = __('Adding a movie by title and year', 'f13-movies');
        $this->label_copy_paste_key         = __('Copy and paste your API Key to the field below.', 'f13-movies');
        $this->label_f13_movie_settings     = __('F13 Movie Settings', 'f13-movies');
        $this->label_fill_in_form           = __('Fill in the form to apply for a FREE API key', 'f13-movies');
        $this->label_obtain_omdb_api        = __('To obtain an OMDB API key', 'f13-movies');
        $this->label_omdb_api               = __('OMDB API', 'f13-movies');
        $this->label_plugins_by_f13         = __('Plugins by F13', 'f13-movies');
        $this->label_requires_omdb_api      = __('This plugin requires an OMDB API key to function', 'f13-movies');
        $this->label_visit                  = __('Visit', 'f13-movies');
        $this->label_welcome                = __('Welcome to the settings page for F13\'s Movies Shortcode.', 'f13-movies');
    }

     public function f13_settings()
    {
        $response = wp_remote_get('https://pluginlist.f13.dev');
        $body     = wp_remote_retrieve_body( $response );
        $v = '<div class="wrap">';
            $v .= '<h1>'.$this->label_plugins_by_f13.'</h1>';
            $v .= '<div id="f13-plugins">'.$body.'</div>';
            $v .= '<a href="'.admin_url('plugin-install.php').'?s=f13dev&tab=search&type=author">'.$this->label_all_wordpress_plugins.'</a>';
        $v .= '</div>';

        return $v;
    }

    public function movies_settings()
    {
        $v = '<div class="wrap">';
            $v .= '<h1>'.$this->label_f13_movie_settings.'</h1>';
            $v .= '<p>'.$this->label_welcome.'</p>';
            $v .= '<p>'.$this->label_requires_omdb_api.'</p>';
            $v .= '<h3>'.$this->label_obtain_omdb_api.':</h3>';
            $v .= '<ol>';
                $v .= '<li>'.$this->label_visit.': <a href="http://www.omdbapi.com/apikey.aspx" title="'.$this->label_omdb_api.'" target="_blank">http://www.omdbapi.com/apikey.aspx</a></li>';
                $v .= '<li>'.$this->label_fill_in_form.'</li>';
                $v .= '<li>'.$this->label_copy_paste_key.'</li>';
            $v .= '</ol>';

            $v .= '<form method="post" action="options.php">';
                $v .= '<input type="hidden" name="option_page" value="'.esc_attr('f13-movies-settings-group').'">';
                $v .= '<input type="hidden" name="action" value="update">';
                $v .= '<input type="hidden" id="_wpnonce" name="_wpnonce" value="'.wp_create_nonce('f13-movies-settings-group-options').'">';
                do_settings_sections('f13-movies-settings-group');
                $v .= '<table class="form-table">';
                    $v .= '<tr valign="top">';
                        $v .= '<th scope="row">'.$this->label_api_key.'</th>';
                        $v .= '<td>';
                            $v .= '<input type="password" name="omdb_api_key" value="'.esc_attr(get_option('omdb_api_key')).'" style="width: 50%">';
                        $v .= '</td>';
                    $v .= '</tr>';
                $v .= '</table>';
                $v .= '<p class="submit"><input type="submit" name="submit" id="submit" class="button button-primary" value="'.__('Save Changes', 'f13-movies').'"></p>';
            $v .= '</form>';

            $v .= '<h3>Shortcode example</h3>';
            $v .= '<p>'.$this->label_by_title_year.':<br>[movie title="The Blair Witch Project" year="1999"]</p>';
            $v .= '<p>'.$this->label_by_imdb.':<br>[movie imdb="tt2123146"]</p>';
        $v .= '</div>';

        return $v;
    }
}