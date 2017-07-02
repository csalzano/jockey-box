<?php

class Jockey_Box_Shortcodes{

	function hooks() {
		add_shortcode( 'jockey_box_grid', array( &$this, 'grid_shortcode' ) );
		add_shortcode( 'jockey_box_sponsor_logo', array( &$this, 'sponsor_logo_shortcode' ) );
		add_shortcode( 'jockey_box_sponsor_grid', array( &$this, 'sponsor_grid_shortcode' ) );
	}

	function grid_shortcode( $atts ) {

		$raw_atts = $atts;

		$atts = shortcode_atts( array(
			'object' => 'brewery',
			'years' => date('Y')
		), $atts, 'jockey_box_grid' );

		if( 'sponsor' == $atts['object'] ) {
			return $this->sponsor_grid_shortcode( $raw_atts );
		}

		$posts = get_posts( array(
			'post_status' => 'publish',
			'post_type' => $atts['object'],
			'posts_per_page' => -1,
			'orderby' => 'rand',
		    'tax_query' => array(
		        array(
			        'taxonomy' => 'years',
			        'field' => 'slug',
			        'terms' => explode( ',', $atts['years'] ),
			    )),
		) );

		if ( 0 == sizeof( $posts ) ) { return ''; }

		$html = '<ul class="breweries" id="beer">';
		foreach( $posts as $brewery ) {
			$attr = array(
				'alt'	=> trim( strip_tags( $brewery->post_title ) ),
				'title'	=> trim( strip_tags( $brewery->post_title ) ),
			);
			//link the featured image to the brewery home page
			$url = get_post_meta( $brewery->ID, 'url', true );
			$html .= '<li><a href="' . $url . '">'
				. get_the_post_thumbnail( $brewery->ID, 'full', $attr )
				. '</a>'

			//title, linked
				. '<p>'
				. '<a href="' . $url . '">' . $brewery->post_title . '<br />'

			//city, state
				. '<span class="city-and-state">'
				. get_post_meta( $brewery->ID, 'city_and_state', true )
				. '</span></a></p>'
				. '</li>';
		}
		return $html . '</ul>';
	}

	function sponsor_grid_shortcode( $atts ) {
		//[jockey_box_sponsor_grid categories="2,6"]

		$atts = shortcode_atts( array(
			'object'     => 'sponsor',
			'years'      => date('Y'),
			'categories' => '',
		), $atts, 'jockey_box_grid' );

		$args = array(
			'post_status'    => 'publish',
			'post_type'      => 'sponsor',
			'posts_per_page' => -1,
			'orderby'        => 'meta_value',
			'order'          => 'ASC',
			'meta_key'       => 'sort_order',
		    'tax_query' => array(
		    	'relation' => 'AND',
		        array(
			        'taxonomy' => 'years',
			        'field' => 'slug',
			        'terms' => explode( ',', $atts['years'] ),
			        'operator' => 'AND'
			    )),
		);

		if( ! empty( $atts['categories'] ) ) {
			$args['tax_query'][] = array(
				'taxonomy' => 'category',
				'field' => 'id',
				'terms' => explode( ",", $atts['categories'] ),
				'operator' => 'AND'
			);
		}

		$sponsors = get_posts( $args );

		echo "<!--";
		var_dump( $args );
		echo "-->";

		if ( 0 == sizeof( $sponsors ) ) { return ''; }

		$html = '';
		$first_minor =
		$first_major =
		$first_regular = true;

		$html .= '<ul class="sponsors">';
		$last_level = '';
		foreach( $sponsors as $sponsor ) {

			$minor = has_category( 'minor-sponsor', $sponsor );
			$major = has_category( 'major-sponsor', $sponsor );
			$premiere = has_category( 'premiere-sponsor', $sponsor );
			$paltry = has_category( 'paltry-sponsor', $sponsor );

			$html .= '<li class="';
			if( $minor || $major || $premiere || $paltry ) {

				if( $minor ) {
					$html .= 'minor';
					if( $first_minor ){
						$html .= ' cl';
						$first_minor = false;
					}
				}
				if( $major ) {
					$html .= 'major';
					if( $first_major ){
						$html .= ' cl';
						$first_major = false;
					}
				}
				if( $premiere ) { $html .= 'premiere '; }
				if( $paltry ) { $html .= 'paltry '; }
			}
			else
			{
				if( $first_regular ) {
					$html .= ' cl';
					$first_regular = false;
				}
			}
			$html .= '">';

			$attr = array(
				'alt'	=> trim( strip_tags( $sponsor->post_title ) ),
				'title'	=> trim( strip_tags( $sponsor->post_title ) ),
			);

			//Create a link
			$url = get_post_meta( $sponsor->ID, 'url', true );
			if( 0 < strlen( $url ) ) { $html .= '<a href="' . $url . '">'; }
			if( ! $paltry ) { // Paltry sponsors and sponsors with no thumbnail link the title
				$img = get_the_post_thumbnail( $sponsor->ID, 'full', $attr );
				$html .= ( '' != $img ? $img . '<br />' : $sponsor->post_title );
			}
			if( ! isset( $atts['imageonly'] ) || 'true' != $atts['imageonly']) {
				$html .= $sponsor->post_title;
			}
			if( 0 < strlen( $url ) ) { $html .= '</a>'; }
			if( isset( $atts['imageonly'] ) && 'true' != $atts['imageonly'])
			{
				$html .= $atts['imageonly'];
				//title, linked
				$html .= '<p>';
				if( 0 < strlen( $url ) ) { $html .= '<a href="' . $url . '">'; }
				$html .= get_the_title();
				if( 0 < strlen( $url ) ) { $html .= '</a></p>'; }
			}
			$html .= '</li>';
		}
		return $html . '</ul>';
	}

	function sponsor_logo_shortcode( $atts ) {
		//[jockey_box_sponsor_logo id="4444"]
		if( ! isset( $atts['id'] ) ) { return ''; }

		//do we have a URL saved for this sponsor in post-meta?
		$url = get_post_meta( $atts['id'], 'url', true );

		$img_element = get_the_post_thumbnail( $atts['id'], 'full', array( 'class' => 'sponsor' ) );

		if( '' == $url ) {
			return $img_element;
		}
		return '<a href="' . $url . '">' . $img_element . '</a>';
	}

}
