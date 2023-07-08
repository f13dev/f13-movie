<?php namespace F13\Movies\Views;

class Admin
{
    private $data;

    public $label_all_wordpress_plugins;
    public $label_api_key;
    public $label_by_imdb;
    public $label_by_title_year;
    public $label_copy_paste_key;
    public $label_f13_movie_settings;
    public $label_fill_in_form;
    public $label_obtain_omdb_api;
    public $label_omdb_api;
    public $label_open_movie_database;
    public $label_plugins_by_f13;
    public $label_preferred_api;
    public $label_requires_omdb_api;
    public $label_the_movie_database;
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
        $this->label_open_movie_database    = __('Open Movie Database', 'f13-movies');
        $this->label_plugins_by_f13         = __('Plugins by F13', 'f13-movies');
        $this->label_preferred_api          = __('Preferred API', 'f13-movies');
        $this->label_the_movie_database     = __('The Movie Database', 'f13-movies');
        $this->label_requires_omdb_api      = __('This plugin requires an OMDB API key to function', 'f13-movies');
        $this->label_visit                  = __('Visit', 'f13-movies');
        $this->label_welcome                = __('Welcome to the settings page for F13\'s Movies Shortcode.', 'f13-movies');
    }

     public function f13_settings()
    {
        $v = '<div class="wrap">';
            $v .= '<h1>'.$this->label_plugins_by_f13.'</h1>';
            foreach ($this->data->results as $item) {
                $v .= '<div class="plugin-card plugin-card-f13-toc" style="margin-left: 0; width: 100%;">';
                    $v .= '<div class="plugin-card-top">';
                        $v .= '<div class="name column-name">';
                            $v .= '<h3>';
                                $v .= '<a href="plugin-install.php?s='.urlencode('"'.$item->search_term.'"').'&tab=search&type=term" class="thickbox open-plugin-details-modal">';
                                    $v .= $item->title;
                                    $v .= '<img src="'.$item->image.'" class="plugin-icon" alt="">';
                                $v .= '</a>';
                            $v .= '</h3>';
                        $v .= '</div>';
                        $v .= '<div class="desc column-description">';
                            $v .= '<p>';
                                $v .= $item->description;
                            $v .= '</p>';
                            $v .= '.<p class="authors">';
                                $v .= ' <cite>By <a href="'.$item->url.'">Jim Valentine - f13dev</a></cite>';
                            $v .= '</p>';
                        $v .= '</div>';
                    $v .= '</div>';
                $v .= '</div>';
            }
        $v .= '<div>';

        return $v;
    }

    public function movies_settings()
    {
        $v = '<div class="wrap">';
            $v .= '<h1>'.$this->label_f13_movie_settings.'</h1>';
            $v .= '<p>'.__('F13 Movie is able to utilise two free API\'s, "OMDB" and "TMDB". Movie data can be retrieved from either, actor data can only be retrieved from "TMDB"').'</p>';
            $v .= '<p>'.sprintf(__('For shortcode examples, visit the %s page.'), '<a href="https://f13.dev/wordpress-plugin-movie-shortcode/" target="_blank">'.__('F13 Movie').'</a>').'</p>';

            $v .= '<form method="post" action="options.php">';
                $v .= '<input type="hidden" name="option_page" value="'.esc_attr('f13-movies-settings-group').'">';
                $v .= '<input type="hidden" name="action" value="update">';
                $v .= '<input type="hidden" id="_wpnonce" name="_wpnonce" value="'.wp_create_nonce('f13-movies-settings-group-options').'">';
                do_settings_sections('f13-movies-settings-group');
                settings_errors();
                $v .= '<table class="form-table">';
                $v .= '<tr valign="top">';
                    $v .= '<th scope="row">'.$this->label_preferred_api.'</th>';
                    $v .= '<td>';
                        $preferred_option = get_option('f13_movie_preferred_api');
                        $v .= '<p>';
                            $v .= '<input type="radio" id="f13_movie_preferred_api_omdb" name="f13_movie_preferred_api" value="omdb" '.($preferred_option == 'omdb' || !$preferred_option ? 'checked="checked"' : '').'>';
                            $v .= '<label for="f13_movie_preferred_api_omdb">'.$this->label_open_movie_database.'</label>';
                        $v .= '</p>';
                        $v .= '<p>';
                            $v .= '<input type="radio" id="f13_movie_preferred_api_tmdb" name="f13_movie_preferred_api" value="tmdb" '.($preferred_option == 'tmdb' ? 'checked="checked"' : '').'>';
                            $v .= '<label for="f13_movie_preferred_api_tmdb">'.$this->label_the_movie_database.'</label>';
                        $v .= '</p>';
                    $v .= '</td>';
                $v .= '</tr>';

                $v .= '<tr valign="top">';
                        $v .= '<td colspan="2">';
                            $v .= '<h2>'.$this->label_open_movie_database.'</h2>';
                            $v .= '<strong>'.$this->label_obtain_omdb_api.':</strong>';
                            $v .= '<ol>';
                                $v .= '<li>'.$this->label_visit.': <a href="http://www.omdbapi.com/apikey.aspx" title="'.$this->label_omdb_api.'" target="_blank">http://www.omdbapi.com/apikey.aspx</a></li>';
                                $v .= '<li>'.$this->label_fill_in_form.'</li>';
                                $v .= '<li>'.$this->label_copy_paste_key.'</li>';
                            $v .= '</ol>';
                        $v .= '</td>';
                    $v .= '</tr>';
                    $v .= '<tr valign="top">';
                        $v .= '<th scope="row">'.$this->label_api_key.'</th>';
                        $v .= '<td>';
                            $v .= '<input type="password" name="omdb_api_key" value="'.esc_attr(get_option('omdb_api_key')).'" style="width: 50%">';
                        $v .= '</td>';
                    $v .= '</tr>';

                    $v .= '<tr valign="top">';
                        $v .= '<td colspan="2">';
                            $v .= '<h2>'.$this->label_the_movie_database.'</h2>';
                            $v .= '<strong>'.__('To obtain a TMDB API key:').'</strong>';
                            $v .= '<ol>';
                                $v .= '<li>'.__('Sign in / Register for an account at <a href="https://www.themoviedb.org/signup" target="_blank">The Movie Database</a>').'</li>';
                                $v .= '<li>'.__('Complete the form to apply for an <a href="https://www.themoviedb.org/settings/api" target="_blank">api key</a>').'</li>';
                                $v .= '<li>'.$this->label_copy_paste_key.'</li>';
                            $v .= '</ol>';
                        $v .= '</td>';
                    $v .= '</tr>';
                    $v .= '<tr valign="top">';
                        $v .= '<th scope="row">'.$this->label_api_key.'</th>';
                        $v .= '<td>';
                            $v .= '<input type="password" name="tmdb_api_key" value="'.esc_attr(get_option('tmdb_api_key')).'" style="width: 50%;">';
                        $v .= '</td>';
                    $v .= '</tr>';


                $v .= '</table>';
                $v .= '<p class="submit"><input type="submit" name="submit" id="submit" class="button button-primary" value="'.__('Save Changes', 'f13-movies').'"></p>';
            $v .= '</form>';
        $v .= '</div>';

        return $v;
    }
}