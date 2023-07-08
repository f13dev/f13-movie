=== Plugin Name ===
Contributors: f13dev
Donate link: http://f13dev.com/wordpress-plugin-movie-shortcode/
Tags: movie, tv show, episode, embed, shortcode, imdb, omdb
Requires at least: 3.0.1
Tested up to: 6.2
Stable tag: 2.1.2
License: GPLv3 or later
License URI: http://www.gnu.org/licenses/gpl-3.0.html

Do you blog about movies? It can be tedious adding movie information manually, now you can add movie information with shortcode using an IMDB ID.

== Description ==

Using Movie Shortcode you can easily add information about a movie, tv show, episode or actor to a blog post.

Simply enter the shortcode: [movie imdb="an IMDB ID"] or [movie title="A Movie Title"] to a blog post to add an array of information about the movie, tv show or episode; or enter the shortcode [actor tmdb="a TMDB person ID"] or [actor name="An actors name"] to a blog post to add an array of information about the actor.

=== Movie ===
Optional attributes:

* cachetime="A time in minutes" - default is 1440 (24 hours)
* plot="short|full" - default is full
* disable="title,image,plot,information,rating" - comma separated list of sections to remove
* image_size="1200" - maximum image size in pixels
* trailer="youtube video ID" - a youtube video ID for the movie trailer

If adding the shortcode with a title, rather than an IMDB ID, the following attributes are also optional to find the correct entry:

* type="movie|series|episode"
* year="The year of the movie"

=== Actor ===
Optional attributes (actor):

* cachetime="A time in minutes" - default is 1440 (24 hours)

=== Features ===

* Styled appearance
* Shows film title, year, release date
* If showing a series, the total number of seasons is displayed
* If showing an episode, the season and episode number are displayed
* Displays the plot or synopsis
* Displays a poster image if one is available
* If a poster image is available it is added to your media library and loaded locally
* Displays movie stats such as the runtime, genre, awards, director, writer, Actors
* Displays the available languages and country of origin
* Provides the IMDB rating, including a star rating image
* Adds a link to the movie on IMDB
* Utilizes transient caching to reduce page load times and api calls
* Disable widget blocks to customize the appearance to your needs

== Installation ==

1. Upload the plugin files to the `/wp-content/plugins/plugin-name` directory, or install the plugin through the WordPress plugins screen directly.
2. Activate the plugin through the 'Plugins' screen in WordPress
3. Add the shortcode [movie imdb="an IMDB ID"], or [movie title="A movie title"] to the desired location on your blog

== Frequently Asked Questions ==

= How do I find an IMDB ID =

When viewing a movie, tv show or episode on IMDB.com, the ID is the string starting with 'tt', usually in the format www.imdb.com/title/imdbid/

= How do I adjust the cache time =

Add the attribute cachetime="a time in minutes" to your shortcode

= Using the shortcode with a title returns the wrong movie =

Try adding the optional attributes, type="movie|series|episode" and year="a year" to refine your search result

= How do I hide the plot and information

Include the optional "disable" attribute, disable="plot,information"

== Screenshots ==

1. The results of adding [movie imdb="tt0480249"] to a blog post.

== Changelog ==
= 2.1.3 =
* Fix instructions for TMDB API, Read Access Token is required, not API Key

= 2.1.2 =
* Improved error handling for TMDB API, if API returns an error, this is presented to the user
* Notify user of success / error when saving admin settings
* Fix: setting cachetime=0 in shortcode forces API call on every page load

= 2.1.1 =
* Adding alt text to images
* Setting TMDB logo as no-lightbox
* Removing console logs if F13_MOVIE_DEV is false

= 2.1.0 =
* Adding "image_size" attribute to movie shortcode
* Adding "trailer" attribute to movie shortcode
* Adding secondary API via "The Movie Database"
* Adding "actor" shortcode

= 2.0.4 =
* Adding "title" to disable fields

= 2.0.3 =
* CSS box sizing, stopping plot overflowing into other elements

= 2.0.2 =
* Optional attribute to disable blocks from the widget

= 1.0 =
* Initial release

== Upgrade Notice ==

= 1.0 =
Initial release

== Arbitrary section ==
