<?php namespace MasterPopups\Includes\ServiceIntegration;

use MasterPopups\MailerLiteApi\MailerLite as MailerLite;

class MailerLiteIntegration extends ServiceIntegration {

    /*
    |---------------------------------------------------------------------------------------------------
    | Constructor
    |---------------------------------------------------------------------------------------------------
    */
    public function __construct( $api_key = '' ){
        $this->api_key = $api_key;
        $this->service = new MailerLite( $this->api_key );
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
        return ! isset( $this->service->fields()->get()->first()->error );
    }

    /*
    |---------------------------------------------------------------------------------------------------
    | Retorna todas las listas
    |---------------------------------------------------------------------------------------------------
    */
    public function get_lists(){
        $items = array();
        $lists = $this->service->groups()->get()->toArray();
        foreach( $lists as $list ){
            $items[$list->id] = $list->name;
        }
        return $items;
    }

    /*
    |---------------------------------------------------------------------------------------------------
    | Verifica si un suscriptor está en la lista actual
    |---------------------------------------------------------------------------------------------------
     */
    private function subscriber_exists( $email ){
        $subscribers = $this->service->groups()->getSubscribers( $this->list_id );
        if( ! isset( $subscribers->error ) ){
            foreach( $subscribers as $subscriber ){
                if( $subscriber->email === $email ){
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
        $first_name['name'] = ! empty( $first_name['name'] ) ? $first_name['name'] : 'name';

        $last_name = $data['last_name'];
        $last_name['value'] = ! empty( $last_name['value'] ) ? $last_name['value'] : '';
        $last_name['name'] = ! empty( $last_name['name'] ) ? $last_name['name'] : 'last_name';

        //Comprobamos si el usuario ya está registrado
        if( $this->subscriber_exists( $email ) ){
            $this->error = $this->messages['subscriber_exists'];
            return false;
        }

        //Datos necesarios para la suscripción
        $params = array();
        $params['email'] = $email;
        $params['name'] = $first_name['value'];
        $params['fields'] = array();
        $params['fields'][$first_name['name']] = $first_name['value'];
        $params['fields'][$last_name['name']] = $last_name['value'];

        if( ! empty( $data['custom_fields'] ) ){
            $custom_fields = $this->get_custom_fields();
            foreach( $custom_fields as $cf_id => $cf_name ){
                if( isset( $data['custom_fields'][$cf_name] ) ){
                    $params['fields'][$cf_name] = $data['custom_fields'][$cf_name];
                }
            }
        }

        //Suscribir nuevo usuario
        $this->response = $this->service->groups()->addSubscriber( $this->list_id, $params );

        if( ! isset( $this->response->error ) ){
            return true;
        } else{
            $this->error = $this->response->error;
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
        $fields = $this->service->fields()->get()->toArray();
        if( empty( $fields ) ){
            return array();
        }
        foreach( $fields as $field ){
            $items[$field->id] = $field->key;
        }
        return $items;
    }

}