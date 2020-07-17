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
								<input type="file" name="post-image" />
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
					Webcam.set({
						width: 320,
						height: 240,
						image_format: 'jpeg',
						jpeg_quality: 100,
						enable_flash: false,
					});
				
					Webcam.attach( '#fi-camera' );
				
					function take_snapshot() {
						Webcam.snap( function(data_uri) {
							var raw_image_data = data_uri.replace(/^data\:image\/\w+\;base64\,/, '');
							jQuery(".image-tag").val(raw_image_data);
							document.getElementById('fi-result').innerHTML = '<img src="'+data_uri+'"/>';
						} );
					}
				</script>
			</div>

		<?php
		endwhile;
	endif;
	?>

</div>

<?php get_footer(); ?>