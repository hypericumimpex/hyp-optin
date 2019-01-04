<?php namespace MasterPopups\Includes\ServiceIntegration;

use MasterPopups\DrewM\MailChimp\MailChimp;

class MailchimpIntegration extends ServiceIntegration {

    /*
    |---------------------------------------------------------------------------------------------------
    | Constructor
    |---------------------------------------------------------------------------------------------------
    */
    public function __construct( $api_key = '' ){
        $this->api_key = $api_key;

        try{
            $this->service = new MailChimp( $this->api_key );
            //$this->service->verify_ssl = true;
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
        if( ! $this->service ){
            return false;
        }
        $response = $this->service->get( '' );
        if( is_array( $response ) && ! empty( $response ) ){
            if( isset( $response['account_id'] ) ){
                return true;
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
        $lists = $this->service->get( 'lists', array( 'count' => 50 ) );
        $items = array();
        if( $lists['total_items'] >= 1 ){
            foreach( $lists['lists'] as $list ){
                $items[$list['id']] = $list['name'];
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
        $first_name['name'] = ! empty( $first_name['name'] ) ? $first_name['name'] : 'FNAME';

        $last_name = $data['last_name'];
        $last_name['value'] = ! empty( $last_name['value'] ) ? $last_name['value'] : '';
        $last_name['name'] = ! empty( $last_name['name'] ) ? $last_name['name'] : 'LNAME';

        //Datos necesarios para la suscripción
        $params = array();
        $params['email_address'] = $email;
        $params['status'] = isset( $data['double-opt-in'] ) && $data['double-opt-in'] == 'on' ? 'pending' : 'subscribed';
        $params['merge_fields'] = array();

        if( ! empty( $first_name['value'] ) ){
            $params['merge_fields'][$first_name['name']] = $first_name['value'];
        }

        if( ! empty( $last_name['value'] ) ){
            $params['merge_fields'][$last_name['name']] = $last_name['value'];
        }

        if( ! empty( $data['custom_fields'] ) ){
            $custom_fields = $this->get_custom_fields();
            foreach( $custom_fields as $cf_id => $cf_name ){
                $cf_name_lower = strtolower( $cf_name );
                if( isset( $data['custom_fields'][$cf_name] ) ){
                    $params['merge_fields'][$cf_name] = $data['custom_fields'][$cf_name];
                } elseif( isset( $data['custom_fields'][$cf_name_lower] ) ){
                    $params['merge_fields'][$cf_name] = $data['custom_fields'][$cf_name_lower];
                }
            }
        }

        //Eliminar parámetro 'merge_fields' si está vacío porque la api da error.
        if( empty( $params['merge_fields'] ) ){
            unset( $params['merge_fields'] );
        }

        //Suscribir nuevo usuario
        $this->response = $this->service->post( "lists/{$this->list_id}/members", $params );

        if( $this->service->success() ){
            return true;
        } else{
            $this->error = isset( $this->response['title'] ) ? $this->response['title'] : $this->service->getLastError();
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
        $response = $this->service->get( "lists/{$this->list_id}/merge-fields", array( 'count' => 100 ) );
        if( ! $this->service->success() ){
            return array();
        }
        if( isset( $response['merge_fields'] ) ){
            foreach( $response['merge_fields'] as $field ){
                $items[$field['merge_id']] = $field['tag'];
            }
        }
        return $items;
    }


}
