<div class="bootstrapiso cls-configurator-dom" id="configurator-dom">
	<div class="container-fluid configurator-container text-center">

		<div class="row d-flex flex-row" id="product-config-preview justify-content-center">

			<div style="" class="mx-auto">

				<!-- ===== KONVA IMAGE CONTAINER =====-->
				<div id="image-upload-container" class="justify-content-center cg_left">
					<div id="product-card" class="card d-block mr-0 h-100 d-flex justify-content-between">

						<div class="card-body justify-content-center d-flex" id="konva-container-wrapper" onclick="check_offset_from_top()">
							<div class="shadow-class" id="konva-container"></div>
						</div>

						<div id="product-card-footer" class="card-footer text-center pl-0 pr-0">
							<!-- <span class="cg_visual">visualize your product</span> -->
							<figure class="frame-thumbnail frame-thumbnail-01">
								<img src="../../../uploadhandler/uploads/image_assets/background-wall-03-thumb.jpg" data-value="" alt="" onclick="show_modal(2)">
								<figcaption>
									<span>See on Wall</span>
								</figcaption>
							</figure>
							<figure class="frame-thumbnail frame-thumbnail-02">
								<img id="frame_preview_one" src="" alt="" onclick="show_frame_preview()">
								<figcaption>
									<span>Material</span>
								</figcaption>
							</figure>
							<figure class="frame-thumbnail frame-thumbnail-03">
								<img class="frame-details" src="../../../uploadhandler/uploads/image_assets/frame-details-thumb.png" data-value="" alt="">
								<figcaption>
									<span>Frame Details</span>
								</figcaption>
							</figure>
						</div>

					</div>
				</div>

				<!-- ===== RIGHT SIDE CONTAINER =====-->
				<div id="product-description-container" class="text-left cg_right">
					<div id="product-description-card" class="card">
						<div id="product-description-card-body" class="card-body">

							<div id="frame-details-list-group" class="list-group cg_frame_details">
								<div class="list-group-item">Outside Dimension: <div id="list-item-outer-dimension" class="d-inline product-data-item">...</div></div>
								<!-- <div class="list-group-item">Printed Image: <div id="list-printed-item-image-size" class="d-inline product-data-item">...</div></div> -->
								<div class="list-group-item">Frame: <div id="list-item-frame" class="d-inline product-data-item">...</div></div>
								<!-- <div class="list-group-item"><div id="list-item-top-mat" class="d-inline product-data-item">...</div></div> -->
								<div class="list-group-item cg_item_price_parent">Price: <div id="list-item-price" class="d-inline product-data-item list-item-price">...</div></div>
							</div>

							<!-- ************************************************************************************************ -->

							<!--======= ACCORDION START ========-->
							<div id="accordion" role="tablist" aria-multiselectable="true" class="mt-2 cg_accordion">


								<!---=== ( FRAME SIZES SELECTION ) ===-->
								<div id="sizing-options" class="card mt-1 options-card-class-style cg_frame_size">
									<div class="card-header card-class-header-style p-0" role="tab" id="headingOne">
										<div id="product-sizes-btn-closed">
											<button type="button" class="btn p-1 w-100 bg-transparent d-block" data-toggle="collapse" data-target="#collapseOne" aria-expanded="false" aria-controls="collapseOne">
												<div class="pull-left cg_acc_title cg_acc_title_1"><span>Outside&nbsp;</span>Size</div>
												<div class="pull-right">
													<span id="mobile-size-optons"></span>
													<span class="pull-right"><i class="fa fa-chevron-down"></i></span>
												</div>
											</button>
										</div>
										<div id="product-sizes-btn-open" class="hide">
											<button type="button" class="btn p-1 w-100 bg-transparent d-block" data-toggle="collapse" data-target="#collapseOne" aria-expanded="false" aria-controls="collapseOne">
												<div class="pull-left cg_acc_title cg_acc_title_1">Size</div>
												<div class="pull-right">
													<!-- <span id="mobile-size-optons"></span> -->
													<span class="pull-right"><i class="fa fa-chevron-up"></i></span>
												</div>
											</button>
										</div>
									</div>

									<div id="collapseOne" class="collapse" role="tabpanel" aria-labelledby="headingOne" data-parent="#accordion">
										<div class="card-body options-card-body-class">
											<ul id="configurator-size-options" class="list-group w-100 m-0">
											</ul>
										</div>
									</div>
								</div>

								<!---=== ( FRAME TYPE SELECTION ) ===-->
								<div id="frame-options" class="card mt-1 options-card-class-style cg_frame_type">
									<div class="card-header card-class-header-style p-0" role="tab" id="headingTwo">
										<div id="product-frame-btn-closed">
											<button type="button" class="btn p-1 w-100 bg-transparent d-block" data-toggle="collapse" data-target="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo">
												<div class="pull-left cg_acc_title cg_acc_title_2">Frames</div>
												<div class="pull-right">
													<span id="mobile-frame-description"></span>
													<span class="pull-right"><i class="fa fa-chevron-down"></i></span>
												</div>
											</button>
										</div>
										<div id="product-frame-btn-open" class="hide">
											<button type="button" class="btn p-1 w-100 bg-transparent d-block" data-toggle="collapse" data-target="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo">
												<div class="pull-left cg_acc_title cg_acc_title_2">Frames</div>
												<div class="pull-right">
													<!-- <span id="mobile-frame-description"></span> -->
													<span class="pull-right"><i class="fa fa-chevron-up"></i></span>
												</div>
											</button>
										</div>
									</div>
									<div id="collapseTwo" class="collapse" role="tabpanel" aria-labelledby="headingTwo" data-parent="#accordion">
										<div class="card-body p-0 options-card-body-class text-center">
											<div class="mt-2">
												<div class="frame-box-wrapper">
													<img class="frame-box" onclick="change_frame('1')"
														 src="../../../uploadhandler/uploads/image_assets/10771303-thumb.jpg"
														 alt="frames.frame_sku"/>
													<div class="frame-description-class">
                                                        <script>
                                                            document.write(frame_list[1].frame_description);
                                                        </script>
													</div>
												</div>
												<div class="frame-box-wrapper">
													<img class="frame-box" onclick="change_frame('8')"
														 src="../../../uploadhandler/uploads/image_assets/10771086-thumb.jpg"
														 alt="frames.frame_sku"/>
													<div class="frame-description-class">
                                                        <script>
                                                            document.write(frame_list[8].frame_description);
                                                        </script>
													</div>
												</div>
												<div class="frame-box-wrapper">
													<img class="frame-box" onclick="change_frame('3')"
														 src="../../../uploadhandler/uploads/image_assets/10771054-thumb.jpg"
														 alt="frames.frame_sku"/>
													<div class="frame-description-class">
                                                        <script>
                                                            document.write(frame_list[3].frame_description);
                                                        </script>
													</div>
												</div>
											</div>
											<div class="mt-1">
												<div class="frame-box-wrapper">
													<img class="frame-box" onclick="change_frame('4')"
														 src="../../../uploadhandler/uploads/image_assets/10771302-thumb.jpg"
														 alt="frames.frame_sku"/>
													<div class="frame-description-class">
                                                        <script>
                                                            document.write(frame_list[4].frame_description);
                                                        </script>
													</div>
												</div>
												<div class="frame-box-wrapper">
													<img class="frame-box" onclick="change_frame('5')"
														 src="../../../uploadhandler/uploads/image_assets/10771009-thumb.jpg"
														 alt="frames.frame_sku"/>
													<div class="frame-description-class">
                                                        <script>
                                                            document.write(frame_list[5].frame_description);
                                                        </script>
													</div>
												</div>
												<div class="frame-box-wrapper">
													<img class="frame-box" onclick="change_frame('7')"
														 src="../../../uploadhandler/uploads/image_assets/10771000-thumb.jpg"
														 alt="frames.frame_sku"/>
													<div class="frame-description-class">
                                                        <script>
                                                            document.write(frame_list[7].frame_description);
                                                        </script>
													</div>
												</div>
											</div>
										</div>
									</div>
								</div>
								<div id="matting-options" class="card mt-1 options-card-class-style cg_matting_options">
									<div class="card-header card-class-header-style p-0" role="tab" id="headingThree">
										<div id=""> <!-- product-matting-btn-closed -->
											<button type="button" class="btn p-1 bg-transparent w-100 d-block" data-toggle="collapse" data-target="#collapseThree" aria-expanded="false" aria-controls="collapseThree">
												<div class="pull-left cg_acc_title cg_acc_title_3">Matting</div>
												<div class="pull-right">
													<!-- <span id="mobile-matting"></span> -->
													<div class="switch_test">
														<!-- <span class="switch_test_a" id="cg_on"></span>
														<span class="switch_test_b" id="cg_off"></span> -->
													</div>
												</div>
											</button>
										</div>
										<div id="product-matting-btn-open" class="hide">
											<button type="button" class="btn p-1 bg-transparent w-100 d-block" data-toggle="collapse" data-target="#collapseThree" aria-expanded="false" aria-controls="collapseThree">
												<div class="pull-left cg_acc_title cg_acc_title_3">Matting</div>
												<div class="pull-right">
													<span id="mobile-matting"></span>
													<span class="pull-right"><i class="fa fa-chevron-up"></i></span>
												</div>
											</button>
										</div>
									</div>
									<div id="" class="collapse" role="tabpanel" aria-labelledby="headingThree" data-parent="#accordion"> <!-- collapseThree -->
										<div class="card-body p-0">
											<div class="btn-group-vertical w-100 mt-2">
												<div class="d-block mx-auto">
													<!-- Small switch -->
													<div class="form-group-two hide">
													   <span class="switch switch-top mt-2">
														 <input checked type="checkbox" class="bottom-mat-switch switch switch-mod" id="switch-top" onchange="top_mat_setting(event)">
														 <label for="switch-top">Enable / Disable Matting</label>
													   </span>
													</div>
												</div>
												<div class="d-block mx-auto mb-1">
													<!-- Small switch -->
													<!-- <div class="form-group-two">
													   <span class="switch switch-top mt-2">
														  <select data-style="border border-dark bg-light text-secondary" class="form-control cls-top-mat-select" id="top_mat_width" name="top_mat_width" onchange="set_top_mat_width(this);">
															 <option value="0">No Matting</option>
															 <option value="144" selected>Mat Size: 2 in</option>
														  </select>
													   </span>

									   
													</div> -->
												</div>
											</div>
										</div>
									</div>                                   
								</div>

								<div class="invisible-glass-option" id="invisible-glass-option">
									<div class="invisible-glass-label" onclick="invisible_glass_toggle__regular();" style="cursor: pointer;">Regular Glass</div>
									<div class="switch switch-top">
										<input type="checkbox" class="bottom-mat-switch switch switch-mod" id="switch-invisible-glass" onchange="invisible_glass_toggle()">
										<label for="switch-invisible-glass">Invisible Glass</label>
									</div>
									<div class="invisible-glass-label regular-glass-label" onclick="invisible_glass_toggle__invisible();" style="cursor: pointer;">Invisible Glass</div>
								</div> <!-- invisible-glass-option -->

								<!---=== ( PREVIEW OPTIONS IN MOBILE MODE ) ===-->
								<div id="preview-options-mobile" class="card options-card-class-style mt-1 hide cg_preview_options_mobile">
									<div class="card-header card-class-header-style p-0" role="tab" id="headingPreviews">
										<div id="product-preview-btn-closed" class="hide">
											<button type="button" class="btn bg-transparent p-1 w-100 d-block" data-toggle="collapse" data-target="#collapsePreviews" aria-expanded="false" aria-controls="collapsePreviews">
												<i class="fa fa-chevron-down pull-right"></i><span class="pull-left">Previews</span>
											</button>
										</div>
										<div id="product-preview-btn-open" class="hide">
											<button type="button" class="btn bg-transparent p-1 w-100 d-block" data-toggle="collapse" data-target="#collapsePreviews" aria-expanded="false" aria-controls="collapsePreviews">
												<i class="fa fa-chevron-up pull-right"></i><span class="pull-left">Previews</span>
											</button>
										</div>
									</div>

									<!-- <div id="collapsePreviews" class="collapse collapsed show" role="tabpanel" aria-labelledby="headingPreviews" data-parent="#accordion">
										<div class="card-body options-card-body-class">
											<div>
												<img src="../../../uploadhandler/uploads/image_assets/background-wall-01-thumb.jpg" data-value="" alt="" onclick="show_modal(1)">
												<img src="../../../uploadhandler/uploads/image_assets/background-wall-03-thumb.jpg" data-value="" alt="" onclick="show_modal(2)">
												<img id="frame_preview_one_sub" src="" alt="" onclick="show_frame_preview(1)">
												<img id="frame_preview_two_sub" src="" alt="" onclick="show_frame_preview(2)">
												<img id="frame_preview_three_sub" src="" alt="" onclick="show_frame_preview(3)">
											</div>
										</div>
									</div>   -->                                  
								</div>
								
							</div><!--======== ACCORDION ENDS ===========-->

							<!---=== ( PRODUCT DETAIL IN MOBILE MODE ) ===-->
							<div class="cg_accordion">
								<div id="product-detail-mobile" class="card options-card-class-style hide cg_product_details_mobile">
									<div class="card-header card-class-header-style p-0" role="tab" id="headingDetail">
										<div id="product-detail-btn-closed" class="hide">
											<button type="button" class="btn bg-transparent p-1 w-100 d-block" data-toggle="collapse" data-target="#collapseDetail" aria-expanded="true" aria-controls="collapseDetail">
												<div class="pull-left">Product Details</div>
												<div class="pull-right">
													<span id="mobile-pricing">...</span>
													<span class="pull-right"><i class="fa fa-chevron-down"></i></span>
												</div>
											</button>
										</div>
										<div id="product-detail-btn-open">
											<button type="button" class="btn bg-transparent p-1 w-100 d-block" data-toggle="collapse" data-target="#collapseDetail" aria-expanded="true" aria-controls="collapseDetail">
												<div class="pull-left">Product Details</div>
												<div class="pull-right">
													<span class="pull-right"><i class="fa fa-chevron-up"></i></span>
												</div>
											</button>
										</div>
									</div>

									<div id="collapseDetail" class="collapse collapsed show" role="tabpanel" aria-labelledby="headingDetail" data-parent="#accordion">
										<div class="card-body options-card-body-class">
											<div>
												<div id="frame-details-list-group" class="list-group">
													<div class="list-group-item">Price: <div id="list-item-price-mobile" class="d-inline product-data-item">...</div></div> 
													<div class="list-group-item">Outside Dimension: <div id="list-item-outer-dimension-mobile" class="d-inline product-data-item">...</div></div>
													<div class="list-group-item">Frame Description: <div id="list-item-frame-mobile" class="d-inline product-data-item">...</div></div>
													<div class="list-group-item"><div id="list-item-top-mat-mobile" class="d-inline product-data-item">...</div></div>
												</div>
											</div>
										</div>
									</div>
								</div>

								<!-- Preview options copy -->
								<div id="collapsePreviews" class="collapse collapsed show visualize_copy" role="tabpanel" aria-labelledby="headingPreviews" data-parent="#accordion">
									<div class="card-body options-card-body-class">
										<div>
											<figure class="frame-thumbnail frame-thumbnail-01">
												<img src="../../../uploadhandler/uploads/image_assets/background-wall-03-thumb.jpg" data-value="" alt="" onclick="show_modal(2)">
												<figcaption>
													<span>See on Wall</span>
												</figcaption>
											</figure>
											<figure class="frame-thumbnail frame-thumbnail-02">
												<img id="frame_preview_one_sub" src="" alt="" onclick="show_frame_preview(1)">
												<img id="frame_preview_two_sub" src="" alt="" onclick="show_frame_preview(2)">
												<img id="frame_preview_three_sub" src="" alt="" onclick="show_frame_preview(3)">
												<figcaption>
													<span>Material</span>
												</figcaption>
											</figure>
											<figure class="frame-thumbnail frame-thumbnail-03">
												<img class="frame-details" src="../../../uploadhandler/uploads/image_assets/frame-details-thumb.png" data-value="" alt="">
												<figcaption>
													<span>Frame Details</span>
												</figcaption>
											</figure>
										</div>
									</div>
								</div>  

								<div class="car mt-1">                                    
									<div class="p-2">
										<button type="button" id="change-image-button" class="bg-info change_image cg_overlay_hook" onclick="change_image('custom')">Change Image</button>
									</div>
								</div>
							</div> <!-- cg_accordion ends -->

							<div class="row base_button" id="action-buttons-row">
								<div class="col col-12">
									<div class="justify-content-center d-flex text-white">
										<div id="btn-send-to-cart-wrapper" class="p-2">
											<button type="button" id="btn-send-to-cart" class="btn-send-to-cart cg_overlay_hook" onclick="send_to_cart()">Add to Cart</button>
									</div>
									</div>
								</div>
							</div>

							<!-- ************************************************************************************************ -->

						</div>
					</div>
				</div>
			</div>

		</div><!--===== THIS IS THE END OF THE CONTAINER FOR BOTH THE IMAGE AND THE RIGHT SIDE =====-->

		<!-- <div class="row">
		   <div class="col col-12">
			  <div id="configurator-options-wrapper" class="d-flex justify-content-center p-3 text-success p-0" style="padding-top: 5px !important;">
				 <div id="configurator-options" class="d-inline-block" style="318px;">
				 </div>
			  </div>
		   </div>
		</div>
	 </div> -->

		<!-- ===============  VISUALIZER MODAL  ================ -->
		<div class="modal fade in" id="visualizer-modal" tabindex="-1" data-keyboard="false" data-backdrop="static" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
			<div class="modal-dialog modal-dialog-centered" role="document">
				<div class="modal-content">
					<div class="modal-header">
						<!-- <h5 class="modal-title" id="exampleModalCenterTitle">Visualize your product</h5> -->
						<div type="text" id="background_image_mode"></div>
					</div>
					<div class="modal-body d-flex justify-content-center">
						<div>
							<div id="visualizer-container" class="d-block"></div>
							<div class="text-center mt-1">
								You can move the frame around
							</div>
						</div>

					</div>
					<div class="modal-footer">
						<button type="button" class="btn btn-secondary flex" data-dismiss="modal">Close</button>
					</div>

				</div>
			</div>
		</div>

		<!-- ============= Frame Preview Modal ============== -->
		<div class="modal fade in" id="frame-preview-modal" tabindex="-1" data-keyboard="false" data-backdrop="static" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
			<div class="modal-dialog modal-dialog-centered" role="document">
				<div class="modal-content">
					<!-- header -->
					<div class="modal-header">
						<h5 class="modal-title" id="preview-modal-title"></h5>
						<div type="text" id="background_image_mode"></div>
					</div>
					<!-- main body -->
					<div class="modal-body d-flex justify-content-center">

						<div id="carouselExampleIndicators" class="carousel slide w-100" data-ride="carousel" data-interval="false">
							<ol class="carousel-indicators">
								<li data-target="#carouselExampleIndicators" data-slide-to="0" class="active"></li>
								<li data-target="#carouselExampleIndicators" data-slide-to="1"></li>
								<li data-target="#carouselExampleIndicators" data-slide-to="2"></li>
							</ol>
							<div class="carousel-inner text-center">
								<div class="carousel-item active">
									<img id="carousel-image-one" class="d-block w-100" src="" alt="First slide">
								</div>
								<div class="carousel-item">
									<img id="carousel-image-two" class="d-block w-100" src="" alt="Second slide">
								</div>
								<div class="carousel-item">
									<img id="carousel-image-three" class="d-block mx-auto" src="" alt="Third slide">
								</div>
							</div>
							<a class="carousel-control-prev" href="#carouselExampleIndicators" role="button" data-slide="prev">
								<span><i class="fa fa-chevron-left"></i></span>
							</a>
							<a class="carousel-control-next" href="#carouselExampleIndicators" role="button" data-slide="next">
								<span><i class="fa fa-chevron-right"></i></span>
							</a>
						</div>

					</div>
					<!-- footer -->
					<div class="modal-footer">

						<button type="button" class="btn btn-secondary flex" data-dismiss="modal">Close</button>
					</div>
				</div>
			</div>
		</div>

		<div class="modal fade in cg_progress" id="invalid-item-guid" tabindex="-1" data-keyboard="false" data-backdrop="static" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
			<div class="modal-dialog modal-dialog-centered" role="document">
				<div class="modal-content text-center">
					<div class="modal-body d-flex justify-content-center">
						<div>
							<div class="pb-3">
								<i class="fa fa-exclamation-circle" aria-hidden="true"></i>
							</div>
							<div>Please use the <span class="font-weight-bold text-primary">EDIT</span> button in the</div>
							<div>Shopping Cart to edit an item.</div>
						</div>
					</div>
					<div class="modal-footer">
						<a href="/online-framing">
							<button type="button" class="btn btn-secondary flex cg_overlay_hook" onclick="">Please upload an image.</button>
						</a>
					</div>
				</div>
			</div>
		</div>

		<!--img id="converted-image" src="" alt=""-->
		<div id="fine-uploader-element"></div>

	</div>
	<script type="text/javascript">
        info = set_top_mat_width(this);
	</script>