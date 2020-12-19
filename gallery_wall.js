function wall_init(id, description, item_count) {
    wall_id = id; // <--
    wall = localStorage.getItem('wall__' + id);
    if (wall === null) {
        wall = {
            'id': id,
            'sku': generateRandomString(30),
            'description': description,
            'item_selected': null,
            'frame_selected': null,
            'list': {},
            'item_count': item_count,
            'price': null
        };
        localStorage.setItem('wall__' + id, JSON.stringify(wall));
    } else {
        wall = JSON.parse(wall);
    }
    wall.price = wall_pricing[id]; // set price from config.js
    localStorage.setItem('wall__' + id, JSON.stringify(wall));
}

function wall_item_select(selector)
{
    if (selector === null) {
        return;
    }

    let wall_id = $(selector).data('wall-id');
    //console.log(wall_id);

    let wall_item = $(selector).data('wall-item');
    //console.log(wall_item);

    let wall = localStorage.getItem('wall__' + wall_id);
    wall = JSON.parse(wall);
    //console.log(wall);

    wall.item_selected = wall_item; // <--
    localStorage.setItem('wall__' + wall_id, JSON.stringify(wall));
    //console.log(wall);

    $('.gwi_frame').removeClass('active');
    $(selector).addClass('active');

    let f = wall.item_selected;
    if (wall.list[f] !== undefined) {
        let ff = wall.list[f];
        // DISPLAY THUMBNAIL IN THE UI
        $('.gwi_frame[data-wall-item="' + wall_item +  '"] .inside_photo').css('background-image', 'url(' + ff.thumb + ')');
    } else {
        $('.gwi_frame[data-wall-item="' + wall_item +  '"] .inside_photo').css('background-image', 'none');
    }
}

function wall_item_select_next() {
    let wall = localStorage.getItem('wall__' + wall_id);
    wall = JSON.parse(wall);
    let item = wall.item_selected;
    if (item === null || item >= wall.item_count) {
        item = 1;
    } else {
        ++item;
    }
    //console.log(item);
    wall_item_select('.gwi_frame[data-wall-item="' + item +  '"]');
}

function wall_item_select_prev() {
    let wall = localStorage.getItem('wall__' + wall_id);
    wall = JSON.parse(wall);
    let item = wall.item_selected;
    if (item === null || item <= 1) {
        item = wall.item_count;
    } else {
        --item;
    }
    //console.log(item);
    wall_item_select('.gwi_frame[data-wall-item="' + item +  '"]');
}

function wall_frame_select(selector, id, options, description, image) // #frame-color-01 | black | 'Gallery Wall 3 / Black frames' | gallery_wall_1x3_black.jpg
{
    let product_wall = {
        'id': id,
        'options': options,
        'description' : description
    };
    $('#wall_image').attr('src', myAjax.plugin_url + 'assets/img/' + image);

    $('.frame-option').removeClass('active');
    $('.frame-option[data-options="' + options + '"]').addClass('active');

    // SAVE WALL DATA
    let wall = localStorage.getItem('wall__' + id);
    wall = JSON.parse(wall);
    //console.log(wall);
    wall.description = description;
    wall.frame_selected = options;
    localStorage.setItem('wall__' + id, JSON.stringify(wall));

    // SAVE PRODUCT DATA
    let $tmp_productJSON = getLsProductJSON();
    $tmp_productJSON.wall = product_wall;
    saveProductJsonToLS($tmp_productJSON);
}

function filestack_run(selector)
{
    if (selector === null) {
        return;
    }

    let image_aspect = $(selector).data('image-aspect');
    //console.log(image_aspect);
    let item = $(selector).data('wall-item');
    //console.log(item);

    if ($('#filestack_picker_gallery_wall').length) {
        // FILE PICKER
        let client = filestack.init(filestack_api_key);
        let options = {
            fromSources: filestack_sources_gallery_wall,
            displayMode: 'inline',
            container: '#filestack_picker_gallery_wall',
            minFiles: 1,
            maxFiles: 1,
            uploadInBackground: false,
            transformations: {
                crop: {
                    aspectRatio: eval(image_aspect),
                    force: true
                },
                circle: false,
                rotate: true
            },
            //onUploadDone: (res) => console.log(res),
            onUploadDone: (res) => ula_x(res),
        };
        let picker = client.picker(options).open();
    }
    $('#modal_filestack').modal('show');
}

/**
    Gallery Wall
    Save uploaded files and data
 */
