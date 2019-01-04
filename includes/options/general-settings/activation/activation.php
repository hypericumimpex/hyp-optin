<?php

$xbox->add_field(array(
	'type' => 'title',
	'name' => __( 'Plugin Activation', 'masterpopups' ),
	//'desc' => __( 'From here you can activate your license to get live updates and more benefits.', 'masterpopups'),
));
$xbox->add_field(array(
	'id' => 'activation-status',
	'type' => 'hidden',
	'default' => 'off',
));
$xbox->add_field(array(
	'id' => 'activation-auth',
	'type' => 'hidden',
	'default' => 'maxwp:items',
));
$xbox->add_field(array(
	'id' => 'activation-status-info',
	'name' => __( 'Status', 'masterpopups' ),
	'type' => 'html',
	'content' => '<span class="ampp-activation-status ampp-bold xbox-color-red">'.__( 'Not Activated', 'masterpopups').'</span>',
));
$xbox->add_field(array(
	'id' => 'activation-username',
	'name' => __( 'Envato Username', 'masterpopups' ).'<span class="xbox-required-field">*</span>',
	'type' => 'text',
	'default' => '',
	'desc' => '',
	'grid' => '4-of-8',
	'options' => array(
		'show_name' => true,
		'helper' => '<i class="xbox-icon xbox-icon-user"></i>'
	),
));
$xbox->add_field(array(
	'id' => 'activation-api-key',
	'name' => __( 'Envato Api Key', 'masterpopups' ).'<span class="xbox-required-field">*</span>',
	'type' => 'text',
	'default' => '',
	'desc' => sprintf(__( 'Click %shere%s for more information', 'masterpopups' ), '<a target="_blank" href="http://masterpopups.com/docs/how-to-activate-your-license/">', '</a>'),
	'grid' => '4-of-8',
	'options' => array(
		'show_name' => true,
		'helper' => '<i class="xbox-icon xbox-icon-key"></i>'
	),
));
$xbox->add_field(array(
	'id' => 'activation-purchase-code',
	'name' => __( 'Purchase Code', 'masterpopups' ).'<span class="xbox-required-field">*</span>',
	'type' => 'text',
	'default' => '',
	'desc' => sprintf(__( 'Click %shere%s for more information', 'masterpopups' ), '<a target="_blank" href="http://masterpopups.com/docs/how-to-activate-your-license/">', '</a>'),
	'grid' => '4-of-8',
	'options' => array(
		'show_name' => true,
		'helper' => '<i class="xbox-icon xbox-icon-shopping-cart"></i>'
	),
));
$xbox->add_field(array(
	'id' => 'activation-email',
	'name' => __( 'Your email', 'masterpopups' ),
	'type' => 'text',
	'default' => '',
	'desc' => __( 'To get in touch when you need help', 'masterpopups' ),
	'grid' => '4-of-8',
	'options' => array(
		'show_name' => true,
		'helper' => '<i class="xbox-icon xbox-icon-envelope-o"></i>'
	),
	'attributes' => array(
		'type' => 'email'
	)
));

$xbox->add_field(array(
	'id' => 'activation-type',
	'name' => __( 'Activation type', 'masterpopups' ),
	'type' => 'radio',
	'default' => 'activation',
	'items' => array(
		'activation' => __( 'Activation', 'masterpopups' ),
		'deactivation' => __( 'Deactivation', 'masterpopups' ),
	),
	'options' => array(
		'show_name' => true,
		'helper' => '<i class="xbox-icon xbox-icon-envelope-o"></i>'
	),
));

$xbox->add_field(array(
	'id' => 'activation-domain',
	'name' => __( 'Domain', 'masterpopups' ),
	'type' => 'text',
	'default' => '',
	'desc' => __( 'Enter the domain in which you want to deactivate the plugin', 'masterpopups' ),
	'grid' => '4-of-8',
	'options' => array(
		'show_name' => true,
		'helper' => '<i class="xbox-icon xbox-icon-globe"></i>',
		'show_if' => array( 'activation-type', 'deactivation' )
	),
	'attributes' => array(
		'placeholder' => 'site.com'
	)
));

$xbox->add_field(array(
	'id' => 'activation-validate-purchase',
	'name' => '',
	'type' => 'button',
	'content' => __( 'Validate Purchase', 'masterpopups' ),
	'desc' => '',
	'options' => array(
		'show_name' => true,
		'color' => 'teal',
	),
	'attributes' => array(
		//'placeholder' => 'site.com'
	)
));