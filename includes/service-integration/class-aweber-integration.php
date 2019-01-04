<?php namespace MasterPopups\Includes\ServiceIntegration;

use \AWeberAPI as AWeberAPI;

class AweberIntegration extends ServiceIntegration {
	private $consumerKey = '';
	private $consumerSecret = '';
	private $accessKey  = '';
	private $accessSecret = '';
	private $account = null;

	/*
	|---------------------------------------------------------------------------------------------------
	| Constructor
	|---------------------------------------------------------------------------------------------------
	*/
	public function __construct( $api_key = '' ){
		$this->api_key = $api_key;
		try {
			$credentials = AWeberAPI::getDataFromAweberID( $this->api_key );
			update_option( 'mpp_aweber_credentials' , $credentials );
		} catch( \Exception $e ){
			$this->error = $e->getMessage();
		}
	}

	/*
	|---------------------------------------------------------------------------------------------------
	| Comprueba si la conexión con el servicio es exitosa
	|---------------------------------------------------------------------------------------------------
	*/
	public function is_connect(){
		$credentials = get_option( 'mpp_aweber_credentials' );
		if( $credentials && is_array( $credentials ) && count( $credentials ) == 4 ){
			$this->consumerKey = $credentials[0];
			$this->consumerSecret = $credentials[1];
			$this->accessKey = $credentials[2];
			$this->accessSecret = $credentials[3];
			try {
			  $this->service = new AWeberAPI( $this->consumerKey, $this->consumerSecret );
			  $this->account = $this->service->getAccount( $this->accessKey, $this->accessSecret );
			  if( isset( $this->account->data['id'] ) ){
			  	$this->error = '';
			  	return true;
			  }
			} catch( \Exception $e ){
				$this->error = $e->getMessage();
			}
		}
		return false;
	}


	/*
	|---------------------------------------------------------------------------------------------------
	| Retorna todas las listas
	|---------------------------------------------------------------------------------------------------
	*/
	public function get_lists(){
		$items = array();
		try {
		  $lists = $this->account->lists;
			if( count( $lists ) > 0 ){
				foreach( $lists as $key => $list ){
					$items[$list->id] = $list->name;
				}
			}
		} catch( \Exception $e ){
			$this->error = $e->getMessage();
		}
		return $items;
	}

	/*
	|---------------------------------------------------------------------------------------------------
	| Agrega un suscriptor a una lista
	|---------------------------------------------------------------------------------------------------
	*/
	public function add_subscriber( $email, $data = array() ){
		$first_name = $data['first_name'];
		$first_name['value'] = ! empty( $first_name['value'] ) ? $first_name['value'] : '';
		$first_name['name'] = ! empty( $first_name['name'] ) ? $first_name['name'] : 'name';

		$last_name = $data['last_name'];
		$last_name['value'] = ! empty( $last_name['value'] ) ? $last_name['value'] : '';
		$last_name['name'] = ! empty( $last_name['name'] ) ? $last_name['name'] : '';

		//Datos necesarios para la suscripción
		$params = array();
		$params['email'] = $email;
		$params['name'] = trim( $first_name['value'] . ' '. $last_name['value'] );
		$params['ip_address'] = $_SERVER['REMOTE_ADDR'];
		$params['custom_fields'] = array();

		//Los campos que no existan serán ignorados.
		if( ! empty( $data['custom_fields'] ) ){
			foreach( $data['custom_fields'] as $cf_name => $cf_value ){
				$params['custom_fields'][$cf_name] = $cf_value;
			}
		}

		//Eliminar parámetro 'custom_fields' si está vacío porque la api da error.
		if( empty( $params['custom_fields'] ) ){
			unset( $params['custom_fields'] );
		}

		//Suscribir nuevo usuario
		try {
      $url = "/accounts/{$this->account->id}/lists/{$this->list_id}";
			$list = $this->account->loadFromUrl( $url );
			$list->subscribers->create( $params );
      return true;
    } catch( \Exception $e ){
      $this->error = $e->getMessage();
      return false;
    }
		return false;
	}

	/*
	|---------------------------------------------------------------------------------------------------
	| Retorna todos los campos personalizados
	|---------------------------------------------------------------------------------------------------
	*/
	public function get_custom_fields(){
		$items = array();
		try {
			$url = "/accounts/{$this->account->id}/lists/{$this->list_id}/custom_fields";
			$fields = $this->account->loadFromUrl( $url );
			if( count( $fields ) > 0 ){
				foreach( $fields as $key => $field ){
					$items[$field->id] = $field->name;
				}
			}
		} catch( \Exception $e ){
		}
		return $items;
	}



}