function ula_x(filestack)
{
    let wall = localStorage.getItem('wall__' + wall_id);
    wall = JSON.parse(wall);

    $('#modal_filestack').modal('hide');

    let url = filestack.filesUploaded[0].url;
    url = url.replace('https://cdn.filestackcontent.com/', 'https://cdn.filestackcontent.com/rotate=deg:exif/'); // <-- rotate image per EXIF
    //console.log(url);
    let thumb = url.replace('https://cdn.filestackcontent.com/', 'https://cdn.filestackcontent.com/resize=width:300/');
    filestack.filesUploaded[0].thumb = thumb;
    //console.log(thumb);

    let thumb_filename = filestack.filesUploaded[0].filename.replace(/\.[^/.]+$/, "");
    thumb_filename = thumb_filename + '_thumb.jpg';
    filestack.filesUploaded[0].thumb_filename = thumb_filename;

    let filename = filestack.filesUploaded[0].filename;

    // DISPLAY THUMBNAIL IN THE UI
    $('.gwi_frame[data-wall-item="' + wall.item_selected +  '"] .inside_photo').css('background-image', 'url(' + thumb + ')');

    // STORE
    let f = wall.item_selected;

    // to remove existing image from local filesystem, if any
    //console.log(wall.list[f]);
    let remove = null;
    if (wall.list[f] !== undefined) {
        remove = wall.list[f];
    }

    wall.list[f] = filestack.filesUploaded[0];
    localStorage.setItem('wall__' + wall.id, JSON.stringify(wall));

    jQuery.ajax({
        url: myAjax.ajaxurl,
        type: 'POST',
        data: {
            'wall': wall,
            'filestack': filestack,
            'remove': remove,
            'action': 'wall__store_x'  // <-- WP action
        },
        dataType: 'json',
        success: function (response) {
            $('#__filestack-picker').remove();
            $('#filestack_picker_gallery_wall').html('');
            console.log(response);
            wall_item_progress(wall);
        },
        error: function(xhr, status, error) {

        }
    });
}

function wall_item_progress(wall) {
    let list_count = Object.keys(wall.list).length;
    if (list_count >= wall.item_count) {
        $('.gw_addtocart_wrapper .wall_add_to_cart').css('display', 'block');
    }
    console.log(wall.id + ' progress -> ' + list_count + ' out of ' + wall.item_count);
}

/**
    Gallery Wall
    Add to Cart
 */
function wall__add_to_cart()
{
    let wall = localStorage.getItem('wall__' + wall_id);
    wall = JSON.parse(wall);

    jQuery.ajax({
        url: myAjax.ajaxurl,
        type: 'POST',
        data: {
            'wall': wall,
            'action': 'wall__add_to_cart'  // <-- WP action
        },
        dataType: 'json',
        success: function (response) {
            localStorage.removeItem('wall__' + wall_id);
            location.href = myAjax.woocommerce_cart_url;
            /*
            if (myAjax.woocommerce_cart_redirect_after_add == 'yes') {
                //console.log('Redirect to Cart ...');
                location.href = myAjax.woocommerce_cart_url;
            } else {
                //console.log('Reload upload page ...');
                window.location.reload(true);
            }
            */
        },
        error: function(xhr, status, error) {

        }
    });
}

jQuery(document).ready( function()
{
    // GALLERY WALL UI
    if (wall !== null && typeof (wall) !== 'undefined') {
        // GALLERY WALL, DISPLAY THUMBNAILS IN THE UI
        if (typeof (wall.list) !== 'undefined') {
            for (const [key, value] of Object.entries(wall.list)) {
                $('.gwi_frame[data-wall-item="' + key +  '"] .inside_photo').css('background-image', 'url(' + value.thumb + ')');
                //console.log(key + ' -> ' + value.thumb);
            }
        }

        // GALLERY WALL, SELECT ITEM
        $('.gwi_frame').on('click', function(e){
            wall_item_select($(this));
            filestack_run($(this));
        });

        // GALLERY WALL, SELECT FRAME
        $('.frame-option').on('click', function(e){
            wall_frame_select($(this), $(this).data('id'), $(this).data('options'), $(this).data('description'), $(this).data('image'));
        });

        // GALLERY WALL, REFLECT STORED SELECTION IN UI
        // 1) WALL ITEM
        if (wall.item_selected === null) {
            //
        } else {
            wall_item_select('.gwi_frame[data-wall-item="' + wall.item_selected + '"]');
        }
        // 2) FRAME
        if (wall.frame_selected === null) {
            $('.frame-option:first').trigger('click'); // set default
        } else {
            $('.frame-option[data-options="' + wall.frame_selected + '"]').trigger('click'); // set selected
        }

        // GALLERY WALL, ADD TO CART BUTTON
        wall_item_progress(wall);
    }
});
