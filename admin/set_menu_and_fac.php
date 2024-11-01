<?php

/**
 * Adding menu as WooCommerce's menu's Submenu
 * check inside Woocommerce Menu
 * 
 * @since 1.0
 */
function wcmmq_g_add_menu(){
    add_submenu_page( 
        'woocommerce',
        __( 'WC Min Max Step Quantity', 'wcmmq' ),
        __( 'Min Max Step Quantity Global', 'wcmmq' ),
        'manage_options',
        'wcmmq_g_min_max_step',
        'wcmmq_g_faq_page_details'
    );
}
add_action( 'admin_menu', 'wcmmq_g_add_menu' );

/**
 * Faq Page for WC Min Max Quantity
 */
function wcmmq_g_faq_page_details(){
    
    if( isset( $_POST['data'] ) && isset( $_POST['reset_button'] ) ){
        //Reset 
        $data = WC_MMQ_G::getDefaults();
        //var_dump($value);
        update_option( WC_MMQ_G::KEY, $data );
        echo sprintf( '<div class="updated inline"><p>%s</p></div>', __( 'Reset Successfully', 'wcmmq' ));
    }else if( isset( $_POST['data'] ) && isset( $_POST['configure_submit'] ) ){
        //Confirm Manage option permission
        if( !current_user_can('manage_options') ){
            return;
        }

        //Nonce verify
        if ( ! isset( $_POST['wcmmq_g_nonce'] ) ) { // Check if our nonce is set.
			return;
	}
        // verify this came from the our screen and with proper authorization,
        // because save_post can be triggered at other times
        if( !wp_verify_nonce( $_POST['wcmmq_g_nonce'], plugin_basename(__FILE__) ) ) {
                return;
        }
        
        //configure_submit
        $values = ( isset($_POST['data']) && is_array( $_POST['data'] ) ? $_POST['data'] : false );
        $data = $final_data = array();
        if( is_array( $values ) && count( $values ) > 0 ){
            foreach( $values as $key=>$value ){
                if( empty( $value ) ){
                   $data[$key] = false; 
                }else{
                   $data[$key] = $value;  
                }
            }
        }else{
            $data = WC_MMQ_G::getDefaults();
        }
        
        if( !$data['_wcmmq_g_min_quantity'] && $data['_wcmmq_g_min_quantity'] != 0 &&  $data['_wcmmq_g_min_quantity'] !=1 && $data['_wcmmq_g_max_quantity'] <= $data['_wcmmq_g_min_quantity'] ){
            $data['_wcmmq_g_max_quantity'] = $data['_wcmmq_g_min_quantity'] + 5;
            echo sprintf( '<div class="error notice"><p>%s</p></div>', __( 'Maximum Quantity can not be smaller, So we have added 5', 'wcmmq' ) );
        }
        if( !$data['_wcmmq_g_product_step'] || $data['_wcmmq_g_product_step'] == '0' || $data['_wcmmq_g_product_step'] == 0 ){
           $data['_wcmmq_g_product_step'] = 1; 
        }
        
        if( !$data['_wcmmq_g_min_quantity'] || $data['_wcmmq_g_min_quantity'] == '0' || $data['_wcmmq_g_min_quantity'] == 0 ){
           $data['_wcmmq_g_min_quantity'] = '0'; 
        }
        
        
        if(is_array( $data ) && count( $data ) > 0 ){
            foreach($data as $key=>$value){
                $val = str_replace('\\', '', $value );
                $final_data[$key] = sanitize_text_field( $val ); // all data sanitized
            }
        }
        update_option( WC_MMQ_G::KEY, $final_data ); // all data sanitized
        echo sprintf( '<div class="updated inline"><p>%s</p></div>', __( 'Successfully Updated', 'wcmmq' ));
    }
    
    
    $saved_data = WC_MMQ_G::getOptions();
?>
<div class="wrap wcmmq_g_wrap">
    <div class="wcmmq_fieldwrap">
        <form action="" method="POST">
            <input type="hidden" name="wcmmq_g_nonce" value="<?php echo wp_create_nonce( plugin_basename(__FILE__) ) ?>" />
            <div class="wcmmq_g_white_board">
                <span class="configure_section_title">Settings (Universal)</span>
                <table class="wcmmq_g_config_form">
                    <tr>
                        <th><?php echo esc_html__( 'Minimum Quantity', 'wcmmq' ); ?></th>
                        <td>
                            <input name="data[_wcmmq_g_min_quantity]" value="<?php echo esc_attr($saved_data['_wcmmq_g_min_quantity']); ?>"  type="number" step=any>
                        </td>

                    </tr>

                    <tr>
                        <th><?php echo esc_html__( 'Maximum Quantity', 'wcmmq' ); ?></th>
                        <td>
                            <input name="data[_wcmmq_g_max_quantity]" value="<?php echo esc_attr($saved_data['_wcmmq_g_max_quantity']); ?>"  type="number" step=any>
                        </td>

                    </tr>

                    <tr>
                        <th><?php echo esc_html__( 'Quantity Step', 'wcmmq' ); ?></th>
                        <td>
                            <input name="data[_wcmmq_g_product_step]" value="<?php echo esc_attr($saved_data['_wcmmq_g_product_step']); ?>"  type="number" step=any>
                        </td>

                    </tr>

                </table>
                <span class="configure_section_title"><?php echo esc_html__( 'Messages', 'wcmmq' ); ?></span>
                <table class="wcmmq_g_config_form wcmmq_g_config_form_message">
                    <tr>
                        <th><?php echo esc_html__( 'Minimum Quantity Validation Message', 'wcmmq' ); ?></th>
                        <td>
                            <input name="data[_wcmmq_g_msg_min_limit]" value="<?php echo esc_attr( $saved_data['_wcmmq_g_msg_min_limit'] ); ?>"  type="text">
                        </td>

                    </tr>
                    <tr>
                        <th><?php echo esc_html__( 'Maximum Quantity Validation Message', 'wcmmq' ); ?></th>
                        <td>
                            <input name="data[_wcmmq_g_msg_max_limit]" value="<?php echo esc_attr( $saved_data['_wcmmq_g_msg_max_limit'] ); ?>"  type="text">
                        </td>

                    </tr>
                    <tr>
                        <th><?php echo esc_html__( 'Already in cart message', 'wcmmq' ); ?></th>
                        <td>
                            <input name="data[_wcmmq_g_msg_max_limit_with_already]" value="<?php echo esc_attr( $saved_data['_wcmmq_g_msg_max_limit_with_already'] ); ?>"  type="text">
                        </td>
                    </tr>
                    <tr>
                        <th><?php echo esc_html__( 'Minimum Quantity message for shop page', 'wcmmq' ); ?></th>
                        <td>
                            <input name="data[_wcmmq_g_min_qty_msg_in_loop]" value="<?php echo esc_attr( $saved_data['_wcmmq_g_min_qty_msg_in_loop'] ); ?>" type="text">
                        </td>
                    </tr>
                </table>
                <div class="wcmmq_g_waring_msg"><?php echo esc_html__( "Important Note: Don't change [%s], because it will work as like  variable. Here 1st [%s] will return Quantity(min/max) and second [%s] will return product's name.", 'wcmmq' ); ?></div>
            </div>
            <br>
            <button type="submit" name="configure_submit" class="button-primary primary button btn-info"><?php echo esc_html__( 'Submit', 'wcmmq' ); ?></button>
            <button type="submit" name="reset_button" class="button"><?php echo esc_html__( 'Reset', 'wcmmq' ); ?></button>
                    
        </form>
    </div>

</div>  

<?php
}

function wcmmq_g_load_custom_wp_admin_style() {
        wp_register_style( 'wcmmq_g_css', WC_MMQ_G_BASE_URL . 'admin/wcmmq_g_style.css', false, WC_MMQ_G::getVersion() );
        wp_enqueue_style( 'wcmmq_g_css' );
}
add_action( 'admin_enqueue_scripts', 'wcmmq_g_load_custom_wp_admin_style' );