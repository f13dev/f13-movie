<?php namespace F13\Movies\Views;

class Movies
{
    private $api;
    private $data;
    private $disable;
    private $image_size;
    private $information;
    private $local_image;
    private $trailer;

    public $label_actors;
    public $label_awards;
    public $label_budget;
    public $label_country;
    public $label_data_provided_by;
    public $label_director;
    public $label_episode;
    public $label_genre;
    public $label_homepage;
    public $label_information;
    public $label_language;
    public $label_omdb_api;
    public $label_omdb_api_title;
    public $label_plot;
    public $label_powered_by_tmdb;
    public $label_release_date;
    public $label_revenue;
    public $label_runtime;
    public $label_season;
    public $label_seasons;
    public $label_toggle_information;
    public $label_writer;

    public function __construct($params = array())
    {
        foreach ($params as $k => $v) {
            $this->{$k} = $v;
        }

        $this->label_actors                 = __('Actors', 'f13-movies');
        $this->label_awards                 = __('Awards', 'f13-movies');
        $this->label_budget                 = __('Budget', 'f13-movies');
        $this->label_country                = __('Country', 'f13-movies');
        $this->label_data_provided_by       = __('Data provided by', 'f13-movies');
        $this->label_director               = __('Director', 'f13-movies');
        $this->label_episode                = __('Episode', 'f13-movies');
        $this->label_genre                  = __('Genre', 'f13-movies');
        $this->label_homepage               = __('Homepage', 'f13-movies');
        $this->label_information            = __('Information', 'f13-movies');
        $this->label_language               = __('Language', 'f13-movies');
        $this->label_omdb_api               = __('OMDB API', 'f13-movies');
        $this->label_omdb_api_title         = __('Open Movie Database API', 'f13-movies');
        $this->label_plot                   = __('Plot', 'f13-movies');
        $this->label_powered_by_tmdb        = __('Powered by The Movie Database', 'f13-movies');
        $this->label_release_date           = __('Release date', 'f13-movies');
        $this->label_revenue                = __('Revenue', 'f13-movies');
        $this->label_season                 = __('Season', 'f13-movies');
        $this->label_seasons                = __('Seasons', 'f13-movies');
        $this->label_runtime                = __('Runtime', 'f13-movies');
        $this->label_toggle_information     = __('Toggle information', 'f13-movies');
        $this->label_writer                 = __('Writer', 'f13-movies');
    }

    public function _get_stars($rating = 0)
    {
        if (substr($rating, -1) == '%') {
            $rating = (int) str_replace('%', '', $rating) / 10;
        } else
        if (substr($rating, - 4) == '/100') {
            $rating = (int) str_replace('/100', '', $rating) / 10;
        } else
        if (substr($rating, - 3) == '/10') {
            $rating = (int) str_replace('/10', '', $rating);
        }

        $v = '';
        for ($i = 1; $i <= 10; $i++) {
            if ($rating > $i) {
                $v .= '<span class="dashicons dashicons-star-filled"></span>';
            } elseif ($rating > $i - 0.5) {
                $v .= '<span class="dashicons dashicons-star-half"></span>';
            } else {
                $v .= '<span class="dashicons dashicons-star-empty"></span>';
            }
        }

        return $v;
    }

