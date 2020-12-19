var $framedware_flag;
var $fineUploader;
var $page_prefix;

//@@@@@
var _CART_PREVIEW_LANDSCAPE = 180;
var _CART_PREVIEW_PORTRAIT = 170;
var max_length_inches = 41
var min_length_inches = 5
var $uploader_scroll_offset = 280;
var $configurator_scroll_offset = 150;
//var $cutoff_time = " 9:01:00";

var detector = new MobileDetect(window.navigator.userAgent)

var $md; // this is a global var to detect the browser size and agent

/*
if ($framedware_flag == 1){
   var $uploadServer = 'https://demo.framedware.net'; // define the upload server
   $page_prefix = '';

} else if ( $framedware_flag == 2 ){
   var $uploadServer = 'https://bestframing.ferdware.net'; // define the upload server
   $page_prefix = '/index.php';
}
*/

const formatter = new Intl.NumberFormat('en-US', {
   style: 'currency',
   currency: 'USD',
   minimumFractionDigits: 2
});

var $setUUID = false;
var $globalUUID = null;
var $cropped_image_upload = false;
var $global_file_image_selected = null;

var $file;

var $ // needed for jquery onready - do not remove!

// generic FrameProductJSON
$FrameProductJSON = {
    frame_border_img: 'bw26042-1.25in-splice.png',
    frame_guid: 'bdeede50-c86a-4fe2-9531-a3586ec6626f',
    frame_width: 54,
    frame_height: 0,
    frame_size_width: 0,
    frame_size_height: 0,
    frame_description: 'Black Satin 3/4in',
    frame_number: '',
    full_path_img_scaled_filename: '',
    glass_type: 'None',
    height: 0,
    id: '',
    img_guid: '',
    img_scaled_width: 0,
    img_scaled_height: 0,
    img_width: 0,
    img_height: 0,
    img_aspect_ratio: 0,
    img_scale_factor: 1,
    img_scaled_filename: '',
    img_original_filename: '',
    img_screen_scale_factor_width: 1,
    img_screen_scale_factor_height: 1,
    img_filename: '',
    img_orientation: null,
    img_max_screen_size: 300,
    item_guid: '',
    item_category: '',
    innerMBWidth: 0,
    innerMBEnabled: false,
    innerMBColor: 'none',
    innerMBColorName: 'none',
    innerMBprice: 0,
    invisible_glass: 0,
    invisible_glass_price: 0,
    materialJSON: '',
    mb1_enabled: '',
    mb1_color: '#ffffff',
    mb1_color_name: 'White',
    mb1_size: 0,
    mb1_width: 144,
    mb1_width_text: '2 in',
    mounting: 0,
    mounting_description: 'No Mounting',
    mode: '',
    print_price: 0,
    printSizes: '',
    print_width: 0,
    print_height: 0,
    pricingJSON: '',
    pricing_grid_mode: 'custom_custom',
    product_mode: '',
    quick_sizes: '',
    scaled_filename: '',
    set_inside_frame_width: 0,
    set_inside_frame_height: 0,
    set_inside_top_mb_frame_height: '',
    set_inside_top_mb_frame_width: '',
    user_guid: '',
    width: 0,
    framesJSON: '',
    type: 'custom',
    wall: {}
};

var _MOBILE_MAX_SCREEN_WIDTH = 300;
var _MOBILE_MAX_SCREEN_HEIGHT = 200;
var _NON_MOBILE_MAX_SCREEN_WIDTH = 400;
var _NON_MOBILE_MAX_SCREEN_HEIGHT = 400;
var _SELECTED_UPLOAD_IMAGE_MAX_WIDTH = 320;
var _SHOPPING_CART_THUMBNAIL_MAX_WIDTH = 150;
var _SHOPPING_CART_THUMBNAIL_MAX_HEIGHT = 200;

var print_object = {
  height: 0,
  width: 0,
  price_category: 0,
};

var pricing = {
  total_price: 0,
  frame_price: 0,
  print_price: 0,
  mb_price: 0,
  inner_mb_price: 0,
  glass_price: 0,
  mounting_price: 0,
  print_sqFt: 0,
};

var frame_details = {
  id: 1,
  frame_guid: "d1dc170d-4eb0-4718-91e8-0600d1651201",
  frame_name: "bw220048",
  frame_border_img: "bw26042-1.25in-splice.png",
  frame_width: 54,
  frame_border_slice: "bw26042-1.25in-splice.png",
  frame_sku: "bw220048",
  frame_description: "Black Satin",
  frame_size_inches: 0.75,
  frame_weight: 1,
  frame_preview_img: "bw220048-FrontStraight.png",
  frame_select_img: "bw220048-preview.png",
  frame_status: 1,
  frame_cost: 1,
  frame_price: 0.50,
};

