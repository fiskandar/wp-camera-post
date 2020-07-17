<?php 
/*
 * Single template for webcam post type
 */

get_header(); ?>

<div class="content">
	<?php 
	if( have_posts() ):
		while( have_posts() ):
			the_post();
			?>

			<div class="fi-content">
				<h1><?php the_title(); ?></h1>

				<?php
				// image data 
				$image_data = wp_get_attachment_image_src( get_post_thumbnail_id(), 'full' ); 
				?>

				<h2>Image Data</h2>
				<pre class="fi-image-data"><?php print_r( $image_data ); ?></pre>

				<h2>The Image</h2>
				<?php the_post_thumbnail( 'full' ); ?>

				<h2>Content</h2>
				<?php 
				// content
				the_content(); ?>

			</div>

			<?php
		endwhile;
	endif;
	?>
</div>

<?php get_footer(); ?>