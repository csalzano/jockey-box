<?php
/**
 * Plugin Name: Jockey Box
 * Plugin URI: http://lititzcraftbeerfest.com
 * Description: Instant craft beer festival support for breweries, sponsors, and food vendors
 * Version: 2.0.0
 * Author: Corey Salzano
 * Author URI: https://coreysalzano.com
 * License: GPLv2 or later
 * License URI: http://www.gnu.org/licenses/gpl-2.0.html
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
		}
	}

	function include_dependencies() {
		$path = plugin_dir_path( __FILE__ ) . 'includes/class-shortcodes.php';
		if( file_exists( $path ) ) { require $path; }
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
			'meta_box_cb'    => array( $this, 'meta_box_years_taxonomy' ),
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

	function meta_box_years_taxonomy( $post ) {
		/**
		 * Creates HTML output for a meta box that turns a taxonomy into
		 * a select drop-down list instead of the typical checkboxes
		 */
		$taxonomy_name = 'years';

		//get all the term names and slugs for $taxonomy_name
		$terms = get_terms( $taxonomy_name,  array( 'hide_empty' => false ) );

		$HTML = '';
		if ( ! empty( $terms ) && ! is_wp_error( $terms ) ) {

			//get the saved terms for this taxonomy
			$saved_terms = wp_get_object_terms( $post->ID, $taxonomy_name );
			$saved_term_slugs = array_map( create_function( '$o', 'return $o->slug;' ), $saved_terms );

			$HTML .= '<ul id="categorychecklist" class="categorychecklist form-no-clear">';
			foreach( $terms as $term ) {
				$HTML .= '<li><label class="selectit">'
					. '<input type="checkbox" name="tax_input[years][]" id="in-years-' . $term->slug . '" value="' . $term->slug . '"'
					. checked( true, in_array( $term->slug, $saved_term_slugs ), false )
					. ' /> ' . $term->name . '</label></li>';
			}
			$HTML .= '</ul>';
		}
		echo $HTML;
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

		//create a CPT for brewery tents
		register_post_type( 'brewery',
			array(
				'labels' => array(
					'name'          => __( 'Breweries' ),
					'singular_name' => __( 'Brewery' )
				),
				'menu_icon' => 'dashicons-store',
				'public' => true,
				'has_archive' => true,
				'taxonomies' => array( 'category', 'years' ),
				'exclude_from_search' => true,
				'supports' => array( 'title', 'editor', 'thumbnail' )
			)
		);

		//create a CPT for sponsors
		register_post_type( 'sponsor',
			array(
				'labels' => array(
					'name' => __( 'Sponsors' ),
					'singular_name' => __( 'Sponsor' )
				),
				'menu_icon' => 'dashicons-flag',
				'public' => true,
				'has_archive' => false,
				'taxonomies' => array('category'),
				'exclude_from_search' => true,
				'supports' => array('title', 'editor', 'thumbnail')
			)
		);

		//create a CPT for food vendors
		register_post_type( 'food-vendor',
			array(
				'labels' => array(
					'name' => __( 'Food vendors' ),
					'singular_name' => __( 'Food vendor' )
				),
				'menu_icon' => 'dashicons-carrot',
				'public' => true,
				'has_archive' => false,
				'taxonomies' => array('category'),
				'exclude_from_search' => true,
				'supports' => array('title', 'editor', 'thumbnail')
			)
		);
	}
}
$salzano_486293853745 = new Lititz_Craft_Beer_Fest_Jockey_Box();
$salzano_486293853745->hooks();
