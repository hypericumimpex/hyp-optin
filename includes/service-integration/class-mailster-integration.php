<?php namespace MasterPopups\Includes\ServiceIntegration;


class MailsterIntegration extends ServiceIntegration {

    /*
    |---------------------------------------------------------------------------------------------------
    | Constructor
    |---------------------------------------------------------------------------------------------------
    */
    public function __construct(){

    }

    /*
    |---------------------------------------------------------------------------------------------------
    | Comprueba si la conexión con el servicio es exitosa
    |---------------------------------------------------------------------------------------------------
    */
    public function is_connect(){
        if( function_exists( 'mailster' ) || function_exists( 'mymail' ) ){
            return true;
        }
        $this->error = sprintf( __( 'Please install and activate the %sMailster%s plugin.', 'masterpopups' ), '<a href="https://codecanyon.net/item/mailster-email-newsletter-plugin-for-wordpress/3078294?ref=codexhelp" target="_blank">', '</a>' );
        return false;
    }

    /*
    |---------------------------------------------------------------------------------------------------
    | Retorna todas las listas
    |---------------------------------------------------------------------------------------------------
    */
    public function get_lists(){
        $items = array();
        $lists = mailster( 'lists' )->get();
        if( is_array( $lists ) ){
            foreach( $lists as $list ){
                $items[$list->ID] = $list->name;
            }
        }
        return $items;
    }

    /*
    |---------------------------------------------------------------------------------------------------
    | Verificar si el contacto está en la lista indicada
    |---------------------------------------------------------------------------------------------------
    */
    private function subscriber_exists( $email ){
        $subscriber = mailster( 'subscribers' )->get_by_mail( $email );
        if( isset( $subscriber->ID ) ){
            return true;
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
        $first_name['name'] = ! empty( $first_name['name'] ) ? $first_name['name'] : 'firstname';

        $last_name = $data['last_name'];
        $last_name['value'] = ! empty( $last_name['value'] ) ? $last_name['value'] : '';
        $last_name['name'] = ! empty( $last_name['name'] ) ? $last_name['name'] : 'lastname';

        //Comprobamos si el usuario ya está registrado
        $overwrite = isset( $data['overwrite'] ) && $data['overwrite'] == 'on' ? true : false;
        if( ! $overwrite && $this->subscriber_exists( $email ) ){
            $this->error = $this->messages['subscriber_exists'];
            return false;
        }

        //Datos necesarios para la suscripción
        $params = array();
        $params['email'] = $email;
        $params[$first_name['name']] = $first_name['value'];
        $params[$last_name['name']] = $last_name['value'];
        $params['status'] = isset( $data['double-opt-in'] ) && $data['double-opt-in'] == 'on' ? 0 : 1;//1 = subscribed (default) , 0 = pending, 2 = unsubscribed, 3 = hardbounced

        if( ! empty( $data['custom_fields'] ) ){
            $custom_fields = $this->get_custom_fields();
            foreach( $custom_fields as $cf_id => $cf_name ){
                if( isset( $data['custom_fields'][$cf_name] ) ){
                    $params[$cf_name] = $data['custom_fields'][$cf_name];
                }
            }
        }

        //Suscribir nuevo usuario
        $subscriber_id = mailster( 'subscribers' )->add( $params, $overwrite );
        if( is_wp_error( $subscriber_id ) ){
            return false;
        }
        return mailster( 'subscribers' )->assign_lists( $subscriber_id, array( $this->list_id ) );
    }

    /*
    |---------------------------------------------------------------------------------------------------
    | Retorna todos los campos por defecto
    |---------------------------------------------------------------------------------------------------
    */
    public function get_default_fields(){
        return array(
            'email',
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
        $fields = mailster_option( 'custom_field' );
        if( is_array( $fields ) ){
            foreach( $fields as $field => $data ){
                $items[] = $field;
            }
        }
        return $items;
    }
}

