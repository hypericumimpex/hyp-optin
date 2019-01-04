<?php namespace MasterPopups\Includes\ServiceIntegration;

use MasterPopups\ActiveCampaign\ActiveCampaign;

class ActiveCampaignIntegration extends ServiceIntegration {
	protected $api_url = '';

	/*
	|---------------------------------------------------------------------------------------------------
	| Constructor
	|---------------------------------------------------------------------------------------------------
	*/
	public function __construct( $api_key = '', $api_url = '' ){
		$this->api_key = $api_key;
		$this->api_url = $api_url;
    $this->service = new ActiveCampaign(  $this->api_url , $this->api_key );
	}

	/*
	|---------------------------------------------------------------------------------------------------
	| Comprueba si la conexión con el servicio es exitosa
	|---------------------------------------------------------------------------------------------------
	*/
	public function is_connect(){
		if( ! $this->service ){
			return false;
		}
    return $this->service->credentials_test();
	}

	/*
	|---------------------------------------------------------------------------------------------------
	| Retorna todas las listas
	|---------------------------------------------------------------------------------------------------
	*/
	public function get_lists(){
		$items = array();
		$lists  = (array) $this->service->api( "list/list_", array(
			'ids' => 'all',
			'full' => '0' //http://www.activecampaign.com/api/example.php?call=list_list
		));
		if( $lists ){
			foreach( $lists as $list ){
				if( is_object( $list ) ){
					$items[$list->id] = $list->name;
				}
			}
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
		$first_name['name'] = ! empty( $first_name['name'] ) ? $first_name['name'] : 'first_name';

		$last_name = $data['last_name'];
		$last_name['value'] = ! empty( $last_name['value'] ) ? $last_name['value'] : '';
		$last_name['name'] = ! empty( $last_name['name'] ) ? $last_name['name'] : 'last_name';

		//Datos necesarios para la suscripción
		$params = array();
		$params['email'] = $email;
		$params[$first_name['name']] = $first_name['value'];
		$params[$last_name['name']] = $last_name['value'];
		$params["p[{$this->list_id}]"] = $this->list_id;
		$params["status[{$this->list_id}]"] = 1;//1: active, 2: unsubscribed

		if( ! empty( $data['custom_fields'] ) ){
			if( isset( $data['custom_fields']['tags'] ) ){
				$params['tags'] = $data['custom_fields']['tags'];
				unset( $data['custom_fields']['tags'] );
			}
			$default_fields = array_map('strtolower', $this->get_default_fields() );
			$custom_fields = array_map('strtolower', $this->get_custom_fields() );
			foreach( $data['custom_fields'] as $cf_name => $cf_value ){
				if( in_array( strtolower( $cf_name ), $default_fields ) ){
					$params[strtolower($cf_name)] = $cf_value;//name debe estar en minúscula para que guarde
				} else if( in_array( strtolower( $cf_name ), $custom_fields ) ){
					$params["field[$cf_name,0]"] = $cf_value;//$cf_name = %FIELD_TAG%
				}
			}
		}

		//Suscribir nuevo usuario
		//Si el suscriptor ya existe en la lista general, sólo lo agrega a la lista actual.
		//Y si ya existe en la lista actual no lo agrega y devuelve 'success' = 0
		$this->response = $this->service->api( "contact/add", $params );

		if( $this->response->success == 1 || isset( $this->response->subscriber_id ) ){
			return true;
		} else {
			$this->error = $this->messages['subscriber_exists'];
			return false;
		}
	}

	/*
	|---------------------------------------------------------------------------------------------------
	| Retorna todos los campos por defecto
	|---------------------------------------------------------------------------------------------------
	*/
	//estos campos deben enviarse sin % al inicio ni al final
	public function get_default_fields(){
		return array(
			'first_name',
			'last_name',
      'phone',
		);
	}

	/*
	|---------------------------------------------------------------------------------------------------
	| Retorna todos los campos personalizados
	|---------------------------------------------------------------------------------------------------
	*/
	//La API solo retorna los creados por el usuario, no los por defecto
	public function get_custom_fields(){
		$items = array();
		$response = $this->service->api("list/field_view?ids=all" );
		if( $response->success == 1 ){
			foreach( get_object_vars( $response ) as $object ){
				if( is_object( $object ) ){
					$items[] = $object->tag;
				}
			}
			return $items;
		}
		return array();
	}

}