var $_tmpProductJSON;

var $computed_tax_rate = 0.0775

var _widthOfMB = 108; // 72dpi = 1in -> 108dpi = 1.5in;

/**- - - - - - - -  - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - */
//////////////////////////////////// Konva Data /////////////////////////////////
/**- - - - - - - -  - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - */

var _borderImage = new Image();
var _sideCanvasImage = new Image();
var _backgroundCanvasImage = new Image();
var _sideFlatbedImage = new Image();
var _mainImage = new Image();
var _backgroundImage = new Image();
var _blankImgPlaceholder = new Image();

var _blankImageSrc = "../../../uploadhandler/uploads/image_assets/blank-image-placeholder.jpg"

// these images represent the background images required for each side of the frame
var _stage_height = 200;
var _stage_width  = 300;

var tmp_configLayer = null;
var tmp_mainImage   = null;
var tmp_leftSide    = null;
var tmp_rightSide   = null;
var tmp_topSide     = null;
var tmp_bottomSide  = null;
var tmp_mbLeftSide  = null;
var tmp_mbRightSide = null;
var tmp_mbTopSide    = null;
var tmp_mbBottomSide = null;
var tmp_configKonvaStage = null;
var tmp_mbTopInnerShadow = null;
var tmp_mbRightInnerShadow = null;
var tmp_mbBottomInnerShadow = null;
var tmp_mbLeftInnerShadow = null;

var _backgroundImgObj = new Image();
var _productImgObj = new Image();

var _SrcImageX = 0;
var _SrcImageY = 0;

///////////////// frame data /////////////////
var _point0 = 300;
var _pointXA = 0; // top right x
var _pointXB = 0; // top right inside X
var _pointXC = 0; // top right inside y
var _pointXD = 0; // bottom left inside x
var _pointXE = 0; // bottom left inside y

var _pointYA = 0;
var _pointYB = 0;
var _pointYC = 0;
var _pointYD = 0;
var _pointYE = 0;

var _scale_factor = 1;
var _BottomSideOffsetX = 0;
var _BottomSideOffsetY = 0;
var _RightSideOffsetX = 0;
var _RightSideOffsetY = 0;
var _LeftSideOffsetX = 0;
var _LeftSideOffsetY = 0;

var _LengthX = 504;
var _LengthY = 504;

var _RectX = 108;
var _RectY = 108;

var _RightSideX = 0;
var _RightSideY = 0;
var _LeftSideX = 0;
var _LeftSideY = 0;
var _BottomSideX = 0;
var _BottomSideY = 0;

var _widthOfFrame = 108; // 72dpi = 1in -> 108dpi = 1.5in
var _scaled_width_of_frame = 0;

var _shadowBlur = 30;
var _ShadowOffsetX = 5;
var _ShadowOffsetY = 20;
var _ShadowColor = "#afafaf";
var _ShadowOpacity = 1;

/////////////////// matboard data /////////////////

var _MBOffsetX = 0;
var _MBOffsetY = 0;

var _MBpoint0 = 300;
var _MBpointXA = 0; // top right x
var _MBpointXB = 0; // top right inside X
var _MBpointXC = 0; // top right inside y
var _MBpointXD = 0; // bottom left inside x
var _MBpointXE = 0; // bottom left inside y

var _MBpointYA = 0;
var _MBpointYB = 0;
var _MBpointYC = 0;
var _MBpointYD = 0;
var _MBpointYE = 0;

var _MBscale_factor = 0;
var _MBBottomSideOffsetX = 0;
var _MBBottomSideOffsetY = 0;
var _MBRightSideOffsetX = 0;
var _MBRightSideOffsetY = 0;
var _MBLeftSideOffsetX = 0;
var _MBLeftSideOffsetY = 0;

var _MBLengthX = 504;
var _MBLengthY = 504;

var _MBRectX = 108;
var _MBRectY = 108;

var _MBRightSideX = 0;
var _MBRightSideY = 0;
var _MBLeftSideX = 0;
var _MBLeftSideY = 0;
var _MBBottomSideX = 0;
var _MBBottomSideY = 0;

var _scaled_width_of_mb = 0;

/////////////////// general data //////////////////

var _imageWidth = 0;
var _imageHeight = 0;

var _RectLengthX = 288;
var _RectLengthY = 288;

var _MBTotalWidth = 0;
var _MBTotalHeight = 0;
var _MBScaled_width = 0;

var _fillColor = "#ffffff";

var _innerShadowFilColor = "#555555";
var _strokeColor = "#bfbfbf";
var _strokeWidth = 5;

var _RightSideAdjustment = -1;
var _BottomSideAdjustment = 0;
var _LeftSideAdjustment = 2;
var _pointYA_Adjustment = 3;
var _pointXA_Adjustment = 1;

var _stageJSON = "";

var _imageLink = "";

/**- - - - - - - -  - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - */
////////////////////////////////// Konva Data End ///////////////////////////////
/**- - - - - - - -  - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - */

var _scale_width  = 300;
var _adj_mobile_scale_width = 270;
var _adj_mobile_scale_height = 250;
var _adj_desktop_scale_width = 350;
var _adj_desktop_scale_width = 300;
var _adj_desktop_scale_height = 300;

var _status = 0;
var _item_price = 0.00;
var _item_qty = 0;
var _img_original_filename = "";
var _img_screen_scale_factor = 0;
var _set_inside_frame_width = 0;
var _set_inside_frame_height = 0;
var _frame_height = 0;
var _mb1_color = "#ffffff";
var _matboard_type  = "";

var _matboardSize = [
  { value: 0, text: 'none' },
  { value: 36, text: '1/2 in' },  // 36
  { value: 72, text: '1 in' },    // 72
  { value: 108, text: '1.5 in' }, // 108
  { value: 144, text: '2 in' },   // 144
  { value: 180, text: '2.5 in' }, // 180
  { value: 216, text: '3 in' },   // 216
  { value: 252, text: '3.5 in' }, // 252
  { value: 288, text: '4 in' },   // 288
]

var _view_mode = 'desktop';
var _screenSizeAdj = 0;

var _textMBWidth = '';
var _btnInnerMat = null;
var _innerShadowVisibility = true;

var _innerMBEnabled = false;
var _innerMBWidth = 0;
var _innerMBColor = 0;

var _canvasDivHeight = 0;

_blankImgPlaceholder.src = _blankImageSrc;
_mainImage.src = _blankImageSrc;
_stage_width = 500;
_stage_height = 400;

var _current_submitted_file_guid;
var _current_submitted_file_name;
var _filename_selected_for_upload;
var _user_id = null;
var _order_guid = null;
var _current_cart_item_guid = null;
var _qty = 1;
var _productMode = 'frames';
var _image_width = 0;
var _image_height = 0;
var _orig_image_width = 0;
var _orig_image_height = 0;
var _image_max_screen_size = 300;
var _savedImageObject = 'TEST OBJECT';
var _uploadFile = new Image();

var _current_filename_being_uploaded;

var _productPreviewImage;

var _quick_sizes;

var _selected_upload_image_wrapper_display_width = 0;
var _selected_upload_image_wrapper_display_height = 0;
var _cropped_offest_adjust = 0;
var _selected_image_for_upload_orig_orientation;


var $_product_colors = [
   { color: 'ffffff',color_name: 'White'},
   { color: '000000',color_name: 'Black'},
   { color: '6f92b7',color_name: 'Sea Grey'},
   { color: '8194a3',color_name: 'Grey'},
   { color: 'aa0a3a',color_name: 'Crimson'},
   { color: 'd14a47',color_name: 'Rust'},
   { color: '678b7f',color_name: 'Forest Green'},
   { color: '861931',color_name: 'Coffee'},
   { color: 'e9e8e1',color_name: 'Ivory'},
   { color: 'cfcddb',color_name: 'Lavender'},
   { color: 'efd1d4',color_name: 'Pink'},
   { color: '767777',color_name: 'Dark Grey'},
   { color: '1a1a1a',color_name: 'Midnight'},
   { color: '725044',color_name: 'Expresso'},
   { color: '664a7e',color_name: 'Purple'},
   { color: '9f9f9f',color_name: 'Med Grey'},
   { color: 'f7ebd3',color_name: 'Cream'},
   { color: 'eeefef',color_name: 'Lt Grey'},
   { color: 'dde3e9',color_name: 'Sky Grey'},
   { color: '9d8e63',color_name: 'Olive'},
   { color: '678b7f',color_name: 'Dark Green'},
   { color: 'bcbdbd',color_name: 'Clay Grey'},
   { color: '95957b',color_name: 'Olive Drab'}
]


var _total_uploaded_bytes;
var _uploaded_bytes_sofar;
var _percent_uploaded;

var $ret_string;
var cart_subtotal;
var $display_string;

var $valid_aspect_ratios = [
   .833,
   .8,
   .786,
   .75,
   .667,
   .647,
   .5,
   .429,
   .333,
   1,
   1.2,
   1.25,
   1.273,
   1.333,
   1.5,
   1.545,
   2,
   2.333,
   3
];