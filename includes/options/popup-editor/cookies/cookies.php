<?php
$xbox->add_field(array(
    'id' => 'custom-cookie-on-close',
    'name' => __( 'Set Custom Cookie on Close Popup', 'masterpopups' ),
    'desc' => __( 'Enter the cookie name you want to add when closing the popup. First you must create it here below in "Custom cookies".', 'masterpopups'),
    'type' => 'text',
    'default' => '',
    'attributes' => array(
        'style' => 'width:300px',
    )
));

$cookies = $xbox->add_group(array(
    'id' => 'custom-cookies',
    'name' => __( 'Custom Cookies', 'masterpopups' ),
    'options' => array(
        'sortable' => false,
    ),
    'controls' => array(
        'name' => 'Cookie #',
        'left_actions' => array(
            'xbox-info-order-item' => '#',
            'xbox-sort-group-item' => '',
        ),
        'right_actions' => array(
            'xbox-duplicate-group-item' => '',
            'xbox-visibility-group-item' => '',
            //'xbox-remove-group-item' => '',
        ),
    ),
));
$cookies->add_field(array(
    'id' => 'name',
    'name' => __( 'Cookie name', 'masterpopups' ),
    'type' => 'text',
    'desc' => 'E.g: cookie_name',
    'attributes' => array(
        'style' => 'width:300px',
    )
));
$cookies->open_mixed_field(array(
    'name' => __( 'Cookie settings', 'masterpopups' ),
    'desc' => __( 'This cookie must be enabled for it to work.', 'masterpopups' ),
));
$cookies->add_field(array(
    'id' => 'enable',
    'name' => __( 'Enable', 'masterpopups' ),
    'type' => 'switcher',
    'default' => 'on',
    'options' => array(
        'desc_tooltip' => true,
        //'show_name' => false,
    )
));
$cookies->add_field(array(
    'id' => 'duration',
    'name' => __( 'Cookie duration', 'masterpopups' ),
    'type' => 'radio',
    'default' => 'current_session',
    'items' => array(
        'current_session' => __( 'Current session', 'masterpopups' ),
        'days' => __( 'Define days', 'masterpopups' ),
    ),
));
$cookies->add_field(array(
    'id' => 'days',
    'name' => __( 'Days', 'masterpopups' ),
    'type' => 'number',
    'default' => '7',
    'options' => array(
        'desc_tooltip' => true,
        'show_spinner' => true,
        'unit' => 'days',
        'show_if' => array('duration', '=', 'days' ),
    ),
    'attributes' => array(
        'min' => 1,
    ),
));
$cookies->close_mixed_field();

$cookies->add_field(array(
    'id' => 'behavior',
    'name' => __( 'Cookie behavior', 'masterpopups' ),
    'type' => 'checkbox',
    'default' => '',
    'desc' => __( 'When the cookie exists in the user browser, what do you want to do?', 'masterpopups' ),
    'items' => array(
        'not_show_popup' => __( 'Not show popup', 'masterpopups' ),
    ),
));