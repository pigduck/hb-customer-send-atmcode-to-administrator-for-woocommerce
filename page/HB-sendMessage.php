<div class="HB-Our-bank-details">
    <h3>
        <?php esc_html_e( 'Our bank details', 'woocommerce' );?>
    </h3>
    <?php
    $bacs_accounts_info = get_option( 'woocommerce_bacs_accounts');
    foreach ($bacs_accounts_info as $value):?>
        <?php echo "<div>"; ?>
        <?php if($value['account_name']):?>
            <?php esc_html_e( 'Account name', 'woocommerce' );?>:
            <?php echo esc_textarea($value['account_name']); ?>
            <br>
        <?php endif?>

        <?php if($value['bank_name']):?>
            <?php esc_html_e( 'Bank', 'woocommerce' );?>:
            <?php echo esc_textarea($value['bank_name']); ?>
            <br>
        <?php endif?>
        <?php if($value['sort_code']):?>
            <?php echo _e( 'Sort code', 'woocommerce' );?>:
            <?php echo esc_textarea($value['sort_code']); ?>
            <br>
        <?php endif?>
        <?php if($value['account_number']):?>
            <?php esc_html_e( 'Account number', 'woocommerce' );?>:
            <?php echo esc_textarea($value['account_number']); ?>
            <br>
        <?php endif?>
        <?php if($value['iban']):?>
            <?php echo esc_html_e( 'IBAN', 'woocommerce' );?>:
            <?php echo esc_textarea($value['iban']); ?>
            <br>
        <?php endif?>
        <?php if($value['bic']):?>
            <?php esc_html_e( 'BIC / Swift', 'woocommerce' );?>:
            <?php echo esc_textarea($value['bic']); ?>
            <br>
        <?php endif?>
        <hr>
        <?php echo "</div>"; ?>
    <?php endforeach;?>
    <?php if($esc_hbatminfo && $hasuserUP[0]!='1'):?>
        <div class="alertinfo" role="alert">
            <?php echo  esc_textarea($esc_hbatminfo);?>
        </div>
    <?php endif;?>
    <?php if(!$esc_hbatminfo && $MessageCode && $MessageDate):?>
        <div class="alertsuccess" role="alert">
            <?php esc_html_e('You have successfully submitted your message, please be patient!', 'hb-customer-send-atmcode-to-administrator-for-woocommerce')?>
        </div>
    <?php endif;?>
    <?php if($esc_hbatminfo && $hasuserUP[0]=='1'):?>
        <div class="alertlight" role="alert">
            <?php esc_html_e('You have completed the update message, please be patient!', 'hb-customer-send-atmcode-to-administrator-for-woocommerce')?>
        </div>
    <?php endif;?>
</div>
<form action="" method="post" id="hbATM">
    <label for="date">
        <h3><?php esc_html_e('Remittance time', 'hb-customer-send-atmcode-to-administrator-for-woocommerce')?></h3>
        <input id="hb-atm-time" type="date" name="ATMtime" value="<?php echo esc_textarea($MessageDate) ?>">
    </label>
    <label for="text">
        <h3><?php esc_html_e('The last five digits of your personal bank account', 'hb-customer-send-atmcode-to-administrator-for-woocommerce')?></h3>
        <input id="hb-atm-text" type="text" name="ATMcode" value="<?php echo esc_textarea($MessageCode)?>" autocomplete="off">
        <?php wp_nonce_field( '_hb-customer-send-atmcode');?>
    </label>
    <?php if($MessageDate!=''):?>
        <input type="submit" value="<?php esc_html_e('Update', 'hb-customer-send-atmcode-to-administrator-for-woocommerce')?>">
    <?php else:?>
        <input type="submit" value="<?php esc_html_e('Send', 'hb-customer-send-atmcode-to-administrator-for-woocommerce')?>">
    <?php endif;?>
</form>



