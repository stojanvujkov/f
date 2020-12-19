<?php
/**
 * Created by PhpStorm.
 * User: ferdware
 * Date: 11/24/18
 * Time: 8:23 AM
 */

function fineUploaderTemplate() { ?>

	<script type="text/template" id="qq-template-manual-trigger">
		<div class="qq-uploader-selector qq-uploader" qq-drop-area-text="Drop files here">
			<div class="qq-total-progress-bar-container-selector qq-total-progress-bar-container">
				<div role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"
				     class="qq-total-progress-bar-selector qq-progress-bar qq-total-progress-bar"></div>
			</div>
			<div class="qq-upload-drop-area-selector qq-upload-drop-area" qq-hide-dropzone>
				<span class="qq-upload-drop-area-text-selector"></span>
			</div>
			<div class="buttons">
				<div class="qq-hide qq-upload-button-selector qq-upload-button">
					<div>Select files</div>
				</div>
				<button type="button" id="trigger-upload" class="qq-hide btn btn-primary" style="display: none;">
					<i class="icon-upload icon-white"></i> Upload
				</button>
			</div>
			<span class="qq-drop-processing-selector qq-drop-processing">
                <span>Processing dropped files...</span>
                <span class="qq-drop-processing-spinner-selector qq-drop-processing-spinner"></span>
            </span>
			<ul class="qq-upload-list-selector qq-upload-list" aria-live="polite" aria-relevant="additions removals">
				<li>
					<div class="d-flex col-12">
						<div class="qq-progress-bar-container-selector">
							<div role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"
							     class="qq-progress-bar-selector qq-progress-bar"></div>
						</div>
						<div class="d-flex col-12" style="max-width: 350px;">
							<div style="display:inline; width: 100%;">
								<img style="float: left;" class="qq-thumbnail-selector" qq-max-size="100" qq-server-scale>
							</div>
							<div style="display:inline; width: 100%;">
								<button type="button" class="qq-btn qq-upload-cancel-selector qq-upload-cancel">Cancel</button>
							</div>
						</div>
					</div>
					<div>
						<span style="display: none;" class="qq-upload-spinner-selector qq-upload-spinner"></span>

						<span style="display: none;" class="qq-upload-file-selector qq-upload-file"></span>
						<span style="display: none;" class="qq-edit-filename-icon-selector qq-edit-filename-icon"
						      aria-label="Edit filename"></span>
						<input style="display: none;" class="qq-edit-filename-selector qq-edit-filename" tabindex="0"
						       type="text">
						<span style="display: none;" class="qq-upload-size-selector qq-upload-size"></span>

						<button style="display: none;" type="button" class="qq-btn qq-upload-retry-selector qq-upload-retry">
							Retry
						</button>
						<button style="display: none;" type="button"
						        class="qq-btn qq-upload-delete-selector qq-upload-delete">Delete
						</button>
						<span role="status" class="qq-upload-status-text-selector qq-upload-status-text"></span>
					</div>
				</li>
			</ul>

			<dialog class="qq-alert-dialog-selector">
				<div class="qq-dialog-message-selector"></div>
				<div class="qq-dialog-buttons">
					<button type="button" class="qq-cancel-button-selector">Close</button>
				</div>
			</dialog>

			<dialog class="qq-confirm-dialog-selector">
				<div class="qq-dialog-message-selector"></div>
				<div class="qq-dialog-buttons">
					<button type="button" class="qq-cancel-button-selector">No</button>
					<button type="button" class="qq-ok-button-selector">Yes</button>
				</div>
			</dialog>

			<dialog class="qq-prompt-dialog-selector">
				<div class="qq-dialog-message-selector"></div>
				<input type="text">
				<div class="qq-dialog-buttons">
					<button type="button" class="qq-cancel-button-selector">Cancel</button>
					<button type="button" class="qq-ok-button-selector">Ok</button>
				</div>
			</dialog>
		</div>
	</script>

<?php };


function shoppingCartTemplate(){?>

	<script id="shopping-cart-template" type="text/template">
		<div class="card w-100" style="margin-bottom: 5px;">
			<img class="card-img-top mx-auto" src="../../../uploadhandler/uploads/{{cart_image}}" alt="Card image cap" style="border: 1px solid #efefef; margin-top: 10px; width: {{width}}px; height: {{height}}px">
			<div class="card-title">
			</div>
			<div class="card-body p-1">
				<button type="button" id="shopping-cart-edit" class="fware-button shopping-cart-edit" style="width: 120px !important; font-size: 10pt !important; letter-spacing: 0px !important; background-color: #79b6c0" onclick="edit_cart_item('{{cart_item_guid}}')">Edit</button>
				<button type="button" id="shopping-cart-delete" class="fware-button shopping-cart-delete" style="width: 120px !important; font-size: 10pt !important; letter-spacing: 0px !important; background-color: #79b6c0" onclick="delete_cart_item('{{cart_item_guid}}')">Delete</button>
			</div>
			<div class="list-group mx-auto text-left mb-3" style="width: 290px !important;">
				<div class="list-group-item p-1 pl-3">
					<span>Qty: {{qty}}</span>
               <span style="margin-left: 20px;">Price: </span>
               <span style="font-weight: bold;">${{price}}</span> 
				</div>
				<div class="list-group-item p-1 pl-3">Outside Dimensions: {{outside_dim}}</div>
            <div class="list-group-item p-1 pl-3">Printed Image Size: {{printed_img_size}}</div>
				<div class="list-group-item p-1 pl-3">Frame Style: {{frame_style}}</div>
				<div class="list-group-item p-1 pl-3">Matting: {{top_mat}}</div>
			</div>
		</div>
	</script>

	<?php
};
?>