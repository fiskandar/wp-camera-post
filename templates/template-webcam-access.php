<?php get_header(); ?>

<div class="content">

	<?php 
	if( have_posts() ):
		while( have_posts() ):
			the_post();
			?>

			<div class="fi-content">
				<h1 class="fi-title"><?php the_title(); ?></h1>
				<form action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" method="post" enctype="multipart/form-data">
					
					<?php wp_nonce_field( 'webcam-nonce', 'webcam-nonce-field' ); ?>
					<input type="hidden" name="post-image-raw" class="image-tag">
					<input type="hidden" name="action" value="webcam_submit">

					<div class="fi-camera-wrap">
						<div id="fi-camera"></div>
						<input type="button" class="btn" value="Take Snapshot" onClick="take_snapshot()">
					</div>
					
					<hr>
					
					<div class="fi-form-wrap">
						<div class="fi-form-right">
							<div id="fi-result">
								<span>Your Photo</span>
							</div>
							<p>
								<label for="post-image">Alternate Image</label>
								<input type="file" name="post-image" onchange="readURL(this)" />
							</p>
						</div>
						<div class="fi-form-left">
							<p>
								<input type="text" name="post-title" placeholder="Your Name">
							</p>
							<p>
								<?php 
								wp_editor( 
									'', 
									'post-content',
									[
										'media_buttons' => false,
									] 
								); 
								?>
							</p>
						</div>
					</div>
					
					<hr>

					<p>
						<input type="submit" value="Submit" class="btn">
					</p>
				</form>

				<script>
					var shutter 	 = new Audio();
					shutter.autoplay = false;
					shutter.src 	 = navigator.userAgent.match(/Firefox/) ? '<?php echo esc_url( WPCAM_URL ) ?>/assets/webcamjs/shutter.ogg' : '<?php echo esc_url( WPCAM_URL ) ?>/assets/webcamjs/shutter.mp3';

					Webcam.set({
						width: 320,
						height: 240,
						image_format: 'jpeg',
						jpeg_quality: 90,
					});
				
					Webcam.attach( '#fi-camera' );
				
					function take_snapshot() {
						Webcam.snap( function(data_uri) {
							try { shutter.currentTime = 0; } catch(e) {;} // fails in IE
							shutter.play();
							var raw_image_data = data_uri.replace(/^data\:image\/\w+\;base64\,/, '');
							jQuery(".image-tag").val(raw_image_data);
							document.getElementById('fi-result').innerHTML = '<img src="'+data_uri+'"/>';
						} );
					}

					function readURL(input) {
						if (input.files && input.files[0]) {
						    var reader = new FileReader();
						    reader.onload = function(e) {
						      	document.getElementById('fi-result').innerHTML = '<img src="'+e.target.result+'"/>';
						    }
						    reader.readAsDataURL(input.files[0]);
						    jQuery(".image-tag").val('');
					  	}
					}
				</script>
			</div>

		<?php
		endwhile;
	endif;
	?>

</div>

<?php get_footer(); ?>