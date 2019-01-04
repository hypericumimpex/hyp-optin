<?php namespace MasterPopups\Includes\ServiceIntegration;

use MaxLopez\HTTPClientWP\IronMan;

class HubspotIntegration extends ServiceIntegration {
    private $api_endpoint = 'https://api.hubapi.com/contacts/v1';

    /*
    |---------------------------------------------------------------------------------------------------
    | Constructor
    |---------------------------------------------------------------------------------------------------
    */
    public function __construct( $api_key ){
        $this->api_key = trim( $api_key );

        $this->ironman = new IronMan( $this->api_endpoint );
        $this->ironman->set_option( 'reset_body_after_request', true );
        $this->ironman->set_headers( array(
            'User-Agent' => 'MasterPopups plugin',
            'Content-Type' => 'application/json; charset=utf-8',
        ) );
    }

    public function new_request( $method, $url, $body = array(), $headers = array(), $options = array() ){
        $hapikey = rawurlencode( $this->api_key );
        if( $method == 'GET' ){
            return parent::new_request( $method, $url, array_merge( $body, array( 'hapikey' => $hapikey ) ), $headers, $options );
        } else{
            return parent::new_request( $method, $url . "?hapikey=$hapikey", $body, $headers, $options );
        }
    }

    /*
    |---------------------------------------------------------------------------------------------------
    | Comprueba si la conexión con el servicio es exitosa
    |---------------------------------------------------------------------------------------------------
    */
    public function is_connect(){
        $success = $this->new_request( "GET", "/lists", array( 'count' => 200, 'offset' => 0 ) );
        if( $this->ironman->response_code == 401 ){
            return false;
        }
        return $success;
    }

    /*
    |---------------------------------------------------------------------------------------------------
    | Retorna todas las listas
    |---------------------------------------------------------------------------------------------------
    */
    public function get_lists( $args = array() ){
        $success = $this->new_request( "GET", "/lists", array( 'count' => 200, 'offset' => 0 ) );
        if( ! $success ){
            return array();
        }
        $response = json_decode( $this->ironman->get_response_body(), true );
        $lists = isset( $response['lists'] ) ? $response['lists'] : array();
        foreach( $lists as $list ){
            $list_type = $list['dynamic'] ? 'Dynamic' : 'Static';
            $items[$list['listId']] = $list['name']." ($list_type)";
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
        $first_name['name'] = ! empty( $first_name['name'] ) ? $first_name['name'] : 'firstname';

        $last_name = $data['last_name'];
        $last_name['value'] = ! empty( $last_name['value'] ) ? $last_name['value'] : '';
        $last_name['name'] = ! empty( $last_name['name'] ) ? $last_name['name'] : 'lastname';

        //Datos necesarios para la suscripción
        $params = array();
        $params[] = array(
            'property' => 'email',
            'value' => $email
        );
        $params[] = array(
            'property' => $first_name['name'],
            'value' => $first_name['value'],
        );
        $params[] = array(
            'property' => $last_name['name'],
            'value' => $last_name['value']
        );

        if( ! empty( $data['custom_fields'] ) ){
            $custom_fields = $this->get_custom_fields();
            foreach( $custom_fields as $cf_id => $cf_name ){
                if( isset( $data['custom_fields'][$cf_name] ) ){
                    $params[] = array(
                        'property' => $cf_name,
                        'value' => $data['custom_fields'][$cf_name]
                    );
                }
            }
        }

        //Creamos nuevo usuario o actualizamos datos
        $this->ironman->set_option( 'encode_body', true );//La petición requiere datos en formato json
        $success = $this->new_request( "POST", "/contact/createOrUpdate/email/$email", array( 'properties' => $params ) );
        if( ! $success ){
            return false;
        }
        $response = json_decode( $this->ironman->get_response_body(), true );
        $subscriber_id = $response['vid'];

        //Comprobamos si es una lista estática. (No se puede suscribir a listas dinámicas o activas)
        $success = $this->new_request( "GET", "/lists/$this->list_id" );
        if( ! $success ){
            return false;
        }
        $response = json_decode( $this->ironman->get_response_body(), true );
        if( isset( $response['dynamic'] ) && $response['dynamic'] == true ){
            $this->error = 'You cannot add contacts to dynamic lists. Please choose a static list.';
            return false;
        }

        //Suscribir nuevo usuario
        $success = $this->new_request( "POST", "/lists/$this->list_id/add", array( 'vids' => array( $subscriber_id ) ) );
        return $success;
    }

    /*
    |---------------------------------------------------------------------------------------------------
    | Retorna todos los campos por defecto
    |---------------------------------------------------------------------------------------------------
    */
    public function get_default_fields(){
        return array(
            'firstname',
            'lastname',
        );
    }

    /*
    |---------------------------------------------------------------------------------------------------
    | Retorna todos los campos personalizados
    |---------------------------------------------------------------------------------------------------
    */
    public function get_custom_fields(){
        $items = array();
        $success = $this->new_request( "GET", "/properties" );
        if( ! $success ){
            return array();
        }
        $fields = json_decode( $this->ironman->get_response_body(), true );
        foreach( $fields as $field ){
            $items[] = $field['name'];
        }
        return $items;
    }

}
