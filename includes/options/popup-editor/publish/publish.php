<?php
/*
|-----------------------------------------------------------------------------------
| Publish On
|-----------------------------------------------------------------------------------
*/
$xbox->add_field(array(
	'type' => 'title',
	'name' => __( 'When do you want to publish the popup?', 'masterpopups' ),
));
$xbox->add_field(array(
	'id' => 'publish-on',
	'type' => 'radio',
	'default' => 'now',
	'items' => array(
		'now' => __( 'Immediately', 'masterpopups' ),
		'date' => __( 'Set date', 'masterpopups' ),
	),
	'options' => array(
		'show_name' => false,
		'desc_tooltip' => true,
	)
));
$xbox->open_mixed_field(array(
	'name' => __( 'Publish date', 'masterpopups' ),
	'desc' => __( 'The "Time Zone" of the WordPress settings is used.', 'masterpopups' ),
	'options' => array(
		'show_if' => array('publish-on', '=', 'date' ),
	),
));
	$xbox->add_field(array(
		'id' => 'publish-on-date',
		'name' => __( 'Date', 'masterpopups' ),
		'type' => 'date',
		'default' => '',
	));

	$xbox->add_field(array(
		'id' => 'publish-on-time',
		'name' => __( 'Time', 'masterpopups' ),
		'type' => 'time',
		'default' => '',
	));
$xbox->close_mixed_field();

/*
|-----------------------------------------------------------------------------------
| Publish Stop
|-----------------------------------------------------------------------------------
*/
$xbox->add_field(array(
	'type' => 'title',
	'name' => __( 'When you would like to stop showing the popup?', 'masterpopups' ),
));

$xbox->add_field(array(
	'id' => 'publish-stop',
	'type' => 'radio',
	'default' => 'never',
	'items' => array(
		'never' => __( 'Never', 'masterpopups' ),
		'date' => __( 'Set date', 'masterpopups' ),
	),
	'options' => array(
		'show_name' => false,
		'desc_tooltip' => true,
	)
));
$xbox->open_mixed_field(array(
	'name' => __( 'Stop date', 'masterpopups' ),
	'desc' => __( 'The "Time Zone" of the WordPress settings is used.', 'masterpopups' ),
	'options' => array(
		'show_if' => array('publish-stop', '=', 'date' ),
	),
));
	$xbox->add_field(array(
		'id' => 'publish-stop-date',
		'name' => __( 'Date', 'masterpopups' ),
		'type' => 'date',
		'default' => '',
	));

	$xbox->add_field(array(
		'id' => 'publish-stop-time',
		'name' => __( 'Time', 'masterpopups' ),
		'type' => 'time',
		'default' => '',
	));
$xbox->close_mixed_field();
