<?php
$xbox->add_field(array(
	'id' => 'notification-bar-fixed',
	'name' => __( 'Remains fixed', 'masterpopups' ),
	'type' => 'switcher',
	'default' => 'on',
	'desc' => __( 'The notification bar remains fixed on top/bottom.', 'masterpopups' ),
));
$xbox->add_field(array(
	'id' => 'notification-bar-push-page-dow',
	'name' => __( 'Push page down', 'masterpopups' ),
	'type' => 'switcher',
	'default' => 'on',
	'desc' => __( 'Activate to push the page down when open the top notification bar.', 'masterpopups' ),
));
$xbox->add_field(array(
	'id' => 'notification-bar-fixed-header-selector',
	'name' => __( 'Fixed Header. Enter ID or Class', 'masterpopups' ),
	'desc' => __( 'E.g: #header or .custom-header', 'masterpopups' ),
	'type' => 'text',
	'attributes' => array(
		'placeholder' => '#header',
	),
));
$xbox->add_field(array(
	'id' => 'notification-bar-container-page-selector',
	'name' => __( 'Container of your page. Enter ID or Class.', 'masterpopups' ),
	'type' => 'text',
	'desc' => __( 'If you leave it blank, the "body" tag is used as container.', 'masterpopups' ),
	'attributes' => array(
		'placeholder' => '#page',
	),
));