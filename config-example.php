<?php
    // FILESTACK https://dev.filestack.com/
    define('FRAMEDWARE_FILESTACK_API_KEY', ''); // Filestack API key
    define('FRAMEDWARE_FILESTACK_SECRET', ''); // Filestack secret

    // CRON JOB
    define('FRAMEDWARE_CRON_UPLOADS_DELETE', 1); // Delete transactional uploads (images). For on/off put 1/0
    define('FRAMEDWARE_CRON_UPLOADS_DAYS', 0); // number of days in the past

    define('FRAMEDWARE_CRON_PRODUCTS_DELETE', 1); // Delete products associated with orders for orders that are older than specified number of days. For on/off put 1/0
    define('FRAMEDWARE_CRON_PRODUCT_DAYS', 400); // number of days in the past

    // PAYPAL
    define('PAYPAL_SANDBOX', true);