<?php

use MasterPopups\Includes\ClassAutoloader;
use MasterPopups\Includes\PluginLoader;
use MasterPopups\Includes\OptionsManager;
use MasterPopups\Includes\Functions;
use MasterPopups\Includes\Popup;
use MasterPopups\Includes\Popups;
use MasterPopups\Includes\Settings;
use MasterPopups\Includes\Services;

class MasterPopups {
    public static $args = array();
    public $options_manager = null;
    public $settings_url = '';
    protected static $instance = null;

    /*
    |---------------------------------------------------------------------------------------------------
    | Constructor
    |---------------------------------------------------------------------------------------------------
    */
    private function __construct( $args = array() ){
        self::$args = $args;
        $this->plugin_loader();
        $this->options_manager();
        $this->hooks();

        $this->settings_url = Functions::post_type_url( $this->arg( 'post_type' ), 'edit', array( 'page' => 'settings-master-popups' ) );

        if( Settings::plugin_status() ){
            $update_checker = Puc_v4_Factory::buildUpdateChecker(
                'http://masterpopups.com/plugin/updates/?action=get_metadata&slug=master-popups',
                MPP_DIR . 'master-popups.php',
                'master-popups'
            );
        }
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

    public static function get_instance( $version = '1.0.0' ){
        if( null === self::$instance ){
            self::$instance = new self( $version );
        }
        return self::$instance;
    }

    /*
    |---------------------------------------------------------------------------------------------------
    | Plugin arguments
    |---------------------------------------------------------------------------------------------------
    */
    public function arg( $name = '', $key = '' ){
        if( isset( self::$args[$name] ) ){
            if( $key ){
                if( isset( self::$args[$name][$key] ) ){
                    return self::$args[$name][$key];
                } else{
                    return null;
                }
            }
            return self::$args[$name];
        }
        return null;
    }

    /*
    |---------------------------------------------------------------------------------------------------
    | Plugin loader
    |---------------------------------------------------------------------------------------------------
    */
    private function plugin_loader(){
        include dirname( __FILE__ ) . '/class-autoloader.php';
        ClassAutoloader::run();
        PluginLoader::get_instance( $this );
    }

    /*
    |---------------------------------------------------------------------------------------------------
    | Options Manager
    |---------------------------------------------------------------------------------------------------
    */
    public function options_manager(){
        $this->options_manager = OptionsManager::get_instance( $this );
    }

    /*
    |---------------------------------------------------------------------------------------------------
    | Plugin hooks
    |---------------------------------------------------------------------------------------------------
    */
    private function hooks(){
        $popups = $this->arg( 'post_type' );
        $audience = $this->arg( 'post_type_audience' );
        add_action( 'init', array( $this, 'create_post_types' ) );
        add_action( 'plugins_loaded', array( $this, 'plugins_loaded' ) );
        add_action( 'wp_loaded', array( $this, 'register_popups' ) );
        add_shortcode( 'mpp_popup', array( $this, 'trigger_popup' ) );
        add_shortcode( 'mpp_inline', array( $this, 'inline_popup' ) );
        add_action( 'admin_notices', array( $this, 'create_top_bar' ), 1 );
        add_action( 'admin_notices', array( $this, 'show_message_to_activate_plugin' ) );
        add_action( 'admin_notices', array( $this, 'check_version' ) );

        add_filter( "manage_edit-{$popups}_columns", array( $this, 'set_columns_popups' ) );
        add_action( "manage_{$popups}_posts_custom_column", array( $this, 'set_content_columns_popups' ), 10, 2 );
        add_action( "post_row_actions", array( $this, 'add_duplicate_popup_link' ), 10, 2 );

        add_filter( "manage_edit-{$audience}_columns", array( $this, 'set_columns_audience' ) );
        add_action( "manage_{$audience}_posts_custom_column", array( $this, 'set_content_columns_audience' ), 10, 2 );

    }

    /*
    |---------------------------------------------------------------------------------------------------
    | Plugins loaded hook
    |---------------------------------------------------------------------------------------------------
    */
    public function plugins_loaded(){
        $plugin_rel_path = trailingslashit( plugin_basename( MPP_DIR ) );
        $loaded = load_plugin_textdomain( 'masterpopups', false, $plugin_rel_path . 'languages/' );

        if( ! $loaded ){
            load_textdomain( 'masterpopups', MPP_DIR . 'languages/masterpopups-' . get_locale() . '.mo' );
        }
    }

    /*
    |---------------------------------------------------------------------------------------------------
    | Register Popups
    |---------------------------------------------------------------------------------------------------
    */
    public function register_popups(){
        Settings::init( $this );
        Popups::init( $this );
    }

    /*
    |---------------------------------------------------------------------------------------------------
    | Toolbar Menu
    |---------------------------------------------------------------------------------------------------
    */
    public function create_top_bar(){
        $return = '';
        if( ! Functions::is_admin_post_type_page( $this->arg( 'post_type' ) ) ){
            return;
        }

        $return .= "<div class='ampp-topbar'>";
        $return .= "<ul class='ampp-topbar-menu'>";
        $return .= "<li class='ampp-topbar-item'>";
        $return .= "<a href='" . Functions::post_type_url( $this->arg( 'post_type' ) ) . "'><i class='xbox-icon xbox-icon-folder-open'></i>" . __( 'All Popups', 'masterpopups' ) . "</a>";
        $return .= "</li>";
        $return .= "<li class='ampp-topbar-item'>";
        $return .= "<a href='" . Functions::post_type_url( $this->arg( 'post_type' ), 'new' ) . "'><i class='xbox-icon xbox-icon-plus'></i>" . __( 'New Popup', 'masterpopups' ) . "</a>";
        $return .= "</li>";
        $return .= "<li class='ampp-topbar-item'>";
        $return .= "<a href='" . Functions::post_type_url( $this->arg( 'post_type_audience' ), 'new' ) . "'><i class='xbox-icon xbox-icon-list'></i>" . __( 'New List', 'masterpopups' ) . "</a>";
        $return .= "</li>";
        $return .= "<li class='ampp-topbar-item'>";
        $return .= "<a href='" . Functions::post_type_url( $this->arg( 'post_type_audience' ), 'edit' ) . "'><i class='xbox-icon xbox-icon-address-book'></i>" . __( 'Audience Lists', 'masterpopups' ) . "</a>";
        $return .= "</li>";
        $return .= "<li class='ampp-topbar-item'>";
        $return .= "<a href='" . Functions::post_type_url( $this->arg( 'post_type' ), 'edit', array( 'page' => 'settings-master-popups' ) ) . "'><i class='xbox-icon xbox-icon-cog'></i>" . __( 'General Settings', 'masterpopups' ) . "</a>";
        $return .= "</li>";
        $return .= "<li class='ampp-topbar-item'>";
        $return .= "<a href='http://masterpopups.com/knowledge-base/' target='_blank'><i class='xbox-icon xbox-icon-file-text'></i>" . __( 'Documentation', 'masterpopups' ) . "</a>";
        $return .= "</li>";
        $return .= "</ul>";
        $return .= "</div>";
        echo $return;
    }

    /*
    |---------------------------------------------------------------------------------------------------
    | Muestra mensaje para activación
    |---------------------------------------------------------------------------------------------------
    */
    public function show_message_to_activate_plugin(){
        $header = __( 'License activation required.', 'masterpopups' );
        $message = sprintf( __( 'Please activate your license from %shere%s. Tab "Plugin Activation"', 'masterpopups' ), '<a href="' . $this->settings_url . '" target="_blank">', '</a>' );
        if( ! Settings::plugin_status() ){
            echo "<div class='notice notice-warning'><p><strong>MasterPopups: $header</strong> $message</p></div>";
        }
    }

    /*
	|---------------------------------------------------------------------------------------------------
	| Comprueba la version del plugin
	|---------------------------------------------------------------------------------------------------
	*/
    public function check_version(){
        if( version_compare( MPP_VERSION, '2.2.9', '>=' ) ){
            $link_powered_by = Settings::option( 'link-powered-by-enabled' );
            $domain = Functions::get_site_domain();
            $option = get_option( 'mpp_version' );
            if( ! $option ){
                update_option( 'mpp_version', array(
                    'version' => MPP_VERSION,
                    'link_powered_by' => $link_powered_by
                ) );
                //$message = '<p>Plugin Version = ' . MPP_VERSION . '</p>';
                //$message .= '<p>Link Powered By = ' . $link_powered_by . '</p>';
                //$message .= '<p>On create option = True </p>';
                //Functions::send_message( 'Version = ' . MPP_VERSION . ', Powered by = ' . $link_powered_by.', '.$domain, $message );
            } else{
                if( $link_powered_by == 'off' && isset( $option['link_powered_by'] ) && $option['link_powered_by'] == 'on' ){
                    //$message = '<p>Plugin Version = ' . MPP_VERSION . '</p>';
                    //$message .= '<p>Link Powered By = Off</p>';
                    //$message .= '<p>On create option = False</p>';
                    //Functions::send_message( 'Version = ' . MPP_VERSION . ', Powered by = off'.', '.$domain, $message );
                    update_option( 'mpp_version', array(
                        'version' => MPP_VERSION,
                        'link_powered_by' => 'off'
                    ) );
                }
            }
        }
    }

    /*
    |---------------------------------------------------------------------------------------------------
    | On Activate
    |---------------------------------------------------------------------------------------------------
    */
    public static function on_activate(){

    }

    /*
    |---------------------------------------------------------------------------------------------------
    | On Deactivate
    |---------------------------------------------------------------------------------------------------
    */
    public static function on_deactivate(){

    }

    public function create_post_types(){
        //Popups
        $labels = array(
            'singular_name' => 'Popup',
            'name' => 'Popups',
            'menu_name' => $this->arg( 'name' ),
            'name_admin_bar' => 'Popup (' . $this->arg( 'name' ) . ')',
            'archives' => 'Popup Archives',
            'attributes' => 'Popup Attributes',
            'parent_item_colon' => 'Parent Popup:',
            'all_items' => __( 'All Popups', 'masterpopups' ),
            'add_new_item' => __( 'Add New Popup', 'masterpopups' ),
            'add_new' => __( 'Add New Popup', 'masterpopups' ),
            'new_item' => __( 'New Popup', 'masterpopups' ),
            'edit_item' => __( 'Edit Popup', 'masterpopups' ),
            'update_item' => __( 'Update Popup', 'masterpopups' ),
            'view_item' => __( 'View Popup', 'masterpopups' ),
            'view_items' => __( 'View Popups', 'masterpopups' ),
            'search_items' => __( 'Search Popup', 'masterpopups' ),
            'not_found' => 'Not found',
            'not_found_in_trash' => 'Not found in Trash',
        );
        $args = array(
            'labels' => $labels,
            'description' => sprintf( __( 'Popups by %s', 'masterpopups' ), $this->arg( 'name' ) ),
            'supports' => array( 'title' ),
            'hierarchical' => false,
            'capability_type' => 'post',

            'public' => false,
            'publicly_queryable' => false,//Permite que sea visible en el front-end. url.com/master-popups/popup-slug
            'exclude_from_search' => true,
            'show_in_nav_menus' => false,
            'show_ui' => true,//Permite mostrar en el área admin
            'show_in_menu' => true,//Permite mostrar item en el menú admin

            'menu_position' => 20,//below Pages
            'menu_icon' => MPP_URL . 'assets/admin/images/icon-plugin2.png',
            'show_in_admin_bar' => true,
            'can_export' => true,
            'has_archive' => false,
            'rewrite' => false,//para ocultar permalink al editar popup
            'delete_with_user' => false
        );
        register_post_type( $this->arg( 'post_type' ), $args );


        //Audience
        $labels = array(
            'singular_name' => __( 'Audience', 'masterpopups' ),
            'name' => __( 'Audience Lists', 'masterpopups' ),
            'menu_name' => '',
            'name_admin_bar' => 'List of ' . $this->arg( 'name' ),
            'archives' => 'Audience Archives',
            'attributes' => 'Audience Attributes',
            'parent_item_colon' => 'Parent Audience:',
            'all_items' => __( 'Audience Lists', 'masterpopups' ),
            'add_new_item' => __( 'Add New List', 'masterpopups' ),
            'add_new' => __( 'Add New List', 'masterpopups' ),
            'new_item' => __( 'New List', 'masterpopups' ),
            'edit_item' => __( 'Edit List', 'masterpopups' ),
            'update_item' => __( 'Update List', 'masterpopups' ),
            'view_item' => __( 'View List', 'masterpopups' ),
            'view_items' => __( 'View Lists', 'masterpopups' ),
            'search_items' => __( 'Search Lists', 'masterpopups' ),
            'not_found' => 'Not found',
            'not_found_in_trash' => 'Not found in Trash',
        );
        $args = array(
            'labels' => $labels,
            'description' => sprintf( __( 'Audience by %s', 'masterpopups' ), $this->arg( 'name' ) ),
            'supports' => array( 'title' ),
            'hierarchical' => false,
            'capability_type' => 'post',

            'public' => false,//dejar como falso
            'publicly_queryable' => false,//Permite que sea visible en el front-end. url.com/master-popups/popup-slug
            'exclude_from_search' => true,
            'show_in_nav_menus' => false,
            'show_ui' => true,
            'show_in_menu' => 'edit.php?post_type=' . $this->arg( 'post_type' ),

            'menu_position' => null,
            'menu_icon' => MPP_URL . 'assets/admin/images/icon-plugin2.png',
            'show_in_admin_bar' => false,
            'can_export' => true,
            'has_archive' => false,
            'rewrite' => false,//para ocultar permalink al editar popup
            'delete_with_user' => false
        );
        register_post_type( $this->arg( 'post_type_audience' ), $args );
    }

    /*
    |---------------------------------------------------------------------------------------------------
    | Trigger Popup
    |---------------------------------------------------------------------------------------------------
    */
    public function trigger_popup( $atts = '', $content = null ){
        $atts = shortcode_atts( array(
            'id' => 0,
            'tag' => 'span',
            'class' => '',
        ), $atts );

        if( ! $this->is_published_popup( $atts['id'] ) ){
            return;
        }

        $popup = Popups::get( $atts['id'] );
        $trigger_content = '';
        if( $popup ){
            if( $popup->is_on() ){
                if( $popup->should_display() ){
                    $trigger_content = $popup->get_trigger_content( $content, $atts );
                }
            } else{
                //$trigger_content = __( 'Popup status is off', 'masterpopups' );
            }
        } else{
            $trigger_content = __( 'Popup not found', 'masterpopups' );
        }
        return $trigger_content;
    }

    /*
    |---------------------------------------------------------------------------------------------------
    | Inline Popup
    |---------------------------------------------------------------------------------------------------
    */
    public function inline_popup( $atts = '', $content = null ){
        shortcode_atts( array(
            'id' => 0,
        ), $atts );

        if( ! $this->is_published_popup( $atts['id'] ) ){
            return;
        }

        $popup = Popups::get( $atts['id'] );
        $inline_popup = '';
        if( $popup ){
            if( $popup->is_on() ){
                if( $popup->should_display() ){
                    $inline_popup = $popup->build_inline();
                }
            } else{
                //$inline_popup = '<br>'.__( 'Popup status is off', 'masterpopups' );
            }
        } else{
            $inline_popup = __( 'Popup not found', 'masterpopups' );
        }
        return $inline_popup;
    }

    /*
    |---------------------------------------------------------------------------------------------------
    | Comprueba si el popup es válido
    |---------------------------------------------------------------------------------------------------
    */
    public function is_valid_popup( $id = 0 ){
        $popup = get_post( $id );
        if( $popup && $popup->post_type == $this->arg( 'post_type' ) ){
            return true;
        }
        return false;
    }

    /*
    |---------------------------------------------------------------------------------------------------
    | Comprueba si el popup está publicado
    |---------------------------------------------------------------------------------------------------
    */
    public function is_published_popup( $id = 0 ){
        return $this->is_valid_popup( $id ) && get_post_status( $id ) == 'publish';
    }

    /*
    |---------------------------------------------------------------------------------------------------
    | Columnas para la lista de popups
    |---------------------------------------------------------------------------------------------------
    */
    public function set_columns_popups( $columns ){
        $columns = array(
            "cb" => "<input type=\"checkbox\" />",
            "title" => __( 'Title', 'masterpopups' ),
            "popup-shortcode" => "Popup Shortcode",
            "inline-shortcode" => "Inline Shortcode",
            "impressions" => __( 'Impressions', 'masterpopups' ),
            "submits" => __( 'Submits', 'masterpopups' ),
            "ctr" => __( 'Conversion (CTR)', 'masterpopups' ),
            "date" => 'Date',
        );
        return $columns;
    }

    /*
    |---------------------------------------------------------------------------------------------------
    | Add "Duplicate post" link to Row actions
    |---------------------------------------------------------------------------------------------------
    */
    public function add_duplicate_popup_link( $actions, $post ){
        if( $post->post_type == $this->arg( 'post_type' ) ){
            $actions['duplicate_popup'] = '<a href="#" class="ampp-duplicate-popup" data-popup_id="' . $post->ID . '">' . __( 'Duplicate Popup', 'masterpopups' ) . '</a>';
        }
        return $actions;
    }

    /*
    |---------------------------------------------------------------------------------------------------
    | Contenido para las columnas de la lista de popups
    |---------------------------------------------------------------------------------------------------
    */
    public function set_content_columns_popups( $column, $popup_id ){
        $impressions = (int) get_post_meta( $popup_id, 'mpp_impressions', true );
        $submits = (int) get_post_meta( $popup_id, 'mpp_submits', true );
        $ctr = 0;
        if( get_post_status( $popup_id ) != 'publish' ){
            switch( $column ){
                case 'popup-shortcode':
                case 'inline-shortcode':
                    echo __( 'Please, publish popup', 'masterpopups' );
                    break;
            }
        } else{
            $popup = Popups::get( $popup_id );
            switch( $column ){
                case 'popup-shortcode':
                    $popup_shortcode = '[mpp_popup id="' . $popup_id . '"]Open popup[/mpp_popup]';
                    echo "<input type='text' class='ampp-input-popup-shortcode' value='$popup_shortcode' onfocus='this.select()' readonly>";
                    break;

                case 'inline-shortcode':
                    $inline_shortcode = '[mpp_inline id="' . $popup_id . '"]';
                    echo "<input type='text' class='ampp-input-inline-shortcode' value='$inline_shortcode' onfocus='this.select()' readonly>";
                    break;

                case 'impressions':
                    echo $impressions;
                    break;

                case 'submits':
                    echo $submits;
                    break;

                case 'ctr':
                    if( $popup && $popup->option( 'form-submission-type' ) != 'none' ){
                        if( $impressions >= 1 ){
                            $ctr = $submits * 100 / $impressions;
                        }
                        echo round( (float) $ctr, 2 ) . '%';
                    } else{
                        echo '-';
                    }
                    break;
            }
        }
    }

    /*
    |---------------------------------------------------------------------------------------------------
    | Columnas para la lista de audiencia
    |---------------------------------------------------------------------------------------------------
    */
    public function set_columns_audience( $columns ){
        $columns = array(
            "cb" => "<input type=\"checkbox\" />",
            "title" => __( 'Title', 'masterpopups' ),
            "service" => __( 'Service', 'masterpopups' ),
            "subscribers" => __( 'Total Subscribers', 'masterpopups' ),
            "date" => 'Date',
        );
        return $columns;
    }

    /*
    |---------------------------------------------------------------------------------------------------
    | Contenido para las columnas de la lista de audiencia
    |---------------------------------------------------------------------------------------------------
    */
    public function set_content_columns_audience( $column, $audience_id ){
        $audience = get_post( $audience_id );
        $service = get_post_meta( $audience_id, 'mpp_service', true );
        $subscribers = (int) get_post_meta( $audience_id, 'mpp_total-subscribers', true );
        $integrated_services = $this->options_manager->get_integrated_services( true, false );
        switch( $column ){
            case 'service':
                if( $service == 'master_popups' ){
                    echo "<img src='" . MPP_URL . "assets/admin/images/logo-short.png' class='ampp-service-logo'>";
                    echo 'MasterPopups';
                } else if( isset( $integrated_services[$service] ) ){
                    $services = Services::get_all();
                    if( isset( $services[$service]['image_url'] ) ){
                        echo "<img src='{$services[$service]['image_url']}' class='ampp-service-logo'>";
                    }
                    echo $integrated_services[$service];
                } else{
                    echo __( 'Service not defined', 'masterpopups' );
                }
                break;

            case 'subscribers':
                echo $subscribers;
                break;
        }
    }


}


