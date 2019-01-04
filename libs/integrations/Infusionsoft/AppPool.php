<?php
/**
 * Class Mpp_Infusionsoft_AppPool
 */
class Mpp_Infusionsoft_AppPool{
	static protected $apps = array();
	static protected $defaultTokenStorageProvider = null;

	public function __construct(){
	}

    /**
     * @param string $appHostname
     * @return Mpp_Infusionsoft_App
     */

    public static function defaultAppHostname(){
        return self::$apps['default']->getHostname();
    }

    public static function getApp($appHostname = ''){
		$appKey = strtolower($appHostname);
		if($appKey == '') $appKey = 'default';
		if(array_key_exists($appKey, self::$apps)){
			return self::$apps[$appKey];
		} else{
			return null;
		}
	}

    /**
     * @param $app
     * @param null $appKey
     * @return Mpp_Infusionsoft_App
     */
    public static function addApp(Mpp_Infusionsoft_App $app, $appKey = null){
		if(count(self::$apps) == 0){
			self::$apps['default'] = $app;
		}
		if($appKey == null){
			$appKey = $app->getHostname();
		}
		self::$apps[strtolower($appKey)] = $app;
        return $app;
	}

    public static function clearApps(){
        self::$apps = array();
    }

    /**
     * Add an Mpp_Infusionsoft_App to the app pool (If necessary) and set it as the default app.
     * @param Mpp_Infusionsoft_App $app
     * @param null $appKey
     * @return Mpp_Infusionsoft_App The Mpp_Infusionsoft_App added as the default
     */
    public static function setDefaultApp(Mpp_Infusionsoft_App $app, $appKey = null) {
        $existingApp = self::getApp($app->getHostname());
        $app = ($existingApp == null) ? self::addApp($app, $appKey) : $existingApp;
        if (self::$apps['default'] != $app) {
            self::$apps['default'] = $app;
        }
        return self::$apps['default'];
    }

    public static function getDefaultStorageProvider(){
        if(static::$defaultTokenStorageProvider == null){
            static::$defaultTokenStorageProvider = new Mpp_Infusionsoft_SimpleJsonFileTokenStorageProvider();
        }
        return static::$defaultTokenStorageProvider;
    }

    public static function setDefaultStorageProvider(Mpp_Infusionsoft_TokenStorageProvider $storageProvider){
        static::$defaultTokenStorageProvider = $storageProvider;
    }
}