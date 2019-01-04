<?php namespace MasterPopups\Includes\ServiceIntegration;

use MasterPopups\Grzeogrz\GetResponse\GetResponseAPI3;

class GetresponseIntegration extends ServiceIntegration {

	/*
	|---------------------------------------------------------------------------------------------------
	| Constructor
	|---------------------------------------------------------------------------------------------------
	*/
	public function __construct( $api_key = '', $api_url = null, $domain = null ){
		$this->api_key = $api_key;

		$this->service = new GetResponseAPI3( $this->api_key, $api_url, $domain );
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
		$response = $this->service->ping();
		if( $response && isset( $response->accountId ) ){
			return true;
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
		$lists = $this->service->getCampaigns();
		if( $lists ){
			foreach( $lists as $list ){
				$items[$list->campaignId] = $list->description;
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
		$first_name['name'] = ! empty( $first_name['name'] ) ? $first_name['name'] : '';

		$last_name = $data['last_name'];
		$last_name['value'] = ! empty( $last_name['value'] ) ? $last_name['value'] : '';
		$last_name['name'] = ! empty( $last_name['name'] ) ? $last_name['name'] : '';

		//Datos necesarios para la suscripción
		$params = array();
		$params['email'] = $email;
		$params['dayOfCycle'] = 0;
		$params['ipAddress'] = $_SERVER['REMOTE_ADDR'];
		$params['campaign'] = array('campaignId' => $this->list_id);
		if( strlen( trim( $first_name['value'] . $last_name['value'] ) ) >= 2 ){
			$params['name'] = $first_name['value'] . ' ' . $last_name['value'];
		}
		$params['customFieldValues'] = array();

		if( ! empty( $data['custom_fields'] ) ){
			$custom_fields = $this->get_custom_fields();
			foreach( $custom_fields as $cf_id => $cf_name ){
				if( isset( $data['custom_fields'][$cf_name] ) ){
					$params['customFieldValues'][] = array(
						'customFieldId' => $cf_id,
        		'value' => array( $data['custom_fields'][$cf_name] )
					);
				}
			}
		}

		//Suscribir nuevo usuario
		$this->response = $this->service->addContact( $params );

		if( $this->service->http_status == 202 || ( isset( $this->response->message ) && $this->response->message == 'Contact in queue' ) ){
			return true;
		} else {
			$this->error = isset( $this->response->message ) ? $this->response->message : '';
		}
		return false;
	}

	/*
	|---------------------------------------------------------------------------------------------------
	| Retorna todos los campos por defecto
	|---------------------------------------------------------------------------------------------------
	*/
	public function get_default_fields(){
		return array(
			'name',
		);
	}

	/*
	|---------------------------------------------------------------------------------------------------
	| Retorna todos los campos personalizados
	|---------------------------------------------------------------------------------------------------
	*/
	public function get_custom_fields(){
		$items = array();
		$response = $this->service->getCustomFields( array( 'perPage' => 100 ) );
		if( $this->service->http_status != 200 ){
			return array();
		}
		if( $response ){
			foreach( $response as $field ){
				$items[$field->customFieldId] = $field->name;
			}
		}
		return $items;
	}



}

