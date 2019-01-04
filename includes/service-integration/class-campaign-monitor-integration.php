<?php namespace MasterPopups\Includes\ServiceIntegration;

use MaxLopez\HTTPClientWP\IronMan;

class CampaignMonitorIntegration extends ServiceIntegration {

    private $client_id = '';
    private $api_endpoint = 'https://api.createsend.com/api/v3.1';

    /*
    |---------------------------------------------------------------------------------------------------
    | Constructor
    |---------------------------------------------------------------------------------------------------
    */
    public function __construct( $api_key, $client_id ){
        $this->api_key = trim( $api_key );
        $this->client_id = trim( $client_id );

        $this->ironman = new IronMan( $this->api_endpoint );
        $basic_auth = base64_encode( $this->api_key . ':nopass' );
        $this->ironman->headers = array(
            'Content-Type' => 'application/json; charset=utf-8',
            'Authorization' => "Basic $basic_auth",
        );
    }

    /*
    |---------------------------------------------------------------------------------------------------
    | Comprueba si la conexión con el servicio es exitosa
    |---------------------------------------------------------------------------------------------------
    */
    public function is_connect(){
        $success = $this->new_request( "GET", "/clients/{$this->client_id}/lists.json" );
        return $success;
    }

    /*
    |---------------------------------------------------------------------------------------------------
    | Retorna todas las listas
    |---------------------------------------------------------------------------------------------------
    */
    public function get_lists(){
        $items = array();
        $success = $this->new_request( "GET", "/clients/{$this->client_id}/lists.json" );
        if( ! $success ){
            return array();
        }
        $lists = json_decode( $this->ironman->get_response_body() );
        foreach( $lists as $list ){
            $items[$list->ListID] = $list->Name;
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
        $first_name['name'] = ! empty( $first_name['name'] ) ? $first_name['name'] : 'Name';

        $last_name = $data['last_name'];
        $last_name['value'] = ! empty( $last_name['value'] ) ? $last_name['value'] : '';
        $last_name['name'] = ! empty( $last_name['name'] ) ? $last_name['name'] : '';

        //Datos necesarios para la suscripción
        $params = array();
        $params['EmailAddress'] = $email;
        $params['Name'] = trim( $first_name['value'] . ' ' . $last_name['value'] );
        //$params[$last_name['name']] = $last_name['value'];//No tiene last name
        $params['Resubscribe'] = true;
        $params['RestartSubscriptionBasedAutoresponders'] = true;
        $params['CustomFields'] = array();

        if( ! empty( $data['custom_fields'] ) ){
            $custom_fields = $this->get_custom_fields();
            foreach( $data['custom_fields'] as $cf_name => $cf_value ){
                if( $this->isset_field( $cf_name, $custom_fields, false ) ){
                    $params['CustomFields'][] = array(
                        'Key' => $cf_name,
                        'Value' => $cf_value,
                    );
                }
            }
        }

        //Suscribir nuevo usuario
        $this->ironman->set_option( 'encode_body', true );//La petición requiere datos en formato json
        $success = $this->new_request( "POST", "/subscribers/{$this->list_id}.json", $params );
        return $success;
    }

    /*
    |---------------------------------------------------------------------------------------------------
    | Retorna todos los campos personalizados
    |---------------------------------------------------------------------------------------------------
    */
    public function get_custom_fields(){
        $items = array();
        $success = $this->new_request( "GET", "/lists/{$this->list_id}/customfields.json" );
        if( ! $success ){
            return array();
        }
        $fields = json_decode( $this->ironman->get_response_body() );
        foreach( $fields as $field ){
            $items[] = trim( $field->Key, '[ ]' );
        }
        return $items;
    }

}
