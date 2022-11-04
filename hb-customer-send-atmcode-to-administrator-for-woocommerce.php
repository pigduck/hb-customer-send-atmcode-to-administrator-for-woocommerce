<?php
/*
 * Plugin Name: HB Customer Send ATMCode To Administrator for WooCommerce
 * Plugin URI: https://piglet.me/ATMCode
 * Description: A Simple Customer Send ATMCode To Administrator for WooCommerce
 * Version: 0.1.3
 * Author: heiblack
 * Author URI: https://piglet.me
 * License:  GPL 3.0
 * License URI: http://www.gnu.org/licenses/gpl-3.0.txt
 *
 * Text Domain: hb-customer-send-atmcode-to-administrator-for-woocommerce
 * Domain Path: /languages
 *
 *
*/



class HEIBLACK_ATMSend_Simple {
    public function __construct() {
        if ( !defined( 'ABSPATH' ) ) {
            http_response_code( 404 );
            die();
        }
        if (! function_exists('plugin_dir_url')){
            return;
        }
        if (! function_exists( 'is_plugin_active' )){
            require_once (ABSPATH . 'wp-admin/includes/plugin.php');
        }
        if(! is_plugin_active( 'woocommerce/woocommerce.php' ) ) {
            return;
        }
        $this->init();
    }
    public  function init(){

        //Add 'Setting' link  Plugin
        $this->HBAddpluginlink();
        //Add Button in my-account/orders/ page
        $this->HBDecideState();
        //Create Page (order-send)
        $this->HBAddUserATMSendPage();
        //Create Meta Boxes in Woo order Post
        $this->HBAddAdminATMPage();
        //Create Setting Page
        $this->HBAddAdminATMSetting();
    }
    private function HBAddpluginlink(){
        add_filter('plugin_action_links_'.plugin_basename(__FILE__), function ( $links ) {
            $links[] = '<a href="' .
                admin_url( 'admin.php?page=wc-settings&tab=hbatm' ) .
                '">' . esc_html(__('Settings')) . '</a>';
            return $links;
        });


    }
    private function HBDecideState(){
        add_filter( 'woocommerce_my_account_my_orders_actions', function ( $actions, $order ) {

            //Add css only my-account/orders/ page
            wp_enqueue_style( 'HEIBLACK-ATM-SIMPLECSS', plugin_dir_url( __FILE__ ) . 'assets/style.css' );
            $hb_LicenseStatus   = get_option('_hb_LicenseStatus');
            //Remove redundant key
           unset($hb_LicenseStatus['_wpnonce'],$hb_LicenseStatus['_wp_http_referer'],$hb_LicenseStatus['save']);

            $hb_atms_status     =   [];
            if($hb_LicenseStatus){
                foreach ($hb_LicenseStatus as $key=>$value){
                    $hb_atms_status[] = str_replace('hbatm_wc-','',$key);
                }
            }

            $payment_method = $order->payment_method;

            //Show Button only payment method is bacs
            if ( $order->has_status( $hb_atms_status) && $payment_method=='bacs') {

                $hbatminfo = get_post_meta($order->ID,'_hbATMInfoHasRead');

                if(!empty($hbatminfo[0])){
                    $actions['order-sends'] = array(
                        'url'   => wp_nonce_url( add_query_arg( 'order-send', $order->get_id() ), 'hb_atm' ),
                        'name'  => esc_html(__( 'Send ATM Code', 'hb-customer-send-atmcode-to-administrator-for-woocommerce' )),
                    );
                }else{
                    $actions['order-send'] = array(
                        'url'   => wp_nonce_url( add_query_arg( 'order-send', $order->get_id() ), 'hb_atm' ),
                        'name'  => esc_html(__( 'Send ATM Code', 'hb-customer-send-atmcode-to-administrator-for-woocommerce' )),
                    );

                }

                return $actions;
            }
            return $actions;
        }, 9999, 2 );
    }
    private function HBAddUserATMSendPage(){
        add_action( 'init', function () {
            add_rewrite_endpoint( 'order-send', EP_ROOT | EP_PAGES );
        });
        add_action( 'woocommerce_account_order-send_endpoint', function ($post) {
            if ( isset( $_GET['order-send'], $_GET['_wpnonce'] ) && is_user_logged_in() && wp_verify_nonce( wp_unslash( $_GET['_wpnonce'] ), 'hb_atm' ) ){
                $order = wc_get_order( $post );

                $HBATM5numbers   = __( 'Must 5 numbers', 'hb-customer-send-atmcode-to-administrator-for-woocommerce' );
                $HBATMbeanumbers = __( 'Must be a number', 'hb-customer-send-atmcode-to-administrator-for-woocommerce' );


                echo "<script>const HBATM5numbers = \" ".esc_html($HBATM5numbers)."\"; const HBATMbeanumbers = \" ".esc_html($HBATMbeanumbers)."\";</script>";
                wp_enqueue_script('HEIBLACK-ATM-SIMPLEJS', plugin_dir_url(__FILE__) . 'assets/hb_atm.js');
                wp_enqueue_style( 'HEIBLACK-ATM-SIMPLECSS', plugin_dir_url( __FILE__ ) . 'assets/style.css' );

                //invalid order
                if (!$order) {
                    esc_html_e('invalid order',"hb-customer-send-atmcode-to-administrator-for-woocommerce" );
                    return;
                }

                if(@$_POST && wp_verify_nonce( $_POST['_wpnonce'], '_hb-customer-send-atmcode') ){
                    $Safetyarray = array();
                    $Safetyarray['ATMcode'] = sanitize_text_field($_POST['ATMcode']);
                    $Safetyarray['ATMtime'] = sanitize_text_field($_POST['ATMtime']);
                    if(strlen($Safetyarray['ATMcode'])<=5) {

                        $json = wp_json_encode($Safetyarray);
                        $isUpdte = update_post_meta($post, '_hbATMisUpdte', serialize($json));

                        //if there is an update...
                        if ($isUpdte) {
                            update_post_meta($post, '_hbUserATMisUpdte', $isUpdte);
                            $HB_lINE_notify     = get_option('HB_lINE_notify');
                            $HB_wc_mail_notify  = get_option('HB_wc_mail_notify');
                            if($HB_lINE_notify == 'yes' || $HB_wc_mail_notify == 'yes'){
                                $HB_lINE_notify_token       = get_option('HB_lINE_notify_token');
                                $HB_notification_content    = get_option('HB_notice_content');
                                $orders                     = wc_get_order($post);
                                $users                      = $orders->get_user();
                                $order_data                 = $orders->get_data();
                                $order_total                = $order_data['total'];
                                $orderid                    = $post;


                                $message = str_replace('[[order]]', esc_textarea($orderid), esc_textarea($HB_notification_content));
                                $message = str_replace('[[user]]',  esc_textarea($users->nickname), $message);
                                $message = str_replace('[[price]]', esc_textarea($order_total), $message);
                                $message = str_replace('[[date]]',  esc_textarea($Safetyarray['ATMtime']), $message);
                                $message = str_replace('[[code]]',  esc_textarea($Safetyarray['ATMcode']), $message);

                                if ($HB_lINE_notify == 'yes') {
                                    $request_params = array(
                                        "headers" => "Authorization: Bearer ".esc_textarea($HB_lINE_notify_token),
                                        "body" => array(
                                            "message" => esc_textarea($message)
                                        )
                                    );
                                    $result = wp_remote_post('https://notify-api.line.me/api/notify', $request_params);
                                }

                                if ($HB_wc_mail_notify == 'yes') {

                                    $HB_lINE_Mail_Address   = get_option('HB_lINE_Mail_Address');
                                    $HB_mail_Address        = get_option('HB_mail_Address');

                                    wp_mail(esc_attr($HB_mail_Address), esc_attr($HB_lINE_Mail_Address), esc_textarea($message));
                                }
                            }
                        }
                    }
                }
                $hasuserUP      = get_post_meta($post, '_hbUserATMisUpdte');
                $user           = $order->get_user_id();
                $currentUser    = get_current_user_id();

                //nvalid order
                if ($user!=$currentUser) {
                    esc_html_e('invalid order',"hb-customer-send-atmcode-to-administrator-for-woocommerce" );
                    return;
                }
                update_post_meta($post, '_hbATMInfoHasRead', '');
                $moneyTransferMessage               =   get_post_meta($post,'_hbATMisUpdte',true);
                if(!$moneyTransferMessage){
                    $MessageDate ='';
                    $MessageCode ='';
                }else{
                    $unmoneyTransferMessage         =   json_decode(unserialize($moneyTransferMessage));
                    //HB-sendMessage 57
                    $MessageDate                    =   $unmoneyTransferMessage->ATMtime;
                    //HB-sendMessage 61
                    $MessageCode                    =   $unmoneyTransferMessage->ATMcode;
                }
                $orderids           = $order->ID;
                $esc_hbatminfo      = '';
                if ($hbatminfo      = get_post_meta($orderids,'_hbatminfo')){
                    $esc_hbatminfo  = esc_textarea($hbatminfo[0]);
                }

                $hasUpdate          = get_post_meta($orderids,'_hbATMInfoHasRead');
                require_once dirname(__FILE__) . '/page/HB-sendMessage.php';
            }

        },10,1 );
    }
    private function HBAddAdminATMPage(){
        add_action( 'add_meta_boxes', function () {
            add_meta_box(
                'HB_ATM_send_tools',
                esc_html(__( 'ATM Message',"hb-customer-send-atmcode-to-administrator-for-woocommerce" )),
                'hbatminfofunction',
                'shop_order',
                'side',
                'default'
            );
        });
        function hbatminfofunction($order){

            wp_nonce_field( 'hbatminfofunction', 'hbatminfofunction_nonce' );

            $esc_hbatminfo = '';
            if ($hbatminfo = get_post_meta($order->ID,'_hbatminfo')){
                $esc_hbatminfo = $hbatminfo[0];
            }

            require_once dirname(__FILE__) . '/page/HB-SendMessageView.php';

        }
        add_action( 'save_post', function ( $post_id ) {
            if ( ! isset( $_POST[ 'hbatminfofunction_nonce' ] ) ) {
                return $post_id;
            }
            $nonce = sanitize_text_field($_REQUEST[ 'hbatminfofunction_nonce' ]);
            if ( ! wp_verify_nonce( $nonce, 'hbatminfofunction' ) ) {
                return $post_id;
            }
            if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
                return $post_id;
            }
            if ( 'shop_order' == $_POST[ 'post_type' ] ) {
                if ( ! current_user_can( 'edit_page', $post_id ) ) {
                    return $post_id;
                }
            } else {
                return $post_id;
            }

            $result = update_post_meta( $post_id, '_hbatminfo', sanitize_text_field($_POST['hbatminfo']) );

            update_post_meta($post_id, '_hbATMInfoHasRead', $result);

            if($result){
                update_post_meta($post_id, '_hbUserATMisUpdte', '');
            }
        }, 10, 1 );

    }
    private function HBAddAdminATMSetting(){
        add_filter( 'woocommerce_get_settings_pages',  function ( $settings ) {
            $settings[] = require_once dirname(__FILE__) . '/page/HB-Settings.php';
            return $settings;
        } );


    }


}

new HEIBLACK_ATMSend_Simple();










