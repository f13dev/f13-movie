<?php namespace F13\Movies\Views;

class Actors 
{
    public $data; 
    public $credits;

    public function __construct($params = array()) 
    {
        foreach ($params as $k => $v) {
            $this->{$k} = $v;
        }
    }

    public function actor_shortcode()
    {
        $v = '<div class="f13-movies-actor-container">';
            $v .= '<div class="f13-movies-actor-name">'.esc_attr($this->data->name).'</div>';
            $v .= '<div class="f13-movies-head">';
                $v .= '<div class="f13-movies-actor-image">';
                    $v .= '<img src="'.esc_url($this->local_image).'">';
                $v .= '</div>';

                $count = 0;
                $v .= '<div class="f13-movies-actor-credits">';
                    $v .= '<div class="f13-movies-actor-credits-box">';
                        $v .= '<span div class="f13-movies-actor-credits-title">'.__('Known for').':</span>';
                        foreach ($this->data->credits->cast as $credit) {
                            if ($count > 4) {
                                break;
                            }
                            $v .= '<div class="f13-movies-actor-credit">';
                                $v .= '<div class="f13-movies-actor-credit-title">'.esc_attr($credit->title).'</div>';
                                $v .= '<span class="f13-movies-actor-credit-date f13-movies-actor-credit-info"><strong>'.__('Release date').':</strong> '.date('F j Y', strtotime($credit->release_date)).'</span>';
                                $v .= '<span class="f13-movies-actor-credit-character f13-movies-actor-credit-info"><strong>'.__('Character').'</strong> '.esc_attr($credit->character).'</span>';
                            $v .= '</div>';
                            $count++;
                        }
                    $v .= '</div>';
                $v .= '</div>';

            $v .= '</div>';
            $v .= '<div class="f13-movies-actor-bio"><div><strong>'.__('Bio').':</strong> '.nl2br(esc_attr($this->data->biography)).'</div></div>';
            $v .= '<div class="f13-movies-actor-info">';
                if (property_exists($this->data, 'birthday') && $this->data->birthday) {
                    $v .= '<div><strong>'.__('Date of birth').':</strong> '.date('F j Y', strtotime($this->data->birthday)).'</div>';
                }
                if (property_exists($this->data, 'place_of_birth') && $this->data->place_of_birth) {
                    $v .= '<div><strong>'.__('Place of birth').':</strong> '.esc_attr($this->data->place_of_birth).'</div>';
                }
                if (property_exists($this->data, 'deathday') && $this->data->deathday) {
                    $v .= '<div><strong>'.__('Date of death').':</strong> '.date('F j Y', strtotime($this->data->deathday)).'</div>';
                }
                if (property_exists($this->data, 'known_for_department') && $this->data->known_for_department) {
                    $v .= '<div><strong>'.__('Known for').':</strong> '.$this->data->known_for_department.'</div>';
                }
                if (property_exists($this->data, 'homepage') && $this->data->homepage) {
                    $v .= '<div><strong>'.__('Website').':</strong> <a href="'.esc_attr($this->data->homepage).'" target="_blank">'.$this->data->homepage.'</a></div>';
                }
            $v .= '</div>'; 

            $v .= '<div class="f13-movies-powered-by-tmdb">';
                $v .= '<a href="https://www.themoviedb.org/" target="_blank" title="'.__('Powered by The Movie Database').'">';
                    $v .= '<img src="https://www.themoviedb.org/assets/2/v4/logos/v2/blue_long_2-9665a76b1ae401a510ec1e0ca40ddcb3b0cfe45f1d51b77a308fea0845885648.svg">';
                $v .= '</a>';
            $v .= '</div>';

        $v .= '</div>';

        return $v;
    }
}