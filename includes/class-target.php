<?php namespace MasterPopups\Includes;


class Target {
    private $display = false;
    private $plugin = null;
    private $popup = null;
    private $prefix = '';

    /*
    |---------------------------------------------------------------------------------------------------
    | Constructor
    |---------------------------------------------------------------------------------------------------
    */
    public function __construct( $plugin = null, $popup = null ){
        $this->plugin = $plugin;
        $this->popup = $popup;
    }

    /*
    |---------------------------------------------------------------------------------------------------
    | Comprueba si se debe mostrar el popup
    |---------------------------------------------------------------------------------------------------
    */
    public function should_display_popup(){
        $display = false;
        global $post;

        if( is_admin() ){
            return $this->display_on_admin();
        }

        //Display Target
        $display = $this->display_on_all_site();

        if( is_archive() ){
            $display = $this->display_on_archive();
            if( is_category() ){
                $display = $this->display_on_category();
            } else if( is_tag() ){
                $display = $this->display_on_post_tag();
            }
        }

        if( Functions::is_homepage() ){
            $display = $this->display_on_homepage();
        } else if( is_single() ){
            if( is_singular( array( 'post' ) ) ){
                $display = $this->display_on_posts();
            } else{
                $post_types = $this->popup->options_manager->get_not_builtin_post_types();
                if( is_singular( array_keys( $post_types ) ) ){
                    $display = $this->display_on_post_types();
                }
            }
        } else if( is_page() ){
            $display = $this->display_on_pages();
        }

        if( $this->display_on_specific_urls() ){
            $display = true;
        }
        if( $this->not_display_on_specific_urls() ){
            $display = false;
        }

        //Display Conditions
        if( $display ){
            $display_for_users = $this->display_for_users();
            $display_on_devices = $this->display_on_devices();
            if( ! $display_for_users || ! $display_on_devices ){
                $display = false;
            }
        }

        return $display;
    }

    /*
    |---------------------------------------------------------------------------------------------------
    | Comprueba si se debe mostrar el popup en el admin
    |---------------------------------------------------------------------------------------------------
    */
    public function display_on_admin(){
        $display = false;
        if( is_admin() ){
            $display = true;
        }
        return $display;
    }

    /*
    |---------------------------------------------------------------------------------------------------
    | Comprueba si se debe mostrar el popup en todo el sitio
    |---------------------------------------------------------------------------------------------------
    */
    public function display_on_all_site(){
        return 'on' == $this->popup->option( 'display-on-all-site' ) ? true : false;
    }

    /*
    |---------------------------------------------------------------------------------------------------
    | Comprueba si se debe mostrar el popup en la página principal
    |---------------------------------------------------------------------------------------------------
    */
    public function display_on_homepage(){
        return 'on' == $this->popup->option( 'display-on-homepage' ) ? true : false;
    }

    /*
    |---------------------------------------------------------------------------------------------------
    | Comprueba si se debe mostrar el popup en páginas de archivos.
    |---------------------------------------------------------------------------------------------------
    */
    public function display_on_archive(){
        return 'on' == $this->popup->option( 'display-on-archive' ) ? true : false;
    }

    /*
    |---------------------------------------------------------------------------------------------------
    | Comprueba si se debe mostrar el popup en categorías
    |---------------------------------------------------------------------------------------------------
    */
    public function display_on_category(){
        $display = false;
        if( 'on' == $this->popup->option( 'display-on-taxonomy-category' ) ){
            $display = true;
        }
        $term = get_queried_object();
        if( in_array( $term->slug, $this->popup->option( 'display-on-taxonomy-category-terms' ) ) ){
            $display = true;
        }
        return $display;
    }

    /*
    |---------------------------------------------------------------------------------------------------
    | Comprueba si se debe mostrar el popup en etiquetas
    |---------------------------------------------------------------------------------------------------
    */
    public function display_on_post_tag(){
        $display = false;
        if( 'on' == $this->popup->option( 'display-on-taxonomy-post_tag' ) ){
            $display = true;
        }
        $term = get_queried_object();
        if( in_array( $term->slug, $this->popup->option( 'display-on-taxonomy-post_tag-terms' ) ) ){
            $display = true;
        }
        return $display;
    }

    /*
    |---------------------------------------------------------------------------------------------------
    | Comprueba si se debe mostrar el popup en un post
    |---------------------------------------------------------------------------------------------------
    */
    public function display_on_posts(){
        $display = false;
        global $post;

        if( 'on' == $this->popup->option( 'display-on-post' ) ){
            $display = true;
        } else if( in_array( $post->ID, wp_parse_id_list( $this->popup->option( 'display-on-post-include' ) ) ) ){
            $display = true;
        }
        if( in_array( $post->ID, wp_parse_id_list( $this->popup->option( 'display-on-post-exclude' ) ) ) ){
            $display = false;
        }
        return $display;
    }

    /*
    |---------------------------------------------------------------------------------------------------
    | Comprueba si se debe mostrar el popup en una página
    |---------------------------------------------------------------------------------------------------
    */
    public function display_on_pages(){
        $display = false;
        global $post;

        if( 'on' == $this->popup->option( 'display-on-page' ) ){
            $display = true;
        } else if( in_array( $post->ID, wp_parse_id_list( $this->popup->option( 'display-on-page-include' ) ) ) ){
            $display = true;
        }
        if( in_array( $post->ID, wp_parse_id_list( $this->popup->option( 'display-on-page-exclude' ) ) ) ){
            $display = false;
        }
        return $display;
    }

    /*
    |---------------------------------------------------------------------------------------------------
    | Comprueba si se debe mostrar el popup en un post type
    |---------------------------------------------------------------------------------------------------
    */
    public function display_on_post_types(){
        $display = false;
        global $post;
        if( ! $post ){
            return false;
        }
        $name = $post->post_type;
        if( 'on' == $this->popup->option( 'display-on-' . $name ) ){
            $display = true;
        } else if( in_array( $post->ID, wp_parse_id_list( $this->popup->option( 'display-on-' . $name . '-include' ) ) ) ){
            $display = true;
        }
        if( in_array( $post->ID, wp_parse_id_list( $this->popup->option( 'display-on-' . $name . '-exclude' ) ) ) ){
            $display = false;
        }
        return $display;
    }

    /*
    |---------------------------------------------------------------------------------------------------
    | Comprueba si se debe mostrar el popup en urls espefíficas
    |---------------------------------------------------------------------------------------------------
    */
    public function display_on_specific_urls(){
        $display = false;
        $current_url = str_replace( array( 'https://', 'http://' ), '', Functions::current_url() );
        $specific_urls = str_replace( array( 'https://', 'http://' ), '', $this->popup->option( 'display-on-specific-urls' ) );
        $urls = array_map( 'trim', explode( ',', $specific_urls ) );

        foreach( $urls as $url ){
            if( ! empty( $url ) ){
                if( strpos( $url, '*', strlen( $url ) - 1 ) !== false ){
                    $url = str_replace( '*', '', $url );
                    if( strpos( $current_url, $url ) !== false && strlen( $current_url ) > strlen( $url ) ){
                        $display = true;
                    }
                } else if( $current_url == $url || $current_url == $url . '/' ){
                    $display = true;
                }
            }
        }

        return $display;
    }

    /*
    |---------------------------------------------------------------------------------------------------
    | Comprueba si no se debe mostrar el popup en urls espefíficas
    |---------------------------------------------------------------------------------------------------
    */
    public function not_display_on_specific_urls(){
        $not_display = false;
        $specific_urls = str_replace( array( 'https://', 'http://' ), '', $this->popup->option( 'display-on-specific-urls' ) );
        $urls = array_map( 'trim', explode( ',', $specific_urls ) );

        //Exclude URL like: -http://domain.com/post
        if( $this->not_show_in_urls( $urls, true ) ){
            $not_display = true;
        }

        //Excluir también en la nueva opción de "Excluir URLs"
        $specific_urls = str_replace( array( 'https://', 'http://' ), '', $this->popup->option( 'display-on-specific-urls-exclude' ) );
        $urls = array_map( 'trim', explode( ',', $specific_urls ) );
        if( $this->not_show_in_urls( $urls, false ) ){
            $not_display = true;
        }
        return $not_display;
    }

    /*
    |---------------------------------------------------------------------------------------------------
    | No mostrar popup en ciertas urls
    |---------------------------------------------------------------------------------------------------
    */
    public function not_show_in_urls( $urls = array(), $search_minus = false ){
        $not_show = false;
        $current_url = str_replace( array( 'https://', 'http://' ), '', Functions::current_url() );
        foreach( $urls as $url ){
            if( ! empty( $url ) && ( ! $search_minus || ( $search_minus && strpos( $url, '-' ) === 0 ) ) ){
                if( $search_minus ){
                    $url = ltrim( $url, '-' );
                }
                if( strpos( $url, '*', strlen( $url ) - 1 ) !== false ){
                    $url = str_replace( '*', '', $url );
                    if( strpos( $current_url, $url ) !== false && strlen( $current_url ) > strlen( $url ) ){
                        $not_show = true;
                    }
                } else if( $current_url == $url || $current_url == $url . '/' ){
                    $not_show = true;
                }
            }
        }
        return $not_show;
    }


    /*
    |---------------------------------------------------------------------------------------------------
    | Comrpueba si se debe mostrar el popup a usuarios registrados/no registrados
    |---------------------------------------------------------------------------------------------------
    */
    public function display_for_users(){
        $display = false;
        $display_for_users = (array) $this->popup->option( 'display-for-users' );
        if( is_user_logged_in() ){
            $display = in_array( 'logged-in', $display_for_users );
        } else{
            $display = in_array( 'not-logged-in', $display_for_users );
        }
        return $display;
    }

    /*
    |---------------------------------------------------------------------------------------------------
    | Comrpueba si se debe mostrar el popup en ciertos dispositivos
    |---------------------------------------------------------------------------------------------------
    */
    public function display_on_devices(){
        $display = false;
        $display_on_devices = (array) $this->popup->option( 'display-on-devices' );
        $mobile_delect = new \Mobile_Detect_Popup_Master();
        if( $mobile_delect->isMobile() && ! $mobile_delect->isTablet() ){
            $display = in_array( 'mobile', $display_on_devices );
        } else if( $mobile_delect->isTablet() ){
            $display = in_array( 'tablet', $display_on_devices );
        } else{
            $display = in_array( 'desktop', $display_on_devices );
        }
        return $display;
    }


}
