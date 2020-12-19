

    document.getElementById('selected-upload-image').addEventListener('ready', (event) => {
        crop_start();
    });

    $('#add-file-to-uploader-btn').css('display', 'none');
    $('#begin-upload').css('display', 'none');

    /**-------------------------------------------------------------------------------
     |   Document Ready
     *-------------------------------------------------------------------------------*/
    $(document).ready(function()
    {
        $('#btn-begin-upload').text('Upload Selected File');

        init_uploader('custom_custom');
    });

    $('#top_mat_width').on('blur', function(){
        if(detector.mobile())
        {
            var scrollTop    = $(window).scrollTop();
            var elementOffset = $('#upload-crop-image-container').offset().top;
            var distance      = (elementOffset - scrollTop);

            if((distance > 5) || (distance < 0)){
                $('html').animate({scrollTop: 280}, 'slow');//IE, FF
                $('body').animate({scrollTop: 280}, 'slow');//chrome, don't know if Safari works
            }
        }
    });
