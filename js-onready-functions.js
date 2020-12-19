/*======================================================================================================================
 |=======================================================================================================================
 |
 |       BEGIN.READY
 |       Document Ready >>>>> function(){
 |
 |=======================================================================================================================
 +======================================================================================================================*/

var $=jQuery.noConflict(); // without this the following onready will fail.

function clg(){}


/**- - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -*/
///////////////////////////////////////////////// ON-READY BEGIN ///////////////////////////////////////////////////////
/**- - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -*/
$( function() { //===> ========On_Ready_functions=======

  function onReady(){} // this is just a placeholder so I can find the starting point

   var md = new MobileDetect(
      'Mozilla/5.0 (Linux; U; Android 4.0.3; en-in; SonyEricssonMT11i' +
      ' Build/4.1.A.0.562) AppleWebKit/534.30 (KHTML, like Gecko)' +
      ' Version/4.0 Mobile Safari/534.30');


  /*
  clg( 'JS ON READY FUNCTIONS FOUND' ,1);
  clg( 'USER-GUID ====='+myAjax.user_guid,1);
  clg( 'ORDER-GUID ====='+myAjax.order_guid,1);
  clg( 'ITEM-GUID ====='+myAjax.item_guid,1);
  clg( 'SHOPPING CART PAGE ===== '+myAjax.shopping_cart, 1);
  clg( 'ADDRESS INFO PAGE ===== '+myAjax.address_info_page, 1);
  clg( 'SHIPPING INFO PAGE ===== '+myAjax.shipping_page, 1);
  clg( 'UPLOADER PAGE ===== '+myAjax.upload_page, 1);
  */

/**-------------------------------------------------------------------------------
|
|   Declare JS global vars here
|
*-------------------------------------------------------------------------------*/
$(document).on('click', function(e) {

   //var source = event.target || event.srcElement;
   //clg(source,1);

});

/**-------------------------------------------------------------------------------
|
|   This method is invoked when the "Select File to Upload is selected"
|
*-------------------------------------------------------------------------------*/
$('#selected-upload-image').on('load',function(){

   clg('## IMAGE LOADED ##');
   _orig_image_width  = this.naturalWidth;
   _orig_image_height = this.naturalHeight;
   _image_width   = this.naturalWidth;
   _image_height  = this.naturalHeight;

});

   /**-------------------------------------------------------------------------------
    |
    |   This method is invoked when the "Select File to Upload is selected"
    |
    *-------------------------------------------------------------------------------*/
   $('#selected-upload-image').on('click',function(){

      clg('## IMAGE LOADED ##',1);

   })


   $('#billing_info').focusout(function(){

      process_shipping_address_info()


   });

   $('#shipping_info').focusout(function(){

      clg('######### shipping-info-class-body - FOCUS-OUT ######',1);

   });

   $('#payment-details-class-body').focusout(function(){


   });


   /**-------------------------------------------------------------------------------
    |
    |  these functions watch the open and close of the panel options in the configurator
    |
    *-------------------------------------------------------------------------------*/

   $( document.getElementById('collapseDetail') ).on('show.bs.collapse', function () {
      if( detector.mobile()) {
         $(document.getElementById('product-detail-btn-closed')).addClass('hide')
         $(document.getElementById('product-detail-btn-open')).removeClass('hide')
         hide_options_mobile('product_details')
      }
   })
   $( document.getElementById('collapseDetail') ).on('hide.bs.collapse', function () {
      if( detector.mobile()) {
         $(document.getElementById('product-detail-btn-open')).addClass('hide')
         $(document.getElementById('product-detail-btn-closed')).removeClass('hide')
         hide_options_mobile('none')
         reset_offset_from_top()
      }
   })

   $( document.getElementById('collapsePreviews') ).on('show.bs.collapse', function () {
      if( detector.mobile()) {
         $(document.getElementById('product-preview-btn-closed')).addClass('hide')
         $(document.getElementById('product-preview-btn-open')).removeClass('hide')
         hide_options_mobile('previews')
      }
   })
   $( document.getElementById('collapsePreviews') ).on('hide.bs.collapse', function () {
      if( detector.mobile()) {
         $(document.getElementById('product-preview-btn-open')).addClass('hide')
         $(document.getElementById('product-preview-btn-closed')).removeClass('hide')
         hide_options_mobile('none')
         reset_offset_from_top()
      }
   })

   $( document.getElementById('collapseOne') ).on('show.bs.collapse', function () {
      if( detector.mobile()){
         $( document.getElementById('product-sizes-btn-closed') ).addClass('hide')
         $( document.getElementById('product-sizes-btn-open') ).removeClass('hide')
         hide_options_mobile('size_options')
      }
   })
   $( document.getElementById('collapseOne') ).on('hide.bs.collapse', function () {
      if( detector.mobile()) {
         $(document.getElementById('product-sizes-btn-open')).addClass('hide')
         $(document.getElementById('product-sizes-btn-closed')).removeClass('hide')
         hide_options_mobile('none')
         reset_offset_from_top()
      }
   })

   $( document.getElementById('collapseTwo') ).on('show.bs.collapse', function () {
      if( detector.mobile()) {
         $(document.getElementById('product-frame-btn-closed')).addClass('hide')
         $(document.getElementById('product-frame-btn-open')).removeClass('hide')
         hide_options_mobile('frame_options')
      }
   })
   $( document.getElementById('collapseTwo') ).on('hide.bs.collapse', function () {
      if( detector.mobile()) {
         $(document.getElementById('product-frame-btn-open')).addClass('hide')
         $(document.getElementById('product-frame-btn-closed')).removeClass('hide')
         hide_options_mobile('none')
         reset_offset_from_top()
      }
   })

   $( document.getElementById('collapseThree') ).on('show.bs.collapse', function () {
      if( detector.mobile()) {
         $(document.getElementById('product-matting-btn-closed')).addClass('hide')
         $(document.getElementById('product-matting-btn-open')).removeClass('hide')
         hide_options_mobile('matting_options')
      }
   })
   $( document.getElementById('collapseThree') ).on('hide.bs.collapse', function () {
      if( detector.mobile()) {
         $(document.getElementById('product-matting-btn-open')).addClass('hide')
         $(document.getElementById('product-matting-btn-closed')).removeClass('hide')
         hide_options_mobile('none')
         reset_offset_from_top()
      }
   })

/**- - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -*/
/////////////////////////////////////////////////// ON-READY END ///////////////////////////////////////////////////////
/**- - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -*/
});
