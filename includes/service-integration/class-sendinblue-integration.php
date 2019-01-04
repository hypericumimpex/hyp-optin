<?php namespace MasterPopups\Includes\ServiceIntegration;

use MasterPopups\Sendinblue\Mailin;

class SendinblueIntegration extends ServiceIntegration {
    private $url = 'https://api.sendinblue.com/v2.0';

    /*
    |---------------------------------------------------------------------------------------------------
    | Constructor
    |---------------------------------------------------------------------------------------------------
    */
    public function __construct( $api_key = '' ){
        $this->api_key = $api_key;
        $this->service = new Mailin( $this->url, $this->api_key );
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
        $data = $this->service->get_attributes();
        if( $data['code'] === 'failure' ){
            $this->error = $data['message'];
            return false;
        }
        return true;
    }

    /*
    |---------------------------------------------------------------------------------------------------
    | Retorna todas las listas
    |---------------------------------------------------------------------------------------------------
    */
    public function get_lists(){
        $items = array();
        $data = array(
            "page" => 1,
            "page_limit" => 200
        );
        $lists = $this->service->get_lists( $data );
        if( $lists['code'] !== 'failure' ){
            if( $lists['data']['total_list_records'] > 0 ){
                foreach( $lists['data']['lists'] as $list ){
                    $items[$list['id']] = $list['name'];
                }
            }
        } else{
            $this->error = $lists['message'];
        }
        return $items;
    }

    /*
    |---------------------------------------------------------------------------------------------------
    | Verifica si un suscriptor está en la lista actual
    |---------------------------------------------------------------------------------------------------
    */
    private function subscriber_exists( $email ){
        $response = $this->service->get_user( array( "email" => $email ) );
        if( isset( $response['code'] ) && $response['code'] == 'success' ){
            foreach( $response['data']['listid'] as $list ){
                if( $list == $this->list_id ){
                    return true;
                }
            }
        }
        return false;
    }

    /*
    |---------------------------------------------------------------------------------------------------
    | Agrega un suscriptor a una lista
    |---------------------------------------------------------------------------------------------------
    */
    public function add_subscriber( $email, $data = array() ){
        $first_name = $data['first_name'];
        $first_name['value'] = ! empty( $first_name['value'] ) ? $first_name['value'] : '';
        $first_name['name'] = ! empty( $first_name['name'] ) ? $first_name['name'] : 'NAME';

        $last_name = $data['last_name'];
        $last_name['value'] = ! empty( $last_name['value'] ) ? $last_name['value'] : '';
        $last_name['name'] = ! empty( $last_name['name'] ) ? $last_name['name'] : 'SURNAME';

        //Comprobamos si el usuario ya está registrado
        if( $this->subscriber_exists( $email ) ){
            $this->error = $this->messages['subscriber_exists'];
            return false;
        }

        //Datos necesarios para la suscripción
        $params = array();
        $params['email'] = $email;
        $params['listid'] = array( $this->list_id );
        $params['attributes'] = array();
        $params['attributes'][$first_name['name']] = $first_name['value'];
        $params['attributes'][$last_name['name']] = $last_name['value'];

        if( ! empty( $data['custom_fields'] ) ){
            $custom_fields = $this->get_custom_fields();
            foreach( $custom_fields as $cf_id => $cf_name ){
                if( isset( $data['custom_fields'][$cf_name] ) ){
                    $params['attributes'][$cf_name] = $data['custom_fields'][$cf_name];
                }
            }
        }

        //Suscribir nuevo usuario
        $this->response = $this->service->create_update_user( $params );

        if( isset( $this->response['code'] ) ){
            if( $this->response['code'] == 'success' ){
                return true;
            } else{
                $this->error = isset( $this->response['message'] ) ? $this->response['message'] : '';
                return false;
            }
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
            'NAME',
        );
    }

    /*
    |---------------------------------------------------------------------------------------------------
    | Retorna todos los campos personalizados
    |---------------------------------------------------------------------------------------------------
    */
    public function get_custom_fields(){
        $items = array();
        $response = $this->service->get_attributes();
        if( isset( $response['code'] ) && $response['code'] == 'success' ){
            if( isset( $response['data']['normal_attributes'] ) ){
                foreach( $response['data']['normal_attributes'] as $field ){
                    $items[] = $field['name'];
                }
            }
        } else{
            return array();
        }
        return $items;
    }


}



