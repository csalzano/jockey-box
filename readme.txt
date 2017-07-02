=== Jockey Box ===
Contributors: salzano
Tags: beer, beer fest, craft beer, festival, brewery
Requires at least: 3.0.1
Tested up to: 4.8
Stable tag: 2.0.0
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Instant craft beer festival support for breweries, sponsors, and food vendors

== Description ==

This plugin makes running a craft beer festival website easy with WordPress. Maintain lists of breweries, sponsors, and food vendors in the WordPress dashboard. Each of these entitites can be categorized by year, so displaying a list or photo grid of this year's breweries is as easy as listing any other year.

This shortcode creates a grid of featured images attached to your attendees:

[jockey_box_grid object="brewery" years="2017"]

...and works just as easily like so...

[jockey_box_grid object="sponsor" years="2016,2015"]


== TODO ==

 * Refactor your custom post type args arrays during their creation
 * Create a repo for this, get it on github
 * Formalize the dependency on Advanced Custom Fields & write a spin-up component for those fields
 * Add menu links to the admin bar


== Changelog ==

= 2.0.0 =
* Introduces a custom taxonomy for years attended, allowing both management of who is coming this year, and an archive that preserves the history
* Provides a shortcode to display featured images in a grid via [jockey_box_grid object="brewery" years="2017"]
* Provides a shortcode to display a single sponsor logo via [jockey_box_sponsor_logo id="4444"] where 4444 is the sponsor's post ID

= 1.0.0 =
* Version 1 pre-dates this readme.txt file and only provided the custom post types


== History ==

I built this plugin to make my life easier while operating the Lititz Craft Beer Festival website.