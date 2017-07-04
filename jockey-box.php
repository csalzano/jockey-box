<?php
/**
 * Plugin Name: Jockey Box
 * Plugin URI: http://lititzcraftbeerfest.com
 * Description: Instant craft beer festival support for breweries, sponsors, and food vendors
 * Version: 2.1.0
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
			'icon'     => 'dashicon-store',
			),

		'sponsor' => array(
			'singular' => 'Sponsor',
			'plural'   => 'Sponsors',
			'icon'     => 'dashicon-flag',
			),

		'food-vendor' => array(
			'singular' => 'Food vendor',
			'plural'   => 'Food vendors',
			'icon'     => 'dashicon-carrot',
			),
	);

	function hooks() {
		/**
		 * Create custom post types for breweries, sponsors & food vendors
		 */
		add_action( 'init', array( &$this, 'create_post_types' ) );

		/**
		 * Add a taxonomy to catalog breweries, sponsors & food vendors by
		 * year of attendance.
		 */
		add_action( 'init', array( &$this, 'add_custom_taxonomies' ) );
		add_action( 'init', array( &$this, 'populate_custom_taxonomies_with_terms' ) );

		//include our shortcodes class and add the shortcodes
		$this->include_dependencies();
		if( class_exists( 'Jockey_Box_Shortcodes' ) ) {
			$shortcodes = new Jockey_Box_Shortcodes();
			$shortcodes->hooks();

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
			'hierarchical'   => true,
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
			} else { return; }
		}
	}

	function create_post_types() {
		foreach( Lititz_Craft_Beer_Fest_Jockey_Box::$CUSTOM_POST_TYPES as $post_type => $attributes ) {
			register_post_type( $post_type,
				array(
					'labels' => array(
						'name'          => __( $attributes['plural'] ),
						'singular_name' => __( $attributes['singular'] ),
					),
					'menu_icon' => $attributes['icon'],
					'public' => true,
					'has_archive' => true,
					'taxonomies' => array( 'category', 'years' ),
					'exclude_from_search' => true,
					'supports' => array( 'title', 'editor', 'thumbnail' )
				)
			);
		}
	}
}
$salzano_486293853745 = new Lititz_Craft_Beer_Fest_Jockey_Box();
$salzano_486293853745->hooks();
