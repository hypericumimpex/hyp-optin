<?php

use MasterPopups\Includes\Assets as Assets;

$xbox->open_mixed_field(array('name' => __( 'Sticky control', 'masterpopups' )));
	$xbox->add_field(array(
		'id' => 'sticky-control',
		'name' => __( 'Enable', 'masterpopups' ),
		'type' => 'switcher',
		'default' => 'off',
	));
	$xbox->add_field(array(
		'id' => 'sticky-control-initial',
		'name' => __( 'Initial mode', 'masterpopups' ),
		'desc' => __( 'Display only sticky control on initial open', 'masterpopups' ),
		'type' => 'switcher',
		'default' => 'off',
		'options' => array(
			'desc_tooltip' => true,
			'show_if' => array('sticky-control', '=', 'on'),
		),
	));
	$xbox->add_field(array(
		'id' => 'sticky-control-vertical',
		'name' => __( 'Vertically', 'masterpopups' ),
		'desc' => __( 'Show the sticky control vertically.', 'masterpopups' ),
		'type' => 'switcher',
		'default' => 'off',
		'options' => array(
			'desc_tooltip' => true,
			'show_if' => array('sticky-control', '=', 'on'),
		),
	));
$xbox->close_mixed_field();

$xbox->open_mixed_field(array('name' => __( 'Sticky size', 'masterpopups' )));
	$xbox->add_field(array(
		'id' => 'sticky-width',
		'name' => __( 'Width', 'masterpopups' ),
		'type' => 'number',
		'default' => 'auto',
		'options' => array(
			'disable_spinner' => true,
		),
		'attributes' => array(
			'min' => 0,
		),
	));
	$xbox->add_field(array(
		'id' => 'sticky-height',
		'name' => __( 'Height', 'masterpopups' ),
		'type' => 'number',
		'default' => 40,
		'options' => array(
			'show_spinner' => true,
		),
		'attributes' => array(
			'min' => 0,
		),
	));
	$xbox->add_field(array(
		'id' => 'sticky-padding-x',
		'name' => __( 'Padding left/right', 'masterpopups' ),
		'type' => 'number',
		'default' => 15,
		'options' => array(
			'show_spinner' => true,
		),
		'attributes' => array(
			'min' => 0,
		),
	));
$xbox->close_mixed_field();

$xbox->open_mixed_field(array('name' => __( 'Sticky font', 'masterpopups' )));
	$xbox->add_field(array(
		'id' => 'sticky-font-size',
		'name' => 'Font size',
		'type' => 'number',
		'default' => '15',
		'attributes' => array(
			'min' => 0,
		),
		'options' => array(
			'show_spinner' => true,
		),
	));
	$xbox->add_field(array(
		'id' => 'sticky-font-color',
		'name' => 'Font color',
		'type' => 'colorpicker',
		'default' => 'rgba(255,255,255,1)',
		'options' => array(
			'format' => 'rgba',
			'opacity' => 1,
		),
	));
	$xbox->add_field(array(
		'id' => 'sticky-font-family',
		'name' => 'Font family',
		'type' => 'select',
		'default' => 'Roboto',
		'items' => array(
			'Fonts' => Assets::local_fonts(),
			'Google Fonts' => XboxItems::google_fonts(),
		),
		'options' => array(
			'sort' => 'asc',
			'search' => true,
		),
	));
$xbox->close_mixed_field();

$xbox->open_mixed_field(array('name' => __( 'Sticky content', 'masterpopups' )));
	$xbox->add_field(array(
		'id' => 'sticky-text',
		'name' => __( 'Text', 'masterpopups' ),
		'type' => 'text',
		'default' => __( 'Open popup', 'masterpopups' ),
		'row_class' => 'not-full-width',
		'attributes' => array(
			'style' => 'width: 300px'
		)
	));
	$xbox->add_field(array(
		'id' => 'sticky-show-icon',
		'name' => __( 'Show icon', 'masterpopups' ),
		'type' => 'switcher',
		'default' => 'on',
	));
	$xbox->add_field(array(
		'id' => 'sticky-bg-icon',
		'name' => __( 'Icon background color', 'masterpopups' ),
		'type' => 'colorpicker',
		'default' => 'rgba(32,95,240,0.8)',
		'options' => array(
			'format' => 'rgba',
			'opacity' => 1,
			'show_if' => array('sticky-show-icon', '=', 'on')
		),
	));
	$xbox->add_field(array(
		'id' => 'sticky-icon',
		'name' => __( 'Icon', 'masterpopups' ),
		'type' => 'icon_selector',
		'default' => 'mpp-icon-chevron-up',
		'items' => Assets::arrow_icons(),//Load by ajax
		'options' => array(
			'load_with_ajax' => false,
			'wrap_height' => 'auto',
			'size' => '40px',
			'hide_search' => true,
			'hide_buttons' => true,
			'show_if' => array('sticky-show-icon', '=', 'on')
		)
	));
$xbox->close_mixed_field();

$xbox->open_mixed_field(array('name' => __( 'Sticky background', 'masterpopups' )));
	$xbox->add_field(array(
		'id' => 'sticky-bg-color',
		'name' => 'Background color',
		'type' => 'colorpicker',
		'default' => 'rgba(0,0,0,0.8)',
		'options' => array(
			'format' => 'rgba',
			'opacity' => 1,
		),
	));
	$xbox->add_field(array(
		'id' => 'sticky-bg-size',
		'name' => 'Background size',
		'type' => 'select',
		'default' => 'cover',
		'items' => array(
			'auto' => 'Auto',
			'cover' => 'Cover',
			'contain' => 'Contain',
		),
	));
	$xbox->add_field(array(
		'id' => 'sticky-bg-position',
		'name' => 'Background position',
		'type' => 'text',
		'default' => 'center center',
		'row_class' => 'not-full-width',
		'attributes' => array(
			'style' => 'width: 110px'
		)
	));
	$xbox->add_field(array(
		'id' => 'sticky-bg-image',
		'name' => 'Background image',
		'type' => 'file',
		'options' => array(
			'mime_types' => array( 'jpg', 'jpeg', 'png', 'gif', 'ico' ),
			'preview_size' => array( 'width' => '30px','height' => '30px' ),
		),
		'row_class' => 'mpp-image-file',
		'grid' => '7-of-8 last'
	));
$xbox->close_mixed_field();