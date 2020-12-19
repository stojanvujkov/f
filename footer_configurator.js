
$.LoadingOverlay("show");

/**-------------------------------------------------------------------------------
 |   Document Ready
 *-------------------------------------------------------------------------------*/
$(document).ready(function(){

    init_fineuploader(); // => put_product_config_details()

    if( detector.mobile()) {
        $('#frame-details-list-group').addClass('hide');
    } else {
        $('#frame-details-list-group').removeClass('hide');
        $('#product-description-card-body').css('width', '500px !important');
    }

    $("#collapseFour").on("shown.bs.collapse", function(){
        $('#product-description-card-body').scrollTop(900);
    });
    $("#collapseThree").on("shown.bs.collapse", function(){
        $('#product-description-card-body').scrollTop(155);
    });
    $("#collapseTwo").on("shown.bs.collapse", function(){
        $('#product-description-card-body').scrollTop(115);
    });
    $("#collapseOne").on("shown.bs.collapse", function(){
        $('#product-description-card-body').scrollTop(80);
    });
});

/**=========================================================
 *  window ready - when images are loaded
 *----------------------------------------------------------*/
$(window).load(function(){

    getAjaxProductJSON_load_konva('framed');

    if ( detector.mobile() ){
        $('#product-detail-mobile').removeClass('hide');
        $('#preview-options-mobile').removeClass('hide');
        $('#product-card-footer').addClass('hide')
        $('#product-description-card-body').addClass('product-description-card-body-mobile')

    } else {
        $('#product-detail-mobile').addClass('hide');
        $('#preview-options-mobile').addClass('hide');
        $('#product-card-footer').removeClass('hide')
        $('#product-description-card-body').removeClass('product-description-card-body-mobile')
    }

    // listen for the show modal event, then set the title after it's opened
    $('#frame-preview-modal').on('shown.bs.modal',function(){
        //
        var $frame_description = $('#list-item-frame').text();
        //
        $('#preview-modal-title').text( $frame_description )
    })
});
