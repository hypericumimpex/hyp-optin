<?php

$xbox->add_field(array(
	'id' => 'display-for-users',
	'name' => __( 'Display popup for these users', 'masterpopups' ),
	'type' => 'checkbox',
	'default' => array('logged-in', 'not-logged-in'),
	'items' => array(
		'logged-in' => __( 'Logged-In Users', 'masterpopups' ),
		'not-logged-in' => __( 'Not Logged-In Users', 'masterpopups' ),
	),
));
$xbox->add_field(array(
	'id' => 'display-on-devices',
	'name' => __( 'Display popup on these devices', 'masterpopups' ),
	'type' => 'checkbox',
	'default' => array('desktop', 'tablet', 'mobile'),
	'items' => array(
		'desktop' => 'Desktop',
		'tablet' => 'Tablet',
		'mobile' => 'Mobile',
	),
));