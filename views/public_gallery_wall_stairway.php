
<div class="gallery_wall_1x3">
    <div class="modal" tabindex="-1" role="dialog" id="modal_filestack">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Upload file</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <p>
                        <div id="filestack_picker_gallery_wall"></div>
                    </p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
    <div class="gw13_main_wrapper">
        <div class="gw13_wall_container">
            <figure class="gwsth">
                <img id="wall_image" src="<?php echo PLUGIN_URL; ?>assets/img/gallery_wall_stairway_black.jpg">
                <span class="gwi_frame gwsth_frame_1" tabindex="0" data-wall-id="stairway" data-wall-item="1" data-image-aspect="1/1">
					<span class="inside_photo gwsth_inside_photo_1"></span>
				</span>
                <span class="gwi_frame gwsth_frame_2" tabindex="0" data-wall-id="stairway" data-wall-item="2" data-image-aspect="1/1">
					<span class="inside_photo gwsth_inside_photo_2"></span>
				</span>
                <span class="gwi_frame gwsth_frame_3" tabindex="0" data-wall-id="stairway" data-wall-item="3" data-image-aspect="26/20">
					<span class="inside_photo gwsth_inside_photo_3"></span>
				</span>
                <span class="gwi_frame gwsth_frame_4" tabindex="0" data-wall-id="stairway" data-wall-item="4" data-image-aspect="21/17">
					<span class="inside_photo gwsth_inside_photo_4"></span>
				</span>
                <span class="gwi_frame gwsth_frame_5" tabindex="0" data-wall-id="stairway" data-wall-item="5" data-image-aspect="21/32">
					<span class="inside_photo gwsth_inside_photo_5"></span>
				</span>
                <span class="gwi_frame gwsth_frame_6" tabindex="0" data-wall-id="stairway" data-wall-item="6" data-image-aspect="1/1">
					<span class="inside_photo gwsth_inside_photo_6"></span>
				</span>
                <span class="gwi_frame gwsth_frame_7" tabindex="0" data-wall-id="stairway" data-wall-item="7" data-image-aspect="1/1">
					<span class="inside_photo gwsth_inside_photo_7"></span>
				</span>
                <span class="gwi_frame gwsth_frame_8" tabindex="0" data-wall-id="stairway" data-wall-item="8" data-image-aspect="1/1">
					<span class="inside_photo gwsth_inside_photo_8"></span>
				</span>
                <div class="gw13_arrow">
					<span class="gw13_arrow_left" onclick="wall_item_select_prev();">
                        <svg version="1.1" id="Layer_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" viewBox="0 0 492 492" style="enable-background:new 0 0 492 492;" xml:space="preserve"><g><g><path d="M198.608,246.104L382.664,62.04c5.068-5.056,7.856-11.816,7.856-19.024c0-7.212-2.788-13.968-7.856-19.032l-16.128-16.12 C361.476,2.792,354.712,0,347.504,0s-13.964,2.792-19.028,7.864L109.328,227.008c-5.084,5.08-7.868,11.868-7.848,19.084 c-0.02,7.248,2.76,14.028,7.848,19.112l218.944,218.932c5.064,5.072,11.82,7.864,19.032,7.864c7.208,0,13.964-2.792,19.032-7.864 l16.124-16.12c10.492-10.492,10.492-27.572,0-38.06L198.608,246.104z"/></g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g></svg>
					</span>
                    <span class="gw13_arrow_right" onclick="wall_item_select_next();">
						<svg version="1.1" id="Layer_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" viewBox="0 0 492.004 492.004" style="enable-background:new 0 0 492.004 492.004;" xml:space="preserve"><g><g><path d="M382.678,226.804L163.73,7.86C158.666,2.792,151.906,0,144.698,0s-13.968,2.792-19.032,7.86l-16.124,16.12c-10.492,10.504-10.492,27.576,0,38.064L293.398,245.9l-184.06,184.06c-5.064,5.068-7.86,11.824-7.86,19.028 c0,7.212,2.796,13.968,7.86,19.04l16.124,16.116c5.068,5.068,11.824,7.86,19.032,7.86s13.968-2.792,19.032-7.86L382.678,265 c5.076-5.084,7.864-11.872,7.848-19.088C390.542,238.668,387.754,231.884,382.678,226.804z"/></g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g></svg>
					</span>
                </div>
            </figure>
            <div class="gw13_controller">
                <span class="gw13_inst">Click a frame above to start</span>
                <div class="gw_materials">
                    <span class="frame-option gw_material_black" tabindex="0" data-id="stairway" data-options="black" data-description="Gallery Wall Stairway / Black frames" data-image=gallery_wall_stairway_black.jpg"></span>
                    <span class="frame-option gw_material_gold" tabindex="0" data-id="stairway" data-options="gold" data-description="Gallery Wall Stairway / Gold frames" data-image=gallery_wall_stairway_gold.jpg"></span>
                    <span class="frame-option gw_material_walnut" tabindex="0" data-id="stairway" data-options="walnut" data-description="Gallery Wall Stairway / Walnut frames" data-image=gallery_wall_stairway_walnut.jpg"></span>
                    <span class="frame-option gw_material_silver" tabindex="0" data-id="stairway" data-options="silver" data-description="Gallery Wall Stairway / Silver frames" data-image=gallery_wall_stairway_silver.jpg"></span>
                    <span class="frame-option gw_material_oak" tabindex="0" data-id="stairway" data-options="oak" data-description="Gallery Wall Stairway / Oak frames" data-image=gallery_wall_stairway_oak.jpg"></span>
                    <span class="frame-option gw_material_white" tabindex="0" data-id="stairway" data-options="white" data-description="Gallery Wall Stairway / White frames" data-image=gallery_wall_stairway_white.jpg"></span>
                </div> <!-- gw_materials -->
                <div class="gw_checkout">
                    <div class="gw_price_wrapper">
                        <span class="gw_price">$<script>document.write(wall_pricing["stairway"]);</script></span>
                    </div>
                    <div class="gw_addtocart_wrapper">
                        <span class="gw13_button wall_add_to_cart cg_overlay_hook" onclick="wall__add_to_cart();">Add to cart</span>
                    </div>
                </div>
            </div> <!-- gw13_controller -->
        </div> <!-- gw13_wall_container -->
    </div> <!-- gw13_main_wrapper -->
    <div id="wall_preload">
        <img src="<?php echo PLUGIN_URL; ?>assets/img/gallery_wall_stairway_black.jpg">
        <img src="<?php echo PLUGIN_URL; ?>assets/img/gallery_wall_stairway_gold.jpg">
        <img src="<?php echo PLUGIN_URL; ?>assets/img/gallery_wall_stairway_walnut.jpg">
        <img src="<?php echo PLUGIN_URL; ?>assets/img/gallery_wall_stairway_silver.jpg">
        <img src="<?php echo PLUGIN_URL; ?>assets/img/gallery_wall_stairway_oak.jpg">
        <img src="<?php echo PLUGIN_URL; ?>assets/img/gallery_wall_stairway_white.jpg">
    </div>
</div>
<script>
    jQuery(document).ready( function() {
        wall_init('stairway', 'Gallery Wall Stairway', 8);
        //console.log(wall);
    });
</script>