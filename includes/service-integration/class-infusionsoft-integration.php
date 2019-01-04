<?php namespace MasterPopups\Includes\ServiceIntegration;

use MasterPopups\Includes\Functions;

class InfusionsoftIntegration extends ServiceIntegration {
    //public static $procesado = false;

    /*
    |---------------------------------------------------------------------------------------------------
    | Constructor
    |---------------------------------------------------------------------------------------------------
    */
    public function __construct( $api_key = '', $token ){
        $host = $token . '.infusionsoft.com';

        \Mpp_Infusionsoft_AppPool::addApp( new \Mpp_Infusionsoft_App( $host, $api_key, 443 ) );
    }

    /*
    |---------------------------------------------------------------------------------------------------
    | Comprueba si la conexión con el servicio es exitosa
    |---------------------------------------------------------------------------------------------------
    */
    public function is_connect(){
        if( \Mpp_Infusionsoft_DataService::ping() ){
            try{
                \Mpp_Infusionsoft_WebFormService::getMap( \Mpp_Infusionsoft_AppPool::getApp() );
                return true;
            } catch( \Exception $e ){
                if( strpos( $e->getMessage(), "[InvalidKey]" ) !== false ){
                    $this->error = 'Your API Key is not correct';
                } else{
                    $this->error = 'Failure!!! Some other error: ' . $e->error;
                }
                return false;
            }
        }
        $this->error = 'Account not found';
        return false;
    }


    /*
    |---------------------------------------------------------------------------------------------------
    | Retorna todas las listas
    |---------------------------------------------------------------------------------------------------
    */
    public function get_lists(){
        $items = array();
        $objects = \Mpp_Infusionsoft_DataService::query( new \Mpp_Infusionsoft_ContactGroup(), array( 'Id' => '%' ) );
        if( count( $objects ) > 0 ){
            foreach( $objects as $object ){
                $list = $object->toArray();
                $items[$list['Id']] = $list['GroupName'];
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
        //query($object, $queryData, $limit = 1000, $page = 0, $returnFields = false)
        $objects = \Mpp_Infusionsoft_DataService::query( new \Mpp_Infusionsoft_Contact(), array( 'Id' => '%' ), 10000 );
        if( count( $objects ) > 0 ){
            foreach( $objects as $object ){
                $contact = $object->toArray();
                if( $contact['Email'] == $email ){
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
        // if( self::$procesado ){
        // 	return 'No hacer nada';
        // }
        // self::$procesado = true;

        $first_name = $data['first_name'];
        $first_name['value'] = ! empty( $first_name['value'] ) ? $first_name['value'] : '';
        $first_name['name'] = ! empty( $first_name['name'] ) ? $first_name['name'] : 'FirstName';

        $last_name = $data['last_name'];
        $last_name['value'] = ! empty( $last_name['value'] ) ? $last_name['value'] : '';
        $last_name['name'] = ! empty( $last_name['name'] ) ? $last_name['name'] : 'LastName';

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
        $params = array_merge( $params, $data['custom_fields'] );

        //Cambiamos los nombres de los campos a CamelCase (Así requiere la API)
        $new_params = array();
        foreach( $params as $key => $val ){
            $new_params[Functions::string_to_camelcase( $key )] = $val;
        }

        //Creamos el contacto con todos sus datos
        $contact = new \Mpp_Infusionsoft_Contact();
        //Datos básicos
        $contact->Email = $email;
        $contact->FirstName = $first_name['value'];
        $contact->LastName = $last_name['value'];
        //Filtramos otros campos
        $default_fields = $this->get_default_fields();
        foreach( $new_params as $key => $val ){
            if( in_array( $key, $default_fields ) ){
                $contact->$key = $val;
            } else{//Campos personalizados
                $key = '_' . $key;
                $contact->addCustomField( $key );
                $contact->$key = $val;
            }
        }

        //Guardamos el nuevo contacto
        $contact_id = $contact->save();
        if( ! is_int( $contact_id ) ){
            return false;
        }

        //Agregamos el contacto a la lista (Tags en InfusionSoft)
        $response = \Mpp_Infusionsoft_ContactService::addToGroup( $contact_id, $this->list_id );
        if( is_bool( $response ) && $response == true ){
            return true;
        }
        return false;
    }

    /*
    |---------------------------------------------------------------------------------------------------
    | Retorna todos los campos por defecto
    |---------------------------------------------------------------------------------------------------
    */
    public function get_default_fields(){
        $contact = new \Mpp_Infusionsoft_Contact();
        if( $contact ){
            return $contact->getFields();
        }
        return array();
    }

    /*
    |---------------------------------------------------------------------------------------------------
    | Retorna todos los campos personalizados
    |---------------------------------------------------------------------------------------------------
    */
    public function get_custom_fields(){
        $items = array();
        $objects = \Mpp_Infusionsoft_CustomFieldService::getCustomFields( new \Mpp_Infusionsoft_Contact() );
        if( count( $objects ) > 0 ){
            foreach( $objects as $object ){
                $list = $object->toArray();
                $items[$list['Id']] = $list['Name'];
            }
        }
        return $items;
    }


}

