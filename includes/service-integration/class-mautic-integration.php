<?php namespace MasterPopups\Includes\ServiceIntegration;

use MasterPopups\Mautic\Auth\ApiAuth;
use MasterPopups\Mautic\MauticApi;

class MauticIntegration extends ServiceIntegration {
    private $auth = null;
    private $context = null;
    protected $url = '';

    /*
    |---------------------------------------------------------------------------------------------------
    | Constructor
    |---------------------------------------------------------------------------------------------------
    */
    public function __construct( $username = '', $password = '', $url = '' ){
        $this->url = $url;
        $settings = array(
            'userName' => $username,
            'password' => $password,
        );
        // Initiate the auth object specifying to use BasicAuth
        $initAuth = new ApiAuth();
        $this->auth = $initAuth->newAuth( $settings, 'BasicAuth' );
        $this->service = new MauticApi();
    }

    /*
    |---------------------------------------------------------------------------------------------------
    | Retorna el servicio basado en un contexto
    |---------------------------------------------------------------------------------------------------
    */
    public function get_service( $context ){
        return $this->service->newApi( $context, $this->auth, $this->url );
    }

    /*
    |---------------------------------------------------------------------------------------------------
    | Retorna una lista de registros basado e un contexto
    |---------------------------------------------------------------------------------------------------
    */
    private function get_all( $context, $limit = 50000 ){
        return $context->getList( '', 0, $limit );//$search = '', $start = 0, $limit = 0, $orderBy = '', $orderByDir = 'ASC', $publishedOnly = false, $minimal = false
    }

    /*
    |---------------------------------------------------------------------------------------------------
    | Comprueba si la conexión con el servicio es exitosa
    |---------------------------------------------------------------------------------------------------
    */
    public function is_connect(){
        $segmentApi = $this->get_service( 'segments' );
        $segments = $this->get_all( $segmentApi );
        if( isset( $segments['lists'] ) ){
            return true;
        } else if( isset( $segments['error'] ) || isset( $segments['errors'] ) ){
            $error = isset( $segments['errors'] ) ? $segments['errors'][0]['message'] : '';
        } elseif( isset( $segments['message'] ) ){
            $error = $segments['message'];
        }
        $this->error = 'Did you enter a Mautic URL? ' . $error;
        return false;
    }

    /*
    |---------------------------------------------------------------------------------------------------
    | Retorna todas las listas
    |---------------------------------------------------------------------------------------------------
    */
    public function get_lists(){
        return $this->get_segments();
    }

    /*
    |---------------------------------------------------------------------------------------------------
    | Verifica si un suscriptor está en la lista actual
    |---------------------------------------------------------------------------------------------------
    */
    public function get_contacts(){
        $contactApi = $this->get_service( 'contacts' );
        $response = $this->get_all( $contactApi );

        if( ! isset( $response['contacts'] ) ){
            return array();
        }
        $contacts = array();
        foreach( $response['contacts'] as $id => $data ){
            $contacts[$data['id']] = $data['fields']['all'];
        }
        return $contacts;
    }

    /*
      |---------------------------------------------------------------------------------------------------
      | Verifica si un suscriptor está en la lista actual
      |---------------------------------------------------------------------------------------------------
      */
    private function subscriber_exists( $email ){
        $contacts = $this->get_contacts();
        foreach( $contacts as $id => $contact ){
            if( $email == $contact['email'] ){
                return true;
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
        $first_name['name'] = ! empty( $first_name['name'] ) ? $first_name['name'] : 'firstname';

        $last_name = $data['last_name'];
        $last_name['value'] = ! empty( $last_name['value'] ) ? $last_name['value'] : '';
        $last_name['name'] = ! empty( $last_name['name'] ) ? $last_name['name'] : 'lastname';

        //Comprobamos si el usuario ya está registrado
        if( $this->subscriber_exists( $email ) ){
            $this->error = $this->messages['subscriber_exists'];
            return false;
        }

        //Datos necesarios para la suscripción
        $params = array();
        $params['email'] = $email;
        $params[$first_name['name']] = $first_name['value'];
        $params[$last_name['name']] = $last_name['value'];
        $params['ipAddress'] = $_SERVER['REMOTE_ADDR'];

        if( ! empty( $data['custom_fields'] ) ){
            $custom_fields = $this->get_custom_fields();
            foreach( $custom_fields as $cf_id => $cf_name ){
                if( isset( $data['custom_fields'][$cf_name] ) ){
                    $params[$cf_name] = $data['custom_fields'][$cf_name];
                }
            }
        }

        //Suscribir nuevo usuario
        $contactApi = $this->get_service( 'contacts' );
        $this->response = $contactApi->create( $params );

        //'error' is deprecated as of 2.6.0 and will be removed in 3.0. Use the 'errors' array instead.
        if( isset( $this->response['error'] ) ){
            $this->error = $this->response['error']['message'];
            return false;
        }

        $contact_id = $this->response['contact']['id'];
        $segmentApi = $this->get_service( 'segments' );
        $this->response = $segmentApi->addContact( $this->list_id, $contact_id );
        if( ! isset( $this->response['success'] ) ){
            return false;
        }

        return true;
    }

    /*
    |---------------------------------------------------------------------------------------------------
    | Retorna todos los campos personalizados
    |---------------------------------------------------------------------------------------------------
    */
    public function get_custom_fields(){
        $items = array();
        $fieldApi = $this->get_service( 'contactFields' );
        $response = $this->get_all( $fieldApi );
        if( count( $response['fields'] ) < 1 ){
            return array();
        }
        foreach( $response['fields'] as $data ){
            if( $data['isPublished'] ){
                $items[$data['id']] = $data['alias'];
            }
        }
        return $items;
    }

    /*
    |---------------------------------------------------------------------------------------------------
    | Retorna todos los segmentos
    |---------------------------------------------------------------------------------------------------
    */
    public function get_segments(){
        $items = array();
        $segmentApi = $this->get_service( 'segments' );
        $response = $this->get_all( $segmentApi );
        if( count( $response['lists'] ) < 1 ){
            return array();
        }
        foreach( $response['lists'] as $data ){
            if( $data['isPublished'] ){
                $items[$data['id']] = $data['name'];
            }
        }
        return $items;
    }

    /*
    |---------------------------------------------------------------------------------------------------
    | Retorna una lista de "contact owners"
    |---------------------------------------------------------------------------------------------------
    */
    public function get_contact_owners(){
        $items = array();
        $contactApi = $this->get_service( 'contacts' );
        $response = $contactApi->getOwners();

        if( $contactApi->getResponseInfo()['http_code'] == 200 && ! empty( $response ) ){
            foreach( $response as $data ){
                $items[$data['id']] = $data['firstName'] . ' ' . $data['lastName'];
            }
        }
        return $items;
    }

}

