<?php


class Settings_ATMPage extends WC_Settings_Page {
    public function __construct() {
        $this->id    = 'hbatm';
        $this->label = esc_html(__( 'HB ATM', 'hb-customer-send-atmcode-to-administrator-for-woocommerce' ));
        parent::__construct();
    }
    public function get_sections() {
        $sections = array(
            ''              => esc_html(__( 'General', 'hb-customer-send-atmcode-to-administrator-for-woocommerce' )),
            'Notice'        => esc_html(__( 'Notice', 'hb-customer-send-atmcode-to-administrator-for-woocommerce' )),
        );
        return apply_filters( 'woocommerce_get_sections_' . $this->id, $sections );
    }
    public function output() {
        global $current_section;
        if ( 'notice' != $current_section ) {
            $displayed = __('When is it displayed?','hb-customer-send-atmcode-to-administrator-for-woocommerce');
            echo "<h2>".esc_html($displayed)."</h2>";
        }
        $settings = $this->get_settings( $current_section );
        WC_Admin_Settings::output_fields( $settings );
    }
    public function save() {
        global $current_section;
        $settings = $this->get_settings( $current_section );
        WC_Admin_Settings::save_fields( $settings );
        if ($current_section=='') {

            $postdata   =  $_POST;

            unset($postdata['_wpnonce'],$postdata['_wp_http_referer'],$postdata['save']);

            $Safetypostdata = [];
            foreach ($postdata as $key=>$value){
                $Safetypostdata[sanitize_key($key)]= sanitize_text_field($value);
            }

            update_option('_hb_LicenseStatus',$Safetypostdata);
        }
        if ( $current_section ) {
            do_action( 'woocommerce_update_options_' . $this->id . '_' . $current_section );
        }
    }
    public function get_settings( $current_section = '' ) {
        $wcStatuses     =   wc_get_order_statuses();
        $settings       = [];
        if ($current_section=='') {
            foreach ($wcStatuses as $key => $value) {
                $settings[] = [
                    'title' => '',
                    'desc' => $value,
                    'id' => 'hbatm_' . $key,
                    'type' => 'checkbox',
                    'default' => 'no',
                ];
            }
            $settings[] = [
                'type'  => 'sectionend',
                'id'    => 'checkout_page_options',
            ];
        }else if ($current_section=='notice'){

                $settings[] = [
                    'name' => esc_html(__('Notice', 'hb-customer-send-atmcode-to-administrator-for-woocommerce')),
                    'type' => 'title',
                    'desc' => '',
                ];
                $settings[] = [
                    'name'  => esc_html(__('LINE Notify(Recommend)', 'hb-customer-send-atmcode-to-administrator-for-woocommerce')),
                    'type'  => 'checkbox',
                    'id'    => 'HB_lINE_notify',
                ];
                $settings[] = [
                    'type'  => 'text',
                    'id'    => 'HB_lINE_notify_token',
                    'desc'  => esc_html(__( 'LINE Notify Token','hb-customer-send-atmcode-to-administrator-for-woocommerce')),
                ];
                $settings[] = [
                    'name'  => esc_html(__('WP Mail', 'hb-customer-send-atmcode-to-administrator-for-woocommerce')),
                    'type'  => 'checkbox',
                    'id'    => 'HB_wc_mail_notify',
                ];
                $settings[] = [
                    'type'  => 'email',
                    'desc'  => esc_html(__( 'Mail Address','hb-customer-send-atmcode-to-administrator-for-woocommerce')),
                    'id'    => 'HB_mail_Address'
                ];
                $settings[] = [
                    'type'  => 'text',
                    'id'    => 'HB_lINE_Mail_Address',
                    'desc'  => esc_html(__( 'Mail Title','hb-customer-send-atmcode-to-administrator-for-woocommerce')),
                ];
                $settings[] = [
                    'type'  => 'sectionend',
                    'id'    => 'checkout_page_options',
                ];
                $settings[] = [
                    'name' => esc_html(__('Content', 'hb-customer-send-atmcode-to-administrator-for-woocommerce')),
                    'type' => 'title',
                ];
                $settings[] = [
                    'type'  => 'textarea',
                    'id'    => 'HB_notice_content',
                    'desc'  => esc_html(__( '[[order]] :Order ID / [[user]] :User Name  / [[price]] :Order Price / [[date]] User Enter Date  / [[code]] User Enter Code','hb-customer-send-atmcode-to-administrator-for-woocommerce')),
                ];
                $settings[] = [
                    'type'  => 'sectionend',
                    'id'    => 'checkout_page_options',
                ];
        }
        return apply_filters( 'woocommerce_get_settings_' . $this->id, $settings, $current_section );
    }
}

new Settings_ATMPage();