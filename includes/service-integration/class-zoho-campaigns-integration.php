<?php namespace MasterPopups\Includes\ServiceIntegration;

use MaxLopez\HTTPClientWP\IronMan;

class ZohoCampaignsIntegration extends ServiceIntegration {
    //https://www.zoho.com/campaigns/help/api/authentication-token.html
    private $api_endpoint = 'https://campaigns.zoho.com/api/';

    /*
    |---------------------------------------------------------------------------------------------------
    | Constructor
    |---------------------------------------------------------------------------------------------------
    */
    public function __construct( $api_key ){
        $this->api_key = trim( $api_key );

        $this->ironman = new IronMan( $this->api_endpoint );
        $this->ironman->set_body( array(
            'authtoken' => $this->api_key,
            'resfmt' => 'JSON',
            'scope' => 'CampaignsAPI',
        ) );
    }

    /*
    |---------------------------------------------------------------------------------------------------
    | Si la petición se realizó con éxito
    |---------------------------------------------------------------------------------------------------
    */
    public function is_success( $valid_code = null ){
        $success = false;
        if( isset( $this->ironman->response ) ){
            $resp = json_decode( $this->ironman->get_response_body() );
            if( ( isset( $resp->status ) && $resp->status == 'success' ) ){
                $success = true;
            } else if( ( isset( $resp->code ) && $resp->code == '0' ) || ( isset( $resp->Code ) && $resp->Code == '200' ) ){
                $success = true;
            } else if( isset( $resp->response->message ) && $resp->response->message == 'success' ){
                $success = true;
            } else if( ( isset( $resp->response->code ) && $resp->response->code == '0' ) || ( isset( $resp->response->Code ) && $resp->response->Code == '200' ) ){
                $success = true;
            } else if( $valid_code != null && isset( $resp->code ) && $resp->code == $valid_code ){
                $success = true;
            }
            if( ! $success ){
                $this->error = isset( $resp->message ) ? $resp->message : $this->error;
            }
        }
        return $success;
    }

    /*
    |---------------------------------------------------------------------------------------------------
    | Comprueba si la conexión con el servicio es exitosa
    |---------------------------------------------------------------------------------------------------
    */
    public function is_connect(){
        $success = $this->new_request( "POST", "/getmailinglists" );
        //https://www.zoho.com/campaigns/help/api/error-codes.html
        //Código de respuesta 2401 cuando no hay listas, es válido para la conexión.
        return $success && $this->is_success( 2401 );
    }

    /*
    |---------------------------------------------------------------------------------------------------
    | Retorna todas las listas
    |---------------------------------------------------------------------------------------------------
    */
    public function get_lists(){
        $items = array();
        $success = $this->new_request( "POST", "/getmailinglists" );
        if( ! $success || ! $this->is_success() ){
            return array();
        }
        $response = json_decode( $this->ironman->get_response_body() );
        $lists = isset( $response->list_of_details ) ? (array) $response->list_of_details : array();
        foreach( $lists as $list ){
            $items[$list->listkey] = $list->listname;
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
        $first_name['name'] = ! empty( $first_name['name'] ) ? $first_name['name'] : 'First Name';

        $last_name = $data['last_name'];
        $last_name['value'] = ! empty( $last_name['value'] ) ? $last_name['value'] : '';
        $last_name['name'] = ! empty( $last_name['name'] ) ? $last_name['name'] : 'Last Name';

        //Datos necesarios para la suscripción
        $params = array();
        $params['Contact Email'] = $email;
        $params[$first_name['name']] = $first_name['value'];
        $params[$last_name['name']] = $last_name['value'];

        if( ! empty( $data['custom_fields'] ) ){
            $custom_fields = $this->get_custom_fields();
            foreach( $data['custom_fields'] as $cf_name => $cf_value ){
                if( $this->isset_field( $cf_name, $custom_fields, true ) ){
                    $params[$cf_name] = $cf_value;
                }
            }
        }

        //Suscribir nuevo usuario
        $success = $this->new_request( "POST", "/json/listsubscribe", array(
            'listkey' => $this->list_id,
            'contactinfo' => json_encode( $params ),
        ) );

        return $success && $this->is_success();
    }

    /*
    |---------------------------------------------------------------------------------------------------
    | Retorna todos los campos personalizados
    |---------------------------------------------------------------------------------------------------
    */
    public function get_custom_fields(){
        $items = array();
        $success = $this->new_request( "POST", "/contact/allfields" );
        if( ! $success || ! $this->is_success() ){
            return array();
        }
        $resp = json_decode( $this->ironman->get_response_body() );
        $fields = isset( $resp->response->fieldnames->fieldname ) ? $resp->response->fieldnames->fieldname : array();
        foreach( $fields as $field ){
            $items[$field->FIELD_NAME] = $field->DISPLAY_NAME;
        }
        return $items;
    }

}
