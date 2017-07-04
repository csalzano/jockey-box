<?php

class Jockey_Box_Advanced_Custom_Fields{

	function hooks() {
		add_action( 'acf/register_fields', array( &$this, 'register_acf_field_groups' ) );
	}

	function register_acf_field_groups() {
		if(function_exists("register_field_group"))
		{
			register_field_group(array (
				'id' => 'acf_brewery-fields',
				'title' => 'Brewery fields',
				'fields' => array (
					array (
						'key' => 'field_5589f5ed6c669',
						'label' => 'City & state',
						'name' => 'city_and_state',
						'type' => 'text',
						'default_value' => '',
						'placeholder' => '',
						'prepend' => '',
						'append' => '',
						'formatting' => 'html',
						'maxlength' => '',
					),
					array (
						'key' => 'field_5589f6076c66a',
						'label' => 'URL',
						'name' => 'url',
						'type' => 'text',
						'default_value' => '',
						'placeholder' => '',
						'prepend' => '',
						'append' => '',
						'formatting' => 'html',
						'maxlength' => '',
					),
				),
				'location' => array (
					array (
						array (
							'param' => 'post_type',
							'operator' => '==',
							'value' => 'brewery',
							'order_no' => 0,
							'group_no' => 0,
						),
					),
				),
				'options' => array (
					'position' => 'normal',
					'layout' => 'no_box',
					'hide_on_screen' => array (
					),
				),
				'menu_order' => 0,
			));
			register_field_group(array (
				'id' => 'acf_food-vendor-fields',
				'title' => 'Food vendor fields',
				'fields' => array (
					array (
						'key' => 'field_55c8d0452701c',
						'label' => 'URL',
						'name' => 'url',
						'type' => 'text',
						'default_value' => '',
						'placeholder' => '',
						'prepend' => '',
						'append' => '',
						'formatting' => 'html',
						'maxlength' => '',
					),
				),
				'location' => array (
					array (
						array (
							'param' => 'post_type',
							'operator' => '==',
							'value' => 'food-vendor',
							'order_no' => 0,
							'group_no' => 0,
						),
					),
				),
				'options' => array (
					'position' => 'normal',
					'layout' => 'no_box',
					'hide_on_screen' => array (
					),
				),
				'menu_order' => 0,
			));
			register_field_group(array (
				'id' => 'acf_sponsor-fields',
				'title' => 'Sponsor fields',
				'fields' => array (
					array (
						'key' => 'field_55aeca14b705c',
						'label' => 'URL',
						'name' => 'url',
						'type' => 'text',
						'default_value' => '',
						'placeholder' => '',
						'prepend' => '',
						'append' => '',
						'formatting' => 'html',
						'maxlength' => '',
					),
					array (
						'key' => 'field_5754f287b2cae',
						'label' => 'Sort order',
						'name' => 'sort_order',
						'type' => 'number',
						'default_value' => '',
						'placeholder' => '',
						'prepend' => '',
						'append' => '',
						'min' => '',
						'max' => '',
						'step' => '',
					),
				),
				'location' => array (
					array (
						array (
							'param' => 'post_type',
							'operator' => '==',
							'value' => 'sponsor',
							'order_no' => 0,
							'group_no' => 0,
						),
					),
				),
				'options' => array (
					'position' => 'normal',
					'layout' => 'no_box',
					'hide_on_screen' => array (
					),
				),
				'menu_order' => 0,
			));
		}
	}
}
