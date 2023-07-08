<?php namespace F13\Movies\Views;

class Actors 
{
    private $data; 
    private $local_image;

    public $label_bio;
    public $label_character;
    public $label_dob;
    public $label_dod;
    public $label_homepage;
    public $label_known_for;
    public $label_place_of_birth;
    public $label_powered_by_tmdb;
    public $label_release_date;

    public function __construct($params = array()) 
    {
        $this->label_bio                = __('Bio', 'f13-movies');
        $this->label_character          = __('Character', 'f13-movies');
        $this->label_dob                = __('Date of birth', 'f13-movies');
        $this->label_dod                = __('Date of death', 'f13-movies');
        $this->label_homepage           = __('Homepage', 'f13-movies');
        $this->label_known_for          = __('Known for', 'f13-movies');
        $this->label_place_of_birth     = __('Place of birth', 'f13-movies');
        $this->label_powered_by_tmdb    = __('Powered by The Movie Database', 'f13-movies');
        $this->label_release_date       = __('Release date', 'f13-movies');

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
                    $v .= '<img src="'.esc_url($this->local_image).'" role="presentation" alt="'.esc_attr($this->data->name).'" aria-label="Actor: '.esc_attr($this->data->name).'" loading="lazy">';
                $v .= '</div>';

                $count = 0;
                $v .= '<div class="f13-movies-actor-credits">';
                    $v .= '<div class="f13-movies-actor-credits-box">';
                        $v .= '<span div class="f13-movies-actor-credits-title">'.$this->label_known_for.':</span>';
                        foreach ($this->data->credits->cast as $credit) {
                            if ($count > 4) {
                                break;
                            }
                            $v .= '<div class="f13-movies-actor-credit">';
                                $v .= '<div class="f13-movies-actor-credit-title">'.esc_attr($credit->title).'</div>';
                                $v .= '<span class="f13-movies-actor-credit-date f13-movies-actor-credit-info"><strong>'.$this->label_release_date.':</strong> '.date('F j Y', strtotime($credit->release_date)).'</span>';
                                $v .= '<span class="f13-movies-actor-credit-character f13-movies-actor-credit-info"><strong>'.$this->label_character.'</strong> '.esc_attr($credit->character).'</span>';
                            $v .= '</div>';
                            $count++;
                        }
                    $v .= '</div>';
                $v .= '</div>';

            $v .= '</div>';
            $v .= '<div class="f13-movies-actor-bio"><div><strong>'.$this->label_bio.':</strong> '.nl2br(esc_attr($this->data->biography)).'</div></div>';
            $v .= '<div class="f13-movies-actor-info">';
                if (property_exists($this->data, 'birthday') && $this->data->birthday) {
                    $v .= '<div><strong>'.$this->label_dob.':</strong> '.date('F j Y', strtotime($this->data->birthday)).'</div>';
                }
                if (property_exists($this->data, 'place_of_birth') && $this->data->place_of_birth) {
                    $v .= '<div><strong>'.$this->label_place_of_birth.':</strong> '.esc_attr($this->data->place_of_birth).'</div>';
                }
                if (property_exists($this->data, 'deathday') && $this->data->deathday) {
                    $v .= '<div><strong>'.$this->label_dod.':</strong> '.date('F j Y', strtotime($this->data->deathday)).'</div>';
                }
                if (property_exists($this->data, 'known_for_department') && $this->data->known_for_department) {
                    $v .= '<div><strong>'.$this->label_known_for.':</strong> '.$this->data->known_for_department.'</div>';
                }
                if (property_exists($this->data, 'homepage') && $this->data->homepage) {
                    $v .= '<div><strong>'.$this->label_homepage.':</strong> <a href="'.esc_attr($this->data->homepage).'" target="_blank">'.$this->data->homepage.'</a></div>';
                }
            $v .= '</div>'; 

            $v .= '<div class="f13-movies-powered-by-tmdb">';
                $v .= '<a href="https://www.themoviedb.org/" target="_blank" title="'.$this->label_powered_by_tmdb.'">';
                    $v .= '<img class="no-lightbox" alt="The Movie Database" src="https://www.themoviedb.org/assets/2/v4/logos/v2/blue_long_2-9665a76b1ae401a510ec1e0ca40ddcb3b0cfe45f1d51b77a308fea0845885648.svg">';
                $v .= '</a>';
            $v .= '</div>';

        $v .= '</div>';

        return $v;
    }
}