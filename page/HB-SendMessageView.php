<div class="order_data_columns">
    <?php
    $moneyTransferMessage           =   get_post_meta($order->ID,'_hbATMisUpdte',true);
    $orders                         =   wc_get_order( $order );
    $order_data                     =   $orders->get_data();
    $order_total                    =   $order_data['total'];
    ?>
    <?php if($moneyTransferMessage){
        $unmoneyTransferMessage         =   json_decode(unserialize($moneyTransferMessage));
        $MessageDate                    =   $unmoneyTransferMessage->ATMtime;
        $MessageCode                    =   $unmoneyTransferMessage->ATMcode;
        echo '<strong>' . esc_html(__( 'Remittance Date','hb-customer-send-atmcode-to-administrator-for-woocommerce' )) . ':</strong>' . esc_textarea($MessageDate);
        echo '<BR>';
        echo '<strong>' . esc_html(__( 'Code', 'hb-customer-send-atmcode-to-administrator-for-woocommerce')) . ':</strong>' . esc_textarea($MessageCode);
        echo '<BR>';
        echo '<strong>' . esc_html(__( 'Order Amount', 'hb-customer-send-atmcode-to-administrator-for-woocommerce')) . ':</strong>' . wc_price(esc_textarea($order_total));
    }
    ?>
    <?php if($moneyTransferMessage):?>
    <br>
    <hr>
    <strong><?php esc_html_e( 'Message To Customer', 'hb-customer-send-atmcode-to-administrator-for-woocommerce');?></strong>
    <textarea name="hbatminfo" id="hbatminfo" style="width: 100%"><?php echo esc_textarea($esc_hbatminfo);?></textarea>
    <?php else:?>
    <?php esc_html_e( 'Customer did not send content', 'hb-customer-send-atmcode-to-administrator-for-woocommerce'); ?>
    <?php endif;?>
</div>