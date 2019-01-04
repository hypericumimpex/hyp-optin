<?php namespace MasterPopups\Includes;

use MasterPopups\Includes\ServiceIntegration\MailerLiteIntegration;

class PluginLoader {
	public $plugin;
	protected static $instance = null;

	/*
	|---------------------------------------------------------------------------------------------------
	| Constructor
	|---------------------------------------------------------------------------------------------------
	*/
	private function __construct( $plugin ) {
		$this->plugin = $plugin;

		$this->constants();
		$this->hooks();
		$this->general_files();
		$this->assets_loader();
		$this->ajax();
	}

	/*
	|---------------------------------------------------------------------------------------------------
	| Singleton
	|---------------------------------------------------------------------------------------------------
	*/
	private function __clone(){}//Stopping Clonning of Object
	private function __wakeup(){}//Stopping unserialize of object

	public static function get_instance( $plugin = null ) {
		if ( null === self::$instance ) {
			self::$instance = new self( $plugin );
		}
		return self::$instance;
	}

	/*
	|---------------------------------------------------------------------------------------------------
	| Constants
	|---------------------------------------------------------------------------------------------------
	*/
	public function constants(){
		define( 'MPP_VERSION',    $this->plugin->arg( 'version' ) );
		define( 'MPP_SLUG',       $this->plugin->arg( 'slug' ) );
		define( 'MPP_TEXT_DOMAIN', $this->plugin->arg( 'text_domain' ) );
		define( 'MPP_DIR',        trailingslashit( dirname( dirname( __FILE__ ) ) ) );
		define( 'MPP_URL',        trailingslashit( plugins_url( '', dirname( __FILE__ ) ) ) );
	}

	/*
	|---------------------------------------------------------------------------------------------------
	| Hooks
	|---------------------------------------------------------------------------------------------------
	*/
	private function hooks(){
		add_action( 'wp_loaded', array( $this, 'load_files_on_plugins_loaded' ) );

	}


	/*
	|---------------------------------------------------------------------------------------------------
	| General files
	|---------------------------------------------------------------------------------------------------
	*/
	public function general_files(){
		include MPP_DIR . 'includes/global-functions.php';
		include MPP_DIR . 'includes/options/popup-editor/popup-editor-functions.php';
		include MPP_DIR . 'libs/Mobile-Detect/Mobile_Detect.php';
		include MPP_DIR . 'libs/plugin-update-checker/plugin-update-checker.php';
        include MPP_DIR . 'libs/IronMan/class-iron-man.php';
	}

	/*
	|---------------------------------------------------------------------------------------------------
	| Load files on "wp_loaded" hook
	|---------------------------------------------------------------------------------------------------
	*/
	public function load_files_on_plugins_loaded(){
		include MPP_DIR . 'includes/debug.php';
	}

	/*
	|---------------------------------------------------------------------------------------------------
	| Assets Loader
	|---------------------------------------------------------------------------------------------------
	*/
	public function assets_loader(){
		AssetsLoader::get_instance( $this->plugin );
	}

	/*
	|---------------------------------------------------------------------------------------------------
	| Ajax
	|---------------------------------------------------------------------------------------------------
	*/
	public function ajax(){
		Ajax::get_instance( $this->plugin );
	}



}
