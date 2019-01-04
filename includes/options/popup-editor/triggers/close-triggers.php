<?php

/*
|---------------------------------------------------------------------------------------------------
| Close Click on Overlay
|---------------------------------------------------------------------------------------------------
*/
$xbox->add_field( array(
	'type' => 'title',
	'name' => 'On Click Overlay',
	'desc' => __( 'Close the popup by clicking on overlay', 'masterpopups' ),
));
$xbox->open_mixed_field(array('name' => __( 'Status', 'masterpopups' ) ));
	$xbox->add_field(array(
		'id' => 'trigger-close-on-click-overlay',
		'name' => __( 'Enable', 'masterpopups' ),
		'type' => 'switcher',
		'default' => 'on',
		'options' => array(
			'desc_tooltip' => true,
			//'show_name' => false,
		)
	));
$xbox->close_mixed_field();

/*
|---------------------------------------------------------------------------------------------------
| Close with ESC key
|---------------------------------------------------------------------------------------------------
*/
$xbox->add_field( array(
	'type' => 'title',
	'name' => 'On ESC Keydown',
	'desc' => __( 'Close the popup by pressing the ESC Key', 'masterpopups' ),
));
$xbox->open_mixed_field(array('name' => __( 'Status', 'masterpopups' ) ));
	$xbox->add_field(array(
		'id' => 'trigger-close-on-esc-keydown',
		'name' => __( 'Enable', 'masterpopups' ),
		'type' => 'switcher',
		'default' => 'on',
		'options' => array(
			'desc_tooltip' => true,
			//'show_name' => false,
		)
	));
$xbox->close_mixed_field();

/*
|---------------------------------------------------------------------------------------------------
| Close Automatically
|---------------------------------------------------------------------------------------------------
*/
$xbox->add_field( array(
	'type' => 'title',
	'name' => 'Close After X Seconds',
	'desc' => __( 'Close the popup automatically after X seconds', 'masterpopups' ),
));
$xbox->open_mixed_field(array('name' => __( 'Status', 'masterpopups' ) ));
	$xbox->add_field(array(
		'id' => 'trigger-close-automatically',
		'name' => __( 'Enable', 'masterpopups' ),
		'type' => 'switcher',
		'default' => 'off',
		'options' => array(
			'desc_tooltip' => true,
			//'show_name' => false,
		)
	));
	$xbox->add_field(array(
		'id' => 'trigger-close-automatically-delay',
		'name' => __( 'Time delay', 'masterpopups' ),
		'type' => 'number',
		'default' => '10',
		'options' => array(
			'show_spinner' => true,
			'unit' => 'sec',
			'show_if' => array('trigger-close-automatically', '=', 'on' ),
		),
		'attributes' => array(
			'min' => 1,
		),
	));
$xbox->close_mixed_field();

/*
|---------------------------------------------------------------------------------------------------
| On Scroll
|---------------------------------------------------------------------------------------------------
*/
$xbox->add_field( array(
	'type' => 'title',
	'name' => 'On Scroll',
	'desc' => __( 'Close the popup after scrolling down X amount', 'masterpopups' ),
));
$xbox->open_mixed_field(array('name' => __( 'Status', 'masterpopups' ) ));
	$xbox->add_field(array(
		'id' => 'trigger-close-on-scroll',
		'name' => __( 'Enable', 'masterpopups' ),
		'type' => 'switcher',
		'default' => 'off',
		'options' => array(
			'desc_tooltip' => true,
			//'show_name' => false,
		)
	));
	$xbox->add_field(array(
		'id' => 'trigger-close-on-scroll-amount',
		'name' => __( 'Scroll amount', 'masterpopups' ),
		'type' => 'number',
		'default' => '0',
		'options' => array(
			'show_spinner' => true,
			'unit' => '%',
			'unit_picker' => array('px' => 'PX', '%' => '%'),
			'show_if' => array('trigger-close-on-scroll', '=', 'on' ),
		),
		'attributes' => array(
			'min' => 10,
		),
	));
$xbox->close_mixed_field();

