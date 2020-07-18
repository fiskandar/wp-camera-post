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

					<div class="fi-camera-wrap">
						<video autoplay="true" id="video-webcam"></video>
						<input type="button" class="btn" value="Take Snapshot" onClick="takeSnapshot()">
					</div>
					
					<hr>
					
					<div class="fi-form-wrap">

						<div class="fi-form-right">
							<div id="fi-result">
								<span>Your Photo</span>
							</div>
							<p>
								<label><strong>Alternate Image</strong></label>
								<input type="file" name="post-image" onchange="readURL(this)">
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
						<input type="hidden" name="post-image-raw" id="image-data">
						<input type="hidden" name="action" value="webcam_submit">
						<input type="submit" value="Submit" class="btn">
					</p>
				</form>

				<script>
					var videoElement = document.querySelector( '#video-webcam' );

					window.addEventListener( 'load', (event) => {
						navigator.getUserMedia = navigator.getUserMedia || navigator.webkitGetUserMedia || navigator.mozGetUserMedia || navigator.msGetUserMedia || navigator.oGetUserMedia;
						if( navigator.getUserMedia ) {
							navigator.getUserMedia({ video: true }, handleVideo, handleError);
						}
						function handleVideo( stream ) {
							videoElement.srcObject = stream;
						}

						function handleError(e) {
							console.log( 'Error!' );
						}
					});

					function takeSnapshot() {
					    var context;
					    var width   = videoElement.offsetWidth, 
					    	height  = videoElement.offsetHeight,
					    	canvas  = document.createElement('canvas'),
					    	context = canvas.getContext('2d'),
					    	imgSrc  = '',
					    	imgData = '';

					    canvas.width  = width;
					    canvas.height = height;

					    context.drawImage(videoElement, 0, 0, width, height);

					    imgSrc  = canvas.toDataURL('image/png');
					    imgData = imgSrc.replace(/^data\:image\/\w+\;base64\,/, '');

						document.getElementById('image-data').value = imgData;
					    document.getElementById('fi-result').innerHTML = '<img src="'+imgSrc+'"/>';
					}

					function readURL(input) {
						if (input.files && input.files[0]) {
						    var reader = new FileReader();
						    reader.onload = function(e) {
						      	document.getElementById('fi-result').innerHTML = '<img src="'+e.target.result+'"/>';
						    }
						    reader.readAsDataURL(input.files[0]);
						    document.getElementById('image-data').value = '';
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