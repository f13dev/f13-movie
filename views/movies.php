<?php namespace F13\Movies\Views;

class Movies
{
    public $label_actors;
    public $label_awards;
    public $label_country;
    public $label_data_provided_by;
    public $label_director;
    public $label_episode;
    public $label_genre;
    public $label_information;
    public $label_language;
    public $label_omdb_api;
    public $label_omdb_api_title;
    public $label_plot;
    public $label_runtime;
    public $label_season;
    public $label_seasons;
    public $lable_toggle_information;
    public $label_writer;

    public function __construct($params = array())
    {
        foreach ($params as $k => $v) {
            $this->{$k} = $v;
        }

        $this->label_actors                 = __('Actors', 'f13-movies');
        $this->label_awards                 = __('Awards', 'f13-movies');
        $this->label_country                = __('Country', 'f13-movies');
        $this->label_data_provided_by       = __('Data provided by', 'f13-movies');
        $this->label_director               = __('Director', 'f13-movies');
        $this->label_episode                = __('Episode', 'f13-movies');
        $this->label_genre                  = __('Genre', 'f13-movies');
        $this->label_information            = __('Information', 'f13-movies');
        $this->label_language               = __('Language', 'f13-movies');
        $this->label_omdb_api               = __('OMDB API', 'f13-movies');
        $this->label_omdb_api_title         = __('Open Movie Database API', 'f13-movies');
        $this->label_plot                   = __('Plot', 'f13-movies');
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
                $v .= '<div class="f13-movies-title" role="heading">'.$this->data->Title.'</div>';
            }
            $v .= '<div class="f13-movies-head">';
                if (!in_array('image', $this->disable)) {
                    $v .= '<div class="f13-movies-poster">';
                        //$v .= '<a href="'.str_replace('SX300', 'SX1200', $this->data->Poster).'">';
                            $v .= '<img src="'.str_replace('SX300', 'SX'.$this->image_size, $this->data->Poster).'" role="presentation" alt="" aria-label="Poster: '.$this->data->Title.'" loading="lazy">';
                            //$v .= __('Enlarge movie poster');
                        //$v .= '</a>';
                    $v .= '</div>';
                }
                if (!in_array('plot', $this->disable)) {
                    $v .= '<div class="f13-movies-plot">';
                        $v .= '<div>';
                            $v .= '<strong>'.$this->label_plot.':</strong> ';
                            $v .= $this->data->Plot;
                        $v .= '</div>';
                    $v .= '</div>';
                }
            $v .= '</div>';

            if (!in_array('rating', $this->disable)) {
                $v .= '<div class="f13-movies-ratings">';
                    foreach ($this->data->Ratings as $rating) {
                        $v .= '<div class="f13-movies-rating">';
                            $v .= '<strong>'.$rating->Source.':</strong> '.$rating->Value;
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
                            $v .= '<strong>'.$this->label_runtime.':</strong> '.$this->data->Runtime;
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

                        $v .= '<div class="f13-movies-stat">';
                            $v .= '<strong>'.$this->label_genre.':</strong> '.$this->data->Genre;
                        $v .= '</div>';
                        $v .= '<div class="f13-movies-stat">';
                            $v .= '<strong>'.$this->label_awards.':</strong> '.$this->data->Awards;
                        $v .= '</div>';
                        $v .= '<div class="f13-movies-stat">';
                            $v .= '<strong>'.$this->label_director.':</strong> '.$this->data->Director;
                        $v .= '</div>';
                        $v .= '<div class="f13-movies-stat">';
                            $v .= '<strong>'.$this->label_writer.':</strong> '.$this->data->Writer;
                        $v .= '</div>';
                        $v .= '<div class="f13-movies-stat">';
                            $v .= '<strong>'.$this->label_actors.':</strong> '.$this->data->Actors;
                        $v .= '</div>';
                        $v .= '<div class="f13-movies-stat">';
                            $v .= '<strong>'.$this->label_language.':</strong> '.$this->data->Language;
                        $v .= '</div>';
                        $v .= '<div class="f13-movies-stat">';
                            $v .= '<strong>'.$this->label_country.':</strong> '.$this->data->Country;
                        $v .= '</div>';
                    $v .= '</div>';
                $v .= '</details>';
            }

            $v .= '<div class="f13-movies-notice">';
                $v .= $this->label_data_provided_by.' <a href="http://omdbapi.com/" title="'.$this->label_omdb_api_title.'" target="_blank">'.$this->label_omdb_api.'</a>';
            $v .= '</div>';
        $v .= '</div>';

        return $v;
    }
}