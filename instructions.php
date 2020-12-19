<?php
    echo '
# Configuration
    1. Activate plugin. Product category "Uploads" is automatically created, if it does not already exist.
    2. Connect WooCommerce in Plugin Admin Settings.
    3. Create WooCommerce webhook with following required data:
        * Status: Active
        * Topic: Order created        
        * Delivery URL: ' . FRAMEDWARE_SITE_URL . '/framedware/woo-webhook-order-complete
        * API version: v2
    4. Add the shortcodes bellow, to the page(s) of your choice.
    5. Create server cron job (daily) to run ' . FRAMEDWARE_SITE_URL . '/framedware/cron
        

# SHORTCODE parameters
    SINGLE FILE
    [framedeware_uploader]  
    [framedeware_configurator]
    
    GALLERY WALLS
    [framedeware_gallery_wall_1x3]
    [framedeware_gallery_wall_2x4]
    [framedeware_gallery_wall_3x3]
    [framedeware_gallery_wall_4x3]
    [framedeware_gallery_wall_stairway]


# MANUALLY
    * To manually run cron job script click <a href="' . FRAMEDWARE_SITE_URL . '/framedware/cron" target="_blank">here</a>.
    * To manually create pickup location order folders click <a href="' . FRAMEDWARE_SITE_URL . '/framedware/location/prep" target="_blank">here</a>.
    * To manually delete upload files, FTP to: /public_html/uploadhandler/uploads/     (NOTE: DO NOT delete `image_assets` folder) 
    * To change frontend configuration (prices, filestack), FTP to: /public_html/wp-content/plugins/framedware/config.js
    * To change backend configuration (cron job, PayPal), FTP to: /public_html/wp-content/plugins/framedware/config.php
';