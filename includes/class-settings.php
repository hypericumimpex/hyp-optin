<?php namespace MasterPopups\Includes;

class Settings {
    public $plugin = null;
    public static $options = array();
    public $xbox = null;
    private static $instance = null;

    private function __construct( $plugin = null ){
        $this->plugin = $plugin;
        $this->xbox = xbox_get( $this->plugin->options_manager->mb_settings );
        $this->set_options();
    }

    /*
    |---------------------------------------------------------------------------------------------------
    | Singleton
    |---------------------------------------------------------------------------------------------------
    */
    private function __clone(){
    }//Stopping Clonning of Object

    private function __wakeup(){
    }//Stopping unserialize of object

    public static function init( $plugin = null ){
        if( null === self::$instance ){
            self::$instance = new self( $plugin );
        }
        return self::$instance;
    }

    /*
    |---------------------------------------------------------------------------------------------------
    | Acceso a cualquier opción
    |---------------------------------------------------------------------------------------------------
    */
    public static function option( $option_name = '', $default_value = null ){
        if( isset( self::$options[$option_name] ) ){
            return self::$options[$option_name];
        } else if( $default_value ){
            self::$options[$option_name] = $default_value;
            return self::$options[$option_name];
        }
        return null;
    }

    /*
    |---------------------------------------------------------------------------------------------------
    | Establece las opciones por defecto
    |---------------------------------------------------------------------------------------------------
    */
    private function set_options( $options = array() ){
        $default_options = array(
            'enable-enqueue-popups' => $this->xbox->get_field_value( 'enable-enqueue-popups', 'on' ),
            'show-link-edit-popup' => $this->xbox->get_field_value( 'show-link-edit-popup', 'off' ),
            'mx-record-email-validation' => $this->xbox->get_field_value( 'mx-record-email-validation', 'on' ),
            'custom-css' => $this->xbox->get_field_value( 'custom-css', '' ),
            'custom-javascript' => $this->xbox->get_field_value( 'custom-javascript', '' ),
            'integrated-services' => $this->xbox->get_field_value( 'integrated-services', array() ),
            'load-videojs' => $this->xbox->get_field_value( 'load-videojs', 'on' ),
            'load-google-fonts' => $this->xbox->get_field_value( 'load-google-fonts', 'on' ),
            'load-font-awesome' => $this->xbox->get_field_value( 'load-font-awesome', 'on' ),
            'send-data-to-developer' => $this->xbox->get_field_value( 'send-data-to-developer', 'on' ),
            'popups-z-index' => $this->xbox->get_field_value( 'popups-z-index', '99999999' ),
            'validation-msg-general' => $this->xbox->get_field_value( 'validation-msg-general', __( 'This field is required', 'masterpopups' ) ),
            'validation-msg-email' => $this->xbox->get_field_value( 'validation-msg-email', __( 'Invalid email address', 'masterpopups' ) ),
            'validation-msg-checkbox' => $this->xbox->get_field_value( 'validation-msg-checkbox', __( 'This field is required, please check', 'masterpopups' ) ),
            'validation-msg-dropdown' => $this->xbox->get_field_value( 'validation-msg-dropdown', __( 'This field is required. Please select an option', 'masterpopups' ) ),
            'form-submission-back-to-form-text' => $this->xbox->get_field_value( 'form-submission-back-to-form-text', __( 'Back to form', 'masterpopups' ) ),
            'form-submission-close-popup-text' => $this->xbox->get_field_value( 'form-submission-close-popup-text', __( 'Close', 'masterpopups' ) ),
            'link-powered-by-enabled' => $this->xbox->get_field_value( 'link-powered-by-enabled', 'off' ),
            'link-powered-by-username' => $this->xbox->get_field_value( 'link-powered-by-username', '' ),
        );

        self::$options = wp_parse_args( $options, $default_options );

        return self::$options;
    }

    /*
    |---------------------------------------------------------------------------------------------------
    | Retorna los servicios integrados con su estado conectado o desconectado
    |---------------------------------------------------------------------------------------------------
    */
    public static function get_status_integrated_services(){
        $value = (array) self::option( 'integrated-services' );
        $integrated_services = array();
        foreach( $value as $index => $service ){
            $integrated_services[$service['integrated-services_type']] = $service['service-status'];
        }
        return $integrated_services;
    }

    /*
    |---------------------------------------------------------------------------------------------------
    | Retorna las cookies creadas por el usuario
    |---------------------------------------------------------------------------------------------------
    */
    public static function get_custom_cookies(){
        //        $saved_cookies = self::option('cookies');
        //        $cookies = array();
        //        if (is_array($saved_cookies)) {
        //            foreach ($saved_cookies as $key => $value) {
        //                unset($saved_cookies[$key]['cookies_type'],
        //                    $saved_cookies[$key]['cookies_visibility'],
        //                    $saved_cookies[$key]['cookies_name']);
        //
        //                if (!isset($saved_cookies[$key]['behavior'])) {
        //                    $saved_cookies[$key]['behavior'] = array();
        //                }
        //                $cookies[$value['name']] = $saved_cookies[$key];
        //            }
        //        }
        //        return $cookies;
    }

    /*
    |---------------------------------------------------------------------------------------------------
    | Activación del Plugin
    |---------------------------------------------------------------------------------------------------
    */
    public static function plugin_status(){
        $option = get_option( 'mpp-plugin-status' );
        if( $option && is_array( $option ) ){
            return isset( $option['purchase_code'], $option['user_name'] );
        }
        return true;
    }

    public static function plugin_status_message( $url ){
        return '<div class="ampp-message ampp-message-warning ampp-icon-message">
			<header>' . __( 'License activation required.', 'masterpopups' ) . '</header>
			<p>' . sprintf( __( 'Please activate your license from %shere%s. Tab "Plugin Activation"', 'masterpopups' ), '<a href="' . $url . '" target="_blank">', '</a>' ) . '</p>
	</div>';
    }


}
