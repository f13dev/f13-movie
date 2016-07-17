<?php
/*
Plugin Name: F13 Movie Embed Shortcode
Plugin URI: http://f13dev.com/wordpress-plugin-movie-embed-shortcode/
Description: Embed information about a movie or TV show into a WordPress blog post or page using shortcode.
Version: 1.0
Author: Jim Valentine - f13dev
Author URI: http://f13dev.com
Text Domain: f13-movie-embed-shortcode
License: GPLv3
*/

/*
Copyright 2016 James Valentine - f13dev (jv@f13dev.com)
This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 3 of the License, or
any later version.
This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
GNU General Public License for more details.
You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA 02110-1301 USA
*/

// Register the shortcode
add_shortcode( 'movie', 'f13_movie_shortcode');
// Register the css
add_action( 'wp_enqueue_scripts', 'f13_movie_shortcode_style');

/**
 * A function to register the stylesheet
 * @return [type] [description]
 */
function f13_movie_shortcode_style()
{
    wp_register_style( 'f13movie-style', plugins_url('movie-shortcode.css', __FILE__) );
    wp_enqueue_style( 'f13movie-style' );
}

/**
* Function to handle the shortcode
* @param  Array  $atts    The attributes set in the shortcode
* @param  [type] $content [description]
* @return String          The response of the shortcode
*/
function f13_movie_shortcode( $atts, $content = null )
{
    // Get the attributes
    extract( shortcode_atts ( array (
    'imdb' => '', // The IMDB movie ID
    'title' => '', // The title of the movie
    'type' => '', // The type (movie, series, episode)
    'year' => '', // The year of the movie
    'plot', => 'full', // Return full or short plot
    'rating' => 'true', // Return rotton tomatoes rating (true, false)
  ), $atts ));

}
