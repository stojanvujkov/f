<?php
    global $wpdb;
    $table_name = 'fware_woo';
    $query = $wpdb->get_results( 'SELECT * FROM ' . $table_name . ' LIMIT 1;', ARRAY_A )[0];
?>

<div class="container">
    <h1>FramedWare Settings</h1>
</div>

<div class="container">
    <h3>WooCommerce API Connect</h3>
    <!--<span class="description">Use this API key to connect to WooCommerce</span>-->
    <?php if(empty($query['woo_consumer_key']) || empty($query['woo_consumer_secret'])) {?>
        <table class="form-table">
            <tr valign="top">
                <th scope="row" class="titledesc">
                    <label for="framedware-consumer-key">Consumer key</label>
                </th>
                <td class="forminp">
                    <fieldset>
                        <legend class="screen-reader-text"><span>Consumer key</span></legend>
                        <input class="input-text regular-input required" type="text" name="framedware-consumer-key" id="framedware-consumer-key" style="" value="" placeholder="" />
                    </fieldset>
                </td>
            </tr>
            <tr valign="top">
                <th scope="row" class="titledesc">
                    <label for="framedware-consumer-secret">Consumer secret</label>
                </th>
                <td class="forminp">
                    <fieldset>
                        <legend class="screen-reader-text"><span>Consumer secret</span></legend>
                        <input class="input-text regular-input required" type="text" name="framedware-consumer-secret" id="framedware-consumer-secret" style="" value="" placeholder="" />
                    </fieldset>
                </td>
            </tr>
        </table>
        <button class="button button-primary" id="framedware-woocommerce-connect" type="button">Save</button>
    <?php } else { ?>
        <p>You are now connected to WooCommerce.</p>
        <button class="button button-primary" id="framedware-woocommerce-disconnect" type="button">Disconnect</button>
    <?php }?>
</div>

<div class="container">
    <h3>Reports</h3>
    <a href="<?php echo FRAMEDWARE_SITE_URL . '/wp-admin/admin.php?page=framedware_report'; ?>">
        <button class="button">Reports</button>
    </a>
</div>

<div class="container">
    <h3>Instructions</h3>
    Loaded from <?php echo PLUGINPATH . 'instructions.php'; ?>
    <pre class="install-instructions">
<?php
    include (PLUGINPATH . 'instructions.php');
?>
    </pre>
</div>