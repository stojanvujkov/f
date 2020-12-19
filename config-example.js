// save this in directory: public_html/wp-content/plugins/framedware
// see this video for instructions vimeo.com/475601238

// DPI
var $default_min_print_res = 72; // dpi

// MINIMUM PRINT LENGTH (INCHES)
var $minimum_print_length = 5; // inches (in example: 6" @ 72dpi -> 432px)

// SHOW/HIDE RATIOS FOR PRECROPPING FRAMING OPTIONS IN THE UI,
// (1 = show, 0 = hide)
const ui = {
    custom_custom: 1,
    express_1_1: 1,
    express_3_2: 1,
    express_4_3: 1,
    express_16_9: 0
};

// SET DEFAULT FOR INVISIBLE GLASS ON/OFF (1/0)
var invisible_glass_on = 0;

// ASPECT RATIOS
// makes no changes for GUI. It is used for calculations only.
var $frame_aspect_ratio_settings = {
    'custom_custom': { landscape: 0, portrait: 0 },    // custom
    'express_1_1': { landscape: 1, portrait: 1 },      // express - 1:1
    'express_3_2': { landscape: 1.5, portrait: 0.66 }, // express - 3:2
    'express_4_3': { landscape: 1.33, portrait: 0.75 }, // express - 4:3
    'express_16_9': { landscape: 1.77, portrait: 0.56 }, // express - 16:9
};

// PRICING, PAPER SIZES, INVISIBLE GLASS PRICING
// (invisible_glass_price is added to base price)
var $paper = {
    'custom_custom': {
        39: { long_side: 5, short_side: 5, invisible_glass_price: 20 },
        65: { long_side: 7, short_side: 5, invisible_glass_price: 25 },
        85: { long_side: 12, short_side: 9, invisible_glass_price: 25 },
        99: { long_side: 18, short_side: 12, invisible_glass_price: 50 },
        145: { long_side: 24, short_side: 18, invisible_glass_price: 50 },
        179: { long_side: 34, short_side: 24, invisible_glass_price: 100 },
//      209: { long_side: 40, short_side: 32, invisible_glass_price: 250 }
    },
    'express_1_1': {
//      39: { long_side: 5, short_side: 5, invisible_glass_price: 20 },
        85: { long_side: 7, short_side: 7, invisible_glass_price: 25 },
        99: { long_side: 10, short_side: 10, invisible_glass_price: 25 },
        99: { long_side: 12, short_side: 12, invisible_glass_price: 25 },
        145: { long_side: 16, short_side: 16, invisible_glass_price: 50 }
    },
    'express_3_2': {
        85: { long_side: 9, short_side: 6, invisible_glass_price: 25 },
        99: { long_side: 15, short_side: 10, invisible_glass_price: 25 },
        145: { long_side: 21, short_side: 14, invisible_glass_price: 50 },
        179: { long_side: 30, short_side: 20, invisible_glass_price: 70 },
        209: { long_side: 36, short_side: 24, invisible_glass_price: 250 }
    },
    'express_4_3': {
//      85: { long_side: 8, short_side: 6, invisible_glass_price: 25 },
        85: { long_side: 12, short_side: 9, invisible_glass_price: 25 },
        99: { long_side: 16, short_side: 12, invisible_glass_price: 25 },
        145: { long_side: 20, short_side: 15, invisible_glass_price: 50 },
        179: { long_side: 30, short_side: 24, invisible_glass_price: 100 }
    },
    'express_16_9': {
        59: { long_side: 8, short_side: 6, invisible_glass_price: 25 },
        99: { long_side: 12, short_side: 9, invisible_glass_price: 25 },
        129: { long_side: 16, short_side: 12, invisible_glass_price: 25 },
        159: { long_side: 20, short_side: 15, invisible_glass_price: 50 }
    }
};

// GALLERY WALL PRICING
var wall_pricing = {
    '1x3': 500,
    '2x4': 600,
    '3x3': 700,
    '4x3': 800,
    'stairway': 700,
};

// FILE PICKER
/**
 * Choose filepicker tool, can be one of: [local, filestack]
 */
const file_picker = 'filestack';

// FILESTACK
// API KEY
const filestack_api_key = 'AImUxQVTwT7CLBaHwFe4Oz';
// FILEPICKER SOURCES as documented here https://www.filestack.com/docs/uploads/pickers/web/#sources-list
const filestack_sources_regular = ['local_file_system', 'instagram'];
const filestack_sources_gallery_wall = ['local_file_system', 'instagram'];


// FRAMES
// Images are located in this directory on server: /public_html/uploadhandler/uploads/image_assets/
const frame_list = {
    1: {
        "id": 1,
        "frame_guid": "d1dc170d-4eb0-4718-91e8-0600d1651201",
        "frame_name": "Ramino Brushed Gold",
        "frame_border_img": "10771303-splice.jpg",
        "frame_width": 54,
        "frame_border_slice": "bw220048-1.5in-splice.png", // <--- change frame pattern
        "frame_sku": "10771303",
        "frame_description": "Gold",
        "frame_size_inches": 0.75,
        "frame_weight": 1,
        "frame_preview_img_one": "10771303-0-thumb.jpg",
        "frame_preview_img_two": "10771303-1-thumb.jpg",
        "frame_preview_img_three": "10771303-2-thumb.jpg",
        "frame_preview_img_one_std": "10771303-0-std.jpg",
        "frame_preview_img_two_std": "10771303-1-std.jpg",
        "frame_preview_img_three_std": "10771303-2-std.jpg",
        "frame_select_img": "bw220048-preview.png",
        "frame_status": 1,
        "frame_cost": 1,
        "frame_price": 0.50,
        "frame_supplier": "NULL",
        "frame_stock_qty": 1,
// frame number --> is the frame number that prints out in invicces
        "frame_number": "LJ80902"
    },
    3: {
        "id": 3,
        "frame_guid": "dfc97a0d-fb23-4b8d-badd-252d9ed89057",
        "frame_name": "bw26042",
        "frame_border_img": "10771054-splice.jpg",
        "frame_preview_img_one": "10771054-0-thumb.jpg",
        "frame_preview_img_two": "10771054-1-thumb.jpg",
        "frame_preview_img_three": "10771054-2-thumb.jpg",
        "frame_preview_img_one_std": "10771054-0-std.jpg",
        "frame_preview_img_two_std": "10771054-1-std.jpg",
        "frame_preview_img_three_std": "10771054-2-std.jpg",
        "frame_width": 54,
        "frame_border_slice": "bw26042-1.25in-splice.png",
        "frame_sku": "bw26042",
        "frame_description": "Silver",
        "frame_size_inches": 0.75,
        "frame_weight": 1,
        "frame_preview_img": "bw26042-FrontStraight.png",
        "frame_select_img": "bw26042-preview.png",
        "frame_status": 1,
        "frame_cost": 1.25,
        "frame_price": 0.50,
        "frame_supplier": "NULL",
        "frame_stock_qty": 1,
        "frame_number": "LINA"
    },
    4: {
        "id": 4,
        "frame_guid": "89300a2a-852e-44ad-bf43-903d11b27c5b",
        "frame_name": "bw26056",
// frane border image is what the app uses to draw frame picture
// saved here
// frameshops.com/uploadhandler/uploads/image_assets/10771302-splice.jpg
        "frame_border_img": "10771302-splice.jpg",
        "frame_preview_img_one": "10771302-0-thumb.jpg",
        "frame_preview_img_two": "10771302-1-thumb.jpg",
        "frame_preview_img_three": "10771302-2-thumb.jpg",
        "frame_preview_img_one_std": "10771302-0-std.jpg",
        "frame_preview_img_two_std": "10771302-1-std.jpg",
        "frame_preview_img_three_std": "10771302-2-std.jpg",
        "frame_width": 54,
        "frame_border_slice": "bw26056-1.25in-splice.png",
        "frame_sku": "bw26056",
        "frame_description": "Nordic Oak",
        "frame_size_inches": 0.75,
        "frame_weight": 1,
        "frame_preview_img": "bw26056-FrontStraight.png",
        "frame_select_img": "bw26056-preview.png",
        "frame_status": 1,
        "frame_cost": 1.25,
        "frame_price": 0.50,
        "frame_supplier": "NULL",
        "frame_stock_qty": 1,
        "frame_number": "Nordic Oak"
    },
    5: {
        "id": 5,
        "frame_guid": "98d2c543-8f8d-452b-bd5d-21de52695e1e",
        "frame_name": "bw26025",
        "frame_border_img": "10771009-splice.jpg",
        "frame_preview_img_one": "10771009-0-thumb.jpg",
        "frame_preview_img_two": "10771009-1-thumb.jpg",
        "frame_preview_img_three": "10771009-2-thumb.jpg",
        "frame_preview_img_one_std": "10771009-0-std.jpg",
        "frame_preview_img_two_std": "10771009-1-std.jpg",
        "frame_preview_img_three_std": "10771009-2-std.jpg",
        "frame_width": 54,
        "frame_border_slice": "bw26025-1.25in-splice.png",
        "frame_sku": "bw26025",
        "frame_description": "White",
        "frame_size_inches": 0.75,
        "frame_weight": 1,
        "frame_preview_img": "bw26025-FrontStraight.png",
        "frame_select_img": "bw26025-preview.png",
        "frame_status": 1,
        "frame_cost": 1.5,
        "frame_price": 0.50,
        "frame_supplier": "NULL",
        "frame_stock_qty": 1,
        "frame_number": "TAU"
    },
    7: {
        "id": 7,
        "frame_guid": "bdeede50-c86a-4fe2-9531-a3586ec6626f",
        "frame_name": "bw59966",
        "frame_border_img": "10771000-splice.jpg",
        "frame_preview_img_one": "10771000-0-thumb.jpg",
        "frame_preview_img_two": "10771000-1-thumb.jpg",
        "frame_preview_img_three": "10771000-2-thumb.jpg",
        "frame_preview_img_one_std": "10771000-0-std.jpg",
        "frame_preview_img_two_std": "10771000-1-std.jpg",
        "frame_preview_img_three_std": "10771000-2-std.jpg",
        "frame_width": 54,
        "frame_border_slice": "bw59966-1.5in-splice.png",
        "frame_sku": "bw59966",
        "frame_description": "Matt Black",
        "frame_size_inches": 0.75,
        "frame_weight": 1,
        "frame_preview_img": "bw59966-FrontStraight.png",
        "frame_select_img": "bw59966-preview.png",
        "frame_status": 1,
        "frame_cost": 2,
        "frame_price": 0.50,
        "frame_supplier": "NULL",
        "frame_stock_qty": 1,
        "frame_number": "MIA"
    },
    8: {
        "id": 8,
        "frame_guid": "8335ed5b-bd33-46fe-be54-1ce9b56f747c",
        "frame_name": "bw26021",
        "frame_border_img": "10761086-splice.jpg",
        "frame_preview_img_one": "10761086-0-thumb.jpg",
        "frame_preview_img_two": "10761086-1-thumb.jpg",
        "frame_preview_img_three": "10761086-2-thumb.jpg",
        "frame_preview_img_one_std": "10761086-0-std.jpg",
        "frame_preview_img_two_std": "10761086-1-std.jpg",
        "frame_preview_img_three_std": "10761086-2-std.jpg",
        "frame_width": 54,
        "frame_border_slice": "bw26021-1.25in-splice.png",
        "frame_sku": "bw26021",
        "frame_description": "Light Walnut",
        "frame_size_inches": 0.75,
        "frame_weight": 1,
        "frame_preview_img": "bw26021-FrontStraight.png",
        "frame_select_img": "bw26021-preview.png",
        "frame_status": 1,
        "frame_cost": 1.5,
        "frame_price": 0.50,
        "frame_supplier": "NULL",
        "frame_stock_qty": 1,
        "frame_number": "EMMA"
    },
};