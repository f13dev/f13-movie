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
    //'rating' => 'true', // Return rotton tomatoes rating (true, false)
  ), $atts ));

  // Set the cache name for this instance of the shortcode
  $cache = get_transient('f13movie' . md5(serialize($atts)));

  if ($cache)
  {
      // If the cache exists, return it rather than re-creating it
      return $cache;
  }
  else
  {
    // Check if a title or IMDB ID has been entered
    if ($imdb == '' && $title == '')
    {
      // Notify the user that a Title or IMDB ID is required
      $string = 'In order to use this shortcode either the \'imdb\' or \'title\' attributes must be set.<br/>
      Shortcode example:<br />
      [movie imdb=\'imdb_movie_id\'] or [movie title=\'A movie title\']';
    }
    else
    {
      // Generate the result of the shortcode
      if ($imdb != '' && $title != '')
      {
        // If both an IMDB ID and Title have been set, only use the IMDB ID
        $title = '';
      }
      // Store the search query in a variable
      $query = '';
      // Create the query string
      // Add the IMDB id if it is set
      if ($imdb != '')
      {
        $query .= 'i=' . $imdb . '&';
      }
      // Add the title if it is set
      if ($title != '')
      {
        $query .= 't=' . $title . '&';
      }
      // Add the type if it is set
      if ($type != '')
      {
        $query .= 'type=' . $type . '&';
      }
      // Add the year if it is set and is a number
      if ($year != '' && is_numeric($year))
      {
        $query .= 'y=' . $year . '&';
      }
      // If the plot attribute is set to short, set it short,
      // otherwise set it to full
      if ($plot == 'short')
      {
        $query .= 'plot=short&';
      }
      else
      {
        $query .= 'plot=full';
      }
      // If the rating is set to false, set it, otherwise
      // set it to true
      /*
      if ($rating == 'false')
      {
        $query .= 'tomatoes=false&';
      }
      else
      {
        $query .= 'tomatoes=true&'
      }
      */
      // Get the movie data and store it in a variable
      $movie_data = f13_get_movie_data($query);
      // Send the movie data to be formatted
      $string = f13_format_movie_data($movie_data);
    }
    // Return the generated string
    return $string;
  }
}

/**
 * A function to format the movie data into a widget
 * @param  array  $data An array of movie data
 * @return String       A formatted rich text string of movie data
 */
function f13_format_movie_data($data)
{
  // Create a variable to store the formatted rich text data
  $rich_text = '';
  // Open the movie container
  $rich_text .= '<div class="f13-movie-container">';
  // Check if a response was generated
  if ($data['response'] != 'True' || $data['response'] != 'true')
  {
    // If a response was not generated warn the user
    $rich_text .= '<span class="f13-movie-error">The movie, show or episode you requested could not be found.</span>';
  }
  else
  {
    /* If a response was generated build the widget */
    // If the poster exists add it
    if ($data['Poster'] != '')
    {
      $rich_text .= '<div class="f13-movie-poster" style="background:url(' . $data['Poster'] . ')"></div>';
    }
    // Open a content container
    $rich_text .= '<div class="f13-movie-content">';
      // Add the title
      $rich_text .= '<div class="f13-movie-title">' . $data['Title'] . '</div>';
      // If the year is available add it
      if ($data['Year'] != '')
      {
        $year = $data['Year'];
        // Check if the year is a range ending in '-'
        if (substru($year, -1) == '-')
        {
          $year = $year . 'present';
        }
        // Add the year
        $rich_text .= '<div class="f13-movie-year">' . $year . '</div>';
      }
      // If the release date is available add it
      if ($data['Released'] != '')
      {
        $rich_text .= '<div class="f13-movie-released">' . $data['Released'] . '</div>';
      }
      // If the response is a series, input series specific data
      if ($data['Type'] == 'series')
      {
        // Check if totalSeasons is set, if so add it
        if ($data['totalSeasons'] != '')
        {
          $rich_text .= '<div class="f13-movie-totalSeasons">' . $data['totalSeasons']'</div>';
        }
      }
      elseif ($data['Type'] == 'episode')
      {
        // Open an episode div
        $rich_text .= '<div class="f13-movie-episode">';
          // If an episode number is set add it
          if ($data['Episode'] != '')
          {
            $rich_text .= 'Episode ' . $data['Episode'] . ' ';
          }
          // If a season is set, add it
          if ($data['Season'] != '')
          {
            $rich_text .= 'Season ' . $data['Season'];
          }
        // Close the episode div
        $rich_text .= '</div>';
      }
      // If a plot is set, add it
      if ($data['Plot'] != '')
      {
        $rich_text .= '<div class="f13-movie-plot">' . $data['Plot'] . '</div>';
      }
      // If a runtime is set, add it
      if ($data['Runtime'] != '')
      {
        $rich_text .= '<div class="f13-movie-runtime">Runtime: ' . $data['Runtime'] . '</div>';
      }
      // If a genre is set, add it
      if ($data['Genre'] != '')
      {
        $rich_text .= '<div class="f13-movie-genre">' . $data['Genre'] . '</div>';
      }
      // Open a crew div
      $rich_text .= '<div class="f13-movie-crew">';
        // If a director is set, add it
        if ($data['Director'] != 'N/A')
        {
          $rich_text .= '<div class="f13-movie-director">Director: ' . $data['Director'] . '</div>';
        }
        // If a writer is set, add it
        if ($data['Writer'] != 'N/A')
        {
          $rich_text .= '<div class="f13-movie-writer">Writer: ' . $data['Writer'] . '</div>';
        }
        // If actors is set, add it
        if ($data['Actors'] != 'N/A')
        {
          $rich_text .= '<div class="f13-movie-actors">Actors: ' . $data['Actors'] . '</div>';
        }
      // Close the crew div
      $rich_text .= '</div>';
      // Create a localization div
      $rich_text .= '<div class="f13-movie-localization">';
        // If a language is set, add it
        if ($data['Language'] != 'N/A')
        {
          $rich_text .= '<div class="f13-movie-language">Language: ' . $data['Language'] . '</div>';
        }
        // If a country is set, add it
        if ($data['Country'] != 'N/A')
        {
          $rich_text .= '<div class="f13-movie-country">Country: ' . $data['Country'] . '</div>';
        }
      // Close the localization div
      $rich_text .= '</div>';
      // If awards is set, add it
      if ($data['Awards'] != 'N/A')
      {
        $rich_text .= '<div class="f13-movie-awards">' $data['Awards'] . '</div>';
      }
    // Close the content container
    $rich_text .= '</div>';
  }
  // Close the movie container
  $rich_text .= '</div>';

}

/**
 * A function to retrieve the movie information from the
 * Open Movie Databse
 * @param  $query  The query string to be appended to the url.
 * @return         A decoded array of information about the movie.
 */
 function f13_get_movie_data($query)
 {
   // Set the URL
   $url = 'http://www.omdbapi.com/?' . $query;
   // Start curl
   $curl = curl_init();
   // Set curl options
   curl_setopt($curl, CURLOPT_URL, $url);
   curl_setopt($curl, CURLOPT_HTTPGET, true);
   // Set curl headers
   curl_setopt($curl, CURLOPT_HTTPHEADER, array(
     'Content-Type: application/json',
     'Accept: application/json'
   ));
   // Set the user agent
   curl_setopt($curl, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.13) Gecko/20080311 Firefox/2.0.0.13');
   // Set curl to return the response, rather than print it
   curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
   // Get the results
   $result = curl_exec($curl);
   // Close the curl session
   curl_close($curl);
   // Decode the results
   $result = json_decode($result, true);
   // Return the results
   return $result;
 }
