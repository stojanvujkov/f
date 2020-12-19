jQuery(document).ready( function()
{
    jQuery('#framedware-consumer-key').focus();

    jQuery("#framedware-woocommerce-connect").click( function(e)
    {
        e.preventDefault();

        jQuery.ajax({
            async: false,
            type: 'post',
            dataType: 'json',
            url: framedwareWriteAjax.ajaxurl,
            data:
            {
                action: 'framedware_db_insert_wc',
                consumerKey: jQuery('#framedware-consumer-key').val(),
                consumerSecret: jQuery('#framedware-consumer-secret').val()
            },
            success: function(response)
            {
                if(typeof response.success !== 'undefined' && response.success == '1')
                {
                    location.reload();
                    return;
                }

                alert('Unable to connect to WooCommerce. ' + response.msg);
            },
            error: function()
            {
                alert('Unable to connect to WooCommerce. Call failed.');
            }
        })
    });

    jQuery("#framedware-woocommerce-disconnect").click( function(e)
    {
        e.preventDefault();

        jQuery.ajax({
            async: false,
            type: 'post',
            dataType: 'json',
            url: framedwareWriteAjax.ajaxurl,
            data:
            {
                action: 'framedware_db_delete_wc'
            },
            success: function(response)
            {
                if(typeof response.success !== 'undefined' && response.success == '1')
                {
                    location.reload();
                }
                else
                {
                    alert(response);
                }
            }
        })
    });
});