    public function movie_shortcode()
    {
        $v = '<div class="f13-movies-container">';
            if (!in_array('title', $this->disable)) {
                $v .= '<div class="f13-movies-title" role="heading">'.esc_attr(($this->api == 'tmdb') ? $this->data->title : $this->data->Title).'</div>';
            }
            $v .= '<div class="f13-movies-head">';
                if (!in_array('image', $this->disable)) {
                    $v .= '<div class="f13-movies-poster">';
                        $v .= '<img src="'.esc_url(($this->local_image) ? $this->local_image : (($this->api == 'tmdb') ? $this->data->poster_path : str_replace('SX300', 'SX'.$this->image_size, $this->data->Poster))).'" role="presentation" alt="" aria-label="Poster: '.esc_attr(($this->api == 'tmdb') ? $this->data->title : $this->data->Title).'" loading="lazy">';
                    $v .= '</div>';
                }
                if (!in_array('plot', $this->disable)) {
                    $v .= '<div class="f13-movies-plot">';
                        $v .= '<div>';
                            $v .= '<strong>'.$this->label_plot.':</strong> ';
                            $v .= ($this->api == 'tmdb') ? $this->data->overview : $this->data->Plot;
                        $v .= '</div>';
                    $v .= '</div>';
                }
            $v .= '</div>';

            if (!in_array('rating', $this->disable)) {
                if ($this->api == 'tmdb') {
                    $this->data->Ratings = array(
                        (object) array(
                            'Source' => 'The Movie DB',
                            'Value' => $this->data->vote_average.'/10',
                        )
                    );
                }
                $v .= '<div class="f13-movies-ratings">';
                    foreach ($this->data->Ratings as $rating) {
                        $v .= '<div class="f13-movies-rating">';
                            $v .= '<strong>'.esc_attr($rating->Source).':</strong> '.esc_attr($rating->Value);
                        $v .= '</div>';
                        $v .= '<div class="f13-movies-rating f13-movie-stars">';
                            $v .= $this->_get_stars($rating->Value);
                        $v .= '</div>';
                    }
                $v .= '</div>';
            }

            if (!empty($this->trailer)) {
                $v .= '<div class="f13-movies-trailer">';
                    $v .= '<iframe src="https://www.youtube.com/embed/'.esc_attr($this->trailer).'" title="YouTube video player" frameborder="0" allow="accelerometer;" allowfullscreen></iframe>';
                $v .= '</div>';
            }

            if (!in_array('information', $this->disable)) {
                $v .= '<details class="f13-movies-stats" '.($this->information ? 'open' : '').'>';
                    $v .= '<summary tabindex="0" title="'.$this->label_toggle_information.'"><a tabindex="-1" href="#" role="button">'.$this->label_information.'</a></summary>';
                    $v .= '<div class="f13-movies-stats-inner">';
                        $v .= '<div class="f13-movies-stat">';
                            $v .= '<strong>'.$this->label_runtime.':</strong> '.($this->api == 'tmdb' ? $this->data->runtime.' min' : $this->data->Runtime);
                        $v .= '</div>';
                        if (property_exists($this->data, 'totalSeasons')) {
                            $v .= '<div class="f13-movies-stat">';
                                $v .= '<strong>'.$this->label_seasons.':</strong> '.$this->data->totalSeasons;
                            $v .= '</div>';
                        }
                        if (property_exists($this->data, 'Season')) {
                            $v .= '<div class="f13-movies-stat">';
                                $v .= '<strong>'.$this->label_season.':</strong> '.$this->data->Season;
                            $v .= '</div>';
                        }
                        if (property_exists($this->data, 'Episode')) {
                            $v .= '<div class="f13-movies-stat">';
                                $v .= '<strong>'.$this->label_episode.':</strong> '.$this->data->Episode;
                            $v .= '</div>';
                        }
                        if (property_exists($this->data, 'genres')) {
                            $genre = '';
                            foreach ($this->data->genres as $g) {
                                $genre .= $g->name.', ';
                            }
                            $this->data->Genre = rtrim($genre, ', ');
                        }
                        $v .= '<div class="f13-movies-stat">';
                            $v .= '<strong>'.$this->label_genre.':</strong> '.$this->data->Genre;
                        $v .= '</div>';
                        if (property_exists($this->data, 'Awards')) {
                            $v .= '<div class="f13-movies-stat">';
                                $v .= '<strong>'.$this->label_awards.':</strong> '.$this->data->Awards;
                            $v .= '</div>';
                        }
                        if (property_exists($this->data, 'Directors')) {
                            $v .= '<div class="f13-movies-stat">';
                                $v .= '<strong>'.$this->label_director.':</strong> '.$this->data->Director;
                            $v .= '</div>';
                        }
                        if (property_exists($this->data, 'Writer')) {
                            $v .= '<div class="f13-movies-stat">';
                                $v .= '<strong>'.$this->label_writer.':</strong> '.$this->data->Writer;
                            $v .= '</div>';
                        }
                        if (property_exists($this->data, 'Actors')) {
                            $v .= '<div class="f13-movies-stat">';
                                $v .= '<strong>'.$this->label_actors.':</strong> '.$this->data->Actors;
                            $v .= '</div>';
                        }
                        if (property_exists($this->data, 'spoken_languages')) {
                            $lang = '';
                            foreach ($this->data->spoken_languages as $l) {
                                $lang .= $l->english_name.', ';
                            }
                            $this->data->Language = rtrim($lang, ', ');
                        }
                        $v .= '<div class="f13-movies-stat">';
                            $v .= '<strong>'.$this->label_language.':</strong> '.$this->data->Language;
                        $v .= '</div>';
                        if (property_exists($this->data, 'production_countries')) {
                            $country = '';
                            foreach ($this->data->production_countries as $c) {
                                $country .= $c->name.', ';
                            }
                            $this->data->Country = rtrim($country, ', ');
                        }
                        $v .= '<div class="f13-movies-stat">';
                            $v .= '<strong>'.$this->label_country.':</strong> '.$this->data->Country;
                        $v .= '</div>';


                        if (property_exists($this->data, 'budget')) {
                            $v .= '<div class="f13-movies-stat">';
                                $v .= '<strong>'.$this->label_budget.':</strong> $'.number_format($this->data->budget);
                            $v .= '</div>';
                        }
                        if (property_exists($this->data, 'revenue')) {
                            $v .= '<div class="f13-movies-stat">';
                                $v .= '<strong>'.$this->label_revenue.':</strong> $'.number_format($this->data->revenue);
                            $v .= '</div>';
                        }
                        if (property_exists($this->data, 'homepage')) {
                            $v .= '<div class="f13-movies-stat">';
                                $v .= '<strong>'.$this->label_homepage.':</strong> <a href="'.esc_url($this->data->homepage).'" target="_blank">'.esc_url($this->data->homepage).'</a>';
                            $v .= '</div>';
                        }
                        if (property_exists($this->data, 'release_date') && $this->data->release_date) {
                            $v .= '<div class="f13-movies-stat">';
                                $v .= '<strong>'.$this->label_release_date.':</strong> '.date('F j Y', strtotime($this->data->release_date));
                            $v .= '</div>';
                        }
                    $v .= '</div>';
                $v .= '</details>';
            }

            if ($this->api == 'tmdb') {
                $v .= '<div class="f13-movies-powered-by-tmdb">';
                    $v .= '<a href="https://www.themoviedb.org/" target="_blank" title="'.$this->label_powered_by_tmdb.'">';
                        $v .= '<img src="https://www.themoviedb.org/assets/2/v4/logos/v2/blue_long_2-9665a76b1ae401a510ec1e0ca40ddcb3b0cfe45f1d51b77a308fea0845885648.svg">';
                    $v .= '</a>';
                $v .= '</div>';
            } else {
                $v .= '<div class="f13-movies-notice">';
                    $v .= $this->label_data_provided_by.' <a href="http://omdbapi.com/" title="'.$this->label_omdb_api_title.'" target="_blank">'.$this->label_omdb_api.'</a>';
                $v .= '</div>';
            }
        $v .= '</div>';

        return $v;
    }
}