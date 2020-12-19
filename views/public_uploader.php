<div id="public_uploader">

    <div id="filestack_picker"></div>

    <div class="bootstrapiso cls-upload-main-dom" id="uploader-dom">
        <div id="upload-container" class="text-center uploader-dom-container">

            <div id="local_picker">
                <div class="pb-3">
                    <div class="text-uppercase">From your phone to your home</div>
                    <div>
                        <h1>Upload a Digital Photo</h1>
                    </div>
                </div>
                <div id="upload-img-placeholder-wrapper" class="col-12 d-flex justify-content-center">
                    <div id="upload-img-placeholder">
                        <input class="fileInput cls-uploader-btn-width" type="file" name="file1" onchange="select_file_to_upload_framed( this, 'custom' )">
                        <img class="d-inline-block" src="../../uploadhandler/uploads/image_assets/upload-img.png" alt="upload frame placeholder">
                    </div>
                </div>
                <div id="inputWrapper" class="inputWrapper mx-auto">
                    <input class="fileInput cls-uploader-btn-width" type="file" name="file1" onchange="select_file_to_upload_framed( this, 'custom' )"/><span class="inputWrapper-span">Select Image</span>
                </div>
            </div>

            <div id="upload-crop-image-container" class="row hide mx-auto">
                <div id="image-upload-container" class="justify-content-center mx-auto col-sm-12 col-lg-6">
                    <div class="card w-100" style="border: none !important;">
                        <div id="selected-image-upload-card-body" class="card-body mx-auto pl-0 pr-0">
                            <div id="selected-upload-image-wrapper" class="d--inline-block">
                                <img class="" id="selected-upload-image" alt="">
                            </div>
                            <div id="cropper-image-wrapper" class="hide">
                                <div id="btn-rotate-image-wrapper" class="mt-2 mb-2">
                                    <button id="btn-save-crop" class="fware-button cls-uploader-btn-width next_button" onclick="saveCroppedCanvas()">Next</button>
                                </div>
                                <img class="" id="selected-upload-image-to-crop" alt="">
                            </div>
                            <div id="cropper-result-wrapper">
                                <img id="cropper-result" src="" alt="">
                            </div>
                        </div>
                        <div class="pb-3" style="border-top: none !important; background-color: #ffffff;">
                            <div id="selected-file-to-upload-filename" style="font-size: 11pt !important;"></div>
                        </div>

                        <div id="btn-rotate-image-wrapper" class="mt-2 mb-2">
                            <button id="btn-save-crop" class="fware-button cls-uploader-btn-width next_button" onclick="saveCroppedCanvas()">Next</button>
                        </div>

                        <!-- Aspect Ratio V2 -->
                        <div class="aspect_ratio_container">
                            <ul class="aspect_ratio">
                                <div class="aspect_ratio_col">
                                    <li class="aspect_ratio_custom" id="framing_option_custom_custom"><a href="javascript:;" onclick="get_printSizes('custom', 'custom_custom');">Custom</a></li>
                                    <li class="aspect_ratio_1_1" id="framing_option_express_1_1"><a href="javascript:;" onclick="get_printSizes('express', 'express_1_1', 'portrait');">1:1</a></li>
                                </div>
                                <div class="aspect_ratio_col" id="framing_option_express_3_2">
                                    <li class="aspect_ratio_2_3" ><a href="javascript:;" onclick="get_printSizes('express', 'express_3_2', 'portrait');">2:3</a></li>
                                    <li class="aspect_ratio_3_2" ><a href="javascript:;" onclick="get_printSizes('express', 'express_3_2', 'landscape');">3:2</a></li>
                                </div>
                                <div class="aspect_ratio_col" id="framing_option_express_4_3">
                                    <li class="aspect_ratio_3_4" ><a href="javascript:;" onclick="get_printSizes('express', 'express_4_3', 'portrait');">3:4</a></li>
                                    <li class="aspect_ratio_4_3" ><a href="javascript:;" onclick="get_printSizes('express', 'express_4_3', 'landscape');">4:3</a></li>
                                </div>
                                <div class="aspect_ratio_col" id="framing_option_express_16_9">
                                    <li class="aspect_ratio_9_16" ><a href="javascript:;" onclick="get_printSizes('express', 'express_16_9', 'portrait');">9:16</a></li>
                                    <li class="aspect_ratio_16_9" ><a href="javascript:;" onclick="get_printSizes('express', 'express_16_9', 'landscape');">16:9</a></li>
                                </div>
                            </ul>
                        </div>

                    </div>
                </div>
            </div>
            <div id="crop-action-buttons" class="d-fex hide flex-column mt-3">
                <div id="btn-rotate-image-wrapper" class="mt-2 mb-2">
                    <button id="btn-rotate-image" class="crop-functions fware-button cls-uploader-btn-width uploader-button" onclick="rotateCrop()">Rotate Image <span class="cg_rotate_icon"></span></button>
                </div>
                <!-- <div class="mx-auto" style="max-width: 400px;">
                    <div id="btn-crop-reset-wrapper">
                        <button id="btn-crop-reset" class="crop-functions fware-button cls-uploader-btn-width uploader-button cg_overlay_hook" onclick="location.reload()">Back</button>
                    </div>
                </div> -->
            </div>
            <div id="upload-buttons-wrapper" class="d-flex flex-column d-block justify-content-center">

                <div id="btn-reset-upload-wrapper" class="mt-2 hide">
                    <button  style="font-size: 10pt !important;" id="btn-reset-upload" class="crop-functions fware-button cls-uploader-btn-width mx-auto" onclick="reset_upload()">Clear Selected File</button>
                </div>
                <div id="btn-begin-crop-wrapper" class="mt-2 hide">
                    <button  style="font-size: 10pt !important;" id="btn-begin-crop" class="crop-functions fware-button mx-auto cls-uploader-btn-width" onclick="init_cropper()">Crop or Rotate Image</button>
                </div>
                <div id="btn-begin-upload-wrapper" class="mt-2 hide">
                    <button  style="font-size: 10pt !important;" id="btn-begin-upload" class="fware-button mx-auto cls-uploader-btn-width" onclick="beginUpload()">Begin Upload</button>
                </div>
            </div>
            <div class="hide row" id="fineuploader-div-wrapper">
                <div class="d-inline-block mx-auto col-12" style="max-width: 350px;">
                    <div>
                        <div id="fine-uploader-element"></div>
                    </div>
                    <div>
                        <div id="fine-uploader-manual-trigger"></div>
                    </div>
                </div>
            </div>
            <div class="modal fade in cg_progress" id="upload-in-process-modal" tabindex="-1" data-keyboard="false" data-backdrop="static" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true" style="background-color: rgba(0,0,0,0.45)">
                <div class="modal-dialog modal-dialog-centered" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="exampleModalCenterTitle">Please wait while the file uploads</h5>
                        </div>
                        <div class="modal-body">
                            <div class="progress progress-striped active">
                                <div class="progress-bar"></div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button style="font-size: 10pt !important;" type="button" class="btn btn-secondary" onclick="window.location.href = window.location.href;">Cancel Upload</button>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal fade in" id="aspect-ratio-info-modal" tabindex="-1" data-keyboard="false" data-backdrop="static" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true" style="background-color: rgba(0,0,0,0.45)">
                <div class="modal-dialog modal-dialog-centered" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="">Aspect Ratio Information</h5>
                        </div>
                        <div class="modal-body">
                            Aspect Ratio Info.....
                        </div>
                        <div class="modal-footer">
                            <button style="font-size: 10pt !important;" type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal fade in" id="not-supported-file-modal" tabindex="-1" data-keyboard="false" data-backdrop="static" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true" style="background-color: rgba(0,0,0,0.45)">
                <div class="modal-dialog modal-dialog-centered" role="document">
                    <div class="modal-content">
                        <div class="modal-body">
                            <h5 class="modal-title" id="exampleModalCenterTitle">The selected file type is not supported, please try another file.</h5>
                        </div>
                        <div class="modal-footer">
                            <button style="font-size: 10pt !important;" type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal fade in" id="lowres-file-modal" tabindex="-1" data-keyboard="false" data-backdrop="static" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true" style="background-color: rgba(0,0,0,0.45)">
                <div class="modal-dialog modal-dialog-centered" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <div class="mx-auto">
                                <i style="font-size: 36pt !important;" class="fa fa-exclamation-circle" aria-hidden="true"></i>
                            </div>
                        </div>
                        <div class="modal-body" style="font-size: 11pt;">
                            <div style="font-size: 12pt; font-weight: bold;">Uh Oh!</div>
                            <div>The selected file is a bit too small to print</div>
                            <div>Please select a higher resolution file</div>
                            <div class="mt-2">For help, call us at: <b><?php echo $support_phone_number ?></b></div>
                            <div>
                                Or email us at: <a href="mailto:<?php echo $support_email ?>"><?php echo $support_email ?></a>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button style="font-size: 10pt !important;" type="button" class="btn btn-secondary" onclick="location.reload()">Close</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>