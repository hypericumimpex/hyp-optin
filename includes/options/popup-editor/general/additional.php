<?php
$xbox->add_field(array(
	'id' => 'border-radius',
	'name' => __( 'Border radius', 'masterpopups' ),
	'type' => 'number',
	'default' => '0',
	'attributes' => array(
		'min' => 0,
	),
	'options' => array(
		'show_spinner' => true,
	),
));
$xbox->add_field(array(
	'id' => 'box-shadow',
	'name' => 'Box shadow',
	'type' => 'text',
	'default' => '0px 0px 16px 4px rgba(0,0,0,0.5)',
));
$xbox->open_mixed_field(array('name' => 'Margin'));
	$xbox->add_field(array(
		'id' => 'margin-top',
		'name' => __( 'Margin top', 'masterpopups' ),
		'type' => 'text',
		'default' => '0',
        'grid' => '2-of-8',
	));
    $xbox->add_field(array(
        'id' => 'margin-right',
        'name' => __( 'Margin right', 'masterpopups' ),
        'type' => 'text',
        'default' => 'auto',
        'grid' => '2-of-8',
    ));
	$xbox->add_field(array(
		'id' => 'margin-bottom',
		'name' => __( 'Margin bottom', 'masterpopups' ),
        'type' => 'text',
        'default' => '0',
        'grid' => '2-of-8',
	));
    $xbox->add_field(array(
        'id' => 'margin-left',
        'name' => __( 'Margin left', 'masterpopups' ),
        'type' => 'text',
        'default' => 'auto',
        'grid' => '2-of-8',
    ));
$xbox->close_mixed_field();

$xbox->add_field(array(
	'id' => 'placeholder-color',
	'name' => 'Placeholder color',
	'type' => 'colorpicker',
	'default' => 'rgba(134,134,134,1)',
	'options' => array(
		'format' => 'rgba',
		'opacity' => 1,
	),
));

$xbox->add_field(array(
	'id' => 'overflow',
	'name' => 'Overflow',
	'type' => 'select',
	'default' => 'visible',
	'items' => array(
		'auto' => 'Auto',
		'visible' => 'Visible',
		'hidden' => 'Hidden',
		'scroll' => 'Scroll',
	),
));

$xbox->add_field(array(
	'id' => 'disable-page-scroll',
	'name' => __( 'Disable page scroll', 'masterpopups' ),
	'type' => 'switcher',
	'default' => 'off',
	'desc' => __( 'Disable scrolling while the popup is open', 'masterpopups' ),
	'options' => array(
		'desc_tooltip' => false,
	),
));

$xbox->add_field(array(
    'id' => 'disclaimer-enabled',
    'name' => __( 'Enable Disclaimer Features', 'masterpopups' ),
    'type' => 'switcher',
    'default' => 'off',
    'options' => array(
        'desc_tooltip' => false,
    ),
));
$xbox->add_field(array(
    'id' => 'ratio-small-devices',
    'name' => __( 'Ratio for Small Devices', 'masterpopups' ),
    'desc' => __( 'Enter the value 0.9 if you want the slightly smaller popup.', 'masterpopups' ),
    'type' => 'number',
    'default' => '1',
    'options' => array(
        'show_spinner' => true,
        'show_unit' => false,
    ),
    'attributes' => array(
        'min' => 0,
        'step' => 0.1,
        'precision' => 1
    ),
));

$xbox->add_field(array(
    'id' => 'use-theme-links-color',
    'name' => __( 'Use theme links color', 'masterpopups' ),
    'desc' => __( 'If enabled, all links within the popup will have the color set in the current WordPress theme.', 'masterpopups' ),
    'type' => 'switcher',
    'default' => 'on',
    'options' => array(
        'desc_tooltip' => false,
    ),
));

$xbox->open_mixed_field(array('name' => __( 'Play Notification Sound', 'masterpopups' ) ));
	$xbox->add_field(array(
		'id' => 'play-sound',
		'name' => __( 'Enable', 'masterpopups' ),
		'type' => 'switcher',
		'default' => 'off',
	));
	$xbox->add_field(array(
		'id' => 'play-sound-delay',
		'name' => __( 'Play delay', 'masterpopups' ),
		'type' => 'number',
		'default' => '-10',
		'options' => array(
			'show_spinner' => true,
			'unit' => 'ms',
			'show_if' => array('play-sound', '=', 'on' ),
		),
		'attributes' => array(
			//'min' => -2000,
			'step' => 100,
		),
	));
	$xbox->add_field(array(
		'id' => 'play-sound-source',
		'name' => __( 'Audio', 'masterpopups' ),
		'type' => 'select',
		'default' => '',
		'items' => array(
			'' => '- Select sound -',
			'sound1.mp3' => 'Sound 1',
			'sound2.mp3' => 'Sound 2',
			'sound3.mp3' => 'Sound 3',
			'sound4.mp3' => 'Sound 4',
			'sound5.mp3' => 'Sound 5',
			'sound6.mp3' => 'Sound 6',
			'sound7.ogg' => 'Sound 7',
		),
		'options' => array(
			'show_if' => array('play-sound', '=', 'on' ),
		),
	));
$xbox->close_mixed_field();
