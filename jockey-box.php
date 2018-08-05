<?php
/**
 * Plugin Name: Jockey Box
 * Plugin URI: http://lititzcraftbeerfest.com
 * Description: Instant craft beer festival support for breweries, sponsors, and food vendors
 * Version: 3.0.0
 * Author: Corey Salzano
 * Author URI: https://coreysalzano.com
 * License: GPLv3
 * License URI: https://www.gnu.org/licenses/gpl-3.0.html
 */

class Lititz_Craft_Beer_Fest_Jockey_Box{

	//boo LCBF server running PHP 5.3 still const CUSTOM_POST_TYPES = array(
	public static $CUSTOM_POST_TYPES = array(
		'brewery' => array(
			'singular' => 'Brewery',
			'plural'   => 'Breweries',
			'icon'     => 'dashicons-store',
		),

		'sponsor' => array(
			'singular' => 'Sponsor',
			'plural'   => 'Sponsors',
			'icon'     => 'dashicons-flag',
		),

		'food-vendor' => array(
			'singular' => 'Food vendor',
			'plural'   => 'Food vendors',
			'icon'     => 'dashicons-carrot',
		),
	);

	function hooks() {
		/**
		 * Create custom post types for breweries, sponsors & food vendors
		 */
		add_action( 'init', array( $this, 'create_post_types' ) );

		/**
		 * Add a taxonomy to catalog breweries, sponsors & food vendors by
		 * year of attendance.
		 */
		add_action( 'init', array( $this, 'add_custom_taxonomies' ) );
		//This should be an activation hook.
		add_action( 'init', array( $this, 'populate_custom_taxonomies_with_terms' ), 11 );

		$this->include_dependencies();

		if( class_exists( 'Jockey_Box_Shortcodes' ) ) {
			//Make some shortcodes available
			$shortcodes = new Jockey_Box_Shortcodes();
			$shortcodes->hooks();
		}

		if( class_exists( 'Jockey_Box_Advanced_Custom_Fields' ) ) {
			//Implement the Advanced Custom Fields plugin on our post types
			$acf_config = new Jockey_Box_Advanced_Custom_Fields();
			$acf_config->hooks();
		}
	}

	function include_dependencies() {
		$paths = array(
			'class-shortcodes.php',
			'class-advanced-custom-fields.php',
		);
		foreach( $paths as $path ) {
			$path = plugin_dir_path( __FILE__ ) . 'includes/' . $path;
			if( file_exists( $path ) ) { require $path; }
		}
	}

	function add_custom_taxonomies() {

		/**
		 * A taxonomy for year of attendance let's us list who is coming this
		 * year and who came three years ago.
		 */
		$year_tax_args = array (
			'hierarchical'   => true, //true to display as checkboxes in quick edit
			'label'          => 'Years',
			'labels'         => array (
			       'name'          => 'Years',
			       'singular_name' => 'Year',
			       'search_items'  => 'Search years of attendance',
			       'popular_items' => 'Popular years',
			       'all_items'     => 'All years',
			),
			'description'    => 'Track years of attendance',
			'query_var'      => 'years',
			'singular_label' => 'Year',
			'show_in_menu'   => false,
			'show_ui'        => true,
		);

		$post_types = array();
		foreach( Lititz_Craft_Beer_Fest_Jockey_Box::$CUSTOM_POST_TYPES as $post_type => $attributes ) {
			array_push( $post_types, $post_type );
		}
		register_taxonomy( 'years', $post_types, $year_tax_args );

		/**
		 * A taxonomy for level of sponsorship enables a tiered sponsor layout.
		 * A five star system corresponds loosely to the amount of money the
		 * sponsor has given the festival.
		 */
		$levels_tax_args = array (
			'hierarchical'   => true, //true to display as checkboxes in quick edit
			'label'          => 'Levels',
			'labels'         => array (
			       'name'          => 'Levels',
			       'singular_name' => 'Level',
			       'search_items'  => 'Search levels',
			       'popular_items' => 'Popular levels',
			       'all_items'     => 'All levels',
			),
			'description'    => 'Order sponsors by amount donated',
			'query_var'      => 'levels',
			'singular_label' => 'Level',
			'show_in_menu'   => true,
			'show_ui'        => true,
		);
		register_taxonomy( 'levels', 'sponsor', $levels_tax_args );
	}

	function populate_custom_taxonomies_with_terms() {

		//For each year since 2013
		for( $year=intval( date( 'Y' ) ); $year >= 2013; $year-- ) {
			if ( ! is_array( term_exists( $year, 'years' ) ) ) {
				$term_exists = wp_insert_term(
					$year,
					'years',
					array (
						'description' => $year,
						'slug' => $year,
					)
				);
			} else { break; }
		}

		//Populate our sponsorship levels taxonomy with 1 to 5 stars
		$tax = 'levels';
		for( $l=1; $l <= 5; $l++ ) {
			$slug = sprintf(
				'%s star%s',
				$this->number_word( $l ),
				1 != $l ? 's' : ''
			);
			if ( ! is_array( term_exists( $slug, $tax ) ) ) {
				$term_exists = wp_insert_term(
					ucfirst( $slug ),
					$tax,
					array (
						'description' => ucfirst( $slug ),
						'slug'        => $slug,
					)
				);
			} else { break; }
		}
	}

	function number_word( $num ) {
		switch( $num ){
			case 1: return 'one';
			case 2: return 'two';
			case 3: return 'three';
			case 4: return 'four';
			case 5: return 'five';
		}
	}

	function create_post_types() {
		foreach( Lititz_Craft_Beer_Fest_Jockey_Box::$CUSTOM_POST_TYPES as $post_type => $attributes ) {

			$taxonomies = array( 'category', 'years' );
			if( 'sponsor' == $post_type ) {
				$taxonomies = array( 'levels', 'years' );
			}

			register_post_type( $post_type,
				array(
					'labels'              => array(
						'name'          => __( $attributes['plural'] ),
						'singular_name' => __( $attributes['singular'] ),
					),
					'menu_icon'           => $attributes['icon'],
					'public'              => true,
					'has_archive'         => true,
					'taxonomies'          => $taxonomies,
					'exclude_from_search' => true,
					'supports'            => array( 'title', 'editor', 'thumbnail' )
				)
			);
		}
	}
}
$salzano_486293853745 = new Lititz_Craft_Beer_Fest_Jockey_Box();
$salzano_486293853745->hooks();
