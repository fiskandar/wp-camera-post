<?php
/**
 * Plugin Name: WP Webcam Post
 * Description: WP Webcam
 * Author:      feriyadiiskandar
 * Version:     1.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'WPCAM_PATH', plugin_dir_path( __FILE__ ) );
define( 'WPCAM_URL', plugins_url( basename( plugin_dir_path( __FILE__ ) ), basename( __FILE__ ) ) );

if( !class_exists( 'WP_Webcam_Post' ) ) {
	/**
	 * Main class.
	 */
	class WP_Webcam_Post {

		/**
		 * Constructor.
		 */
		public static function init() {
			$self = new self();
			add_action( 'init', [ $self, 'fi_register_post_type' ] );
			add_action( 'plugins_loaded', [ $self, 'fi_create_webcam_page' ] );
			add_action( 'template_include', [ $self, 'fi_webcam_page_redirect' ] );
			add_action( 'wp_enqueue_scripts', [ $self, 'fi_webcam_scripts' ] );
			add_action( 'admin_post_nopriv_webcam_submit', [ $self, 'fi_webcam_post_image' ] );
			add_action( 'admin_post_webcam_submit', [ $self, 'fi_webcam_post_image' ] );
		}

		/**
		 * Register CPT
		 */
		public function fi_register_post_type() {
			register_post_type(
				'webcam',
				[
					'label' 			  => 'Webcam Post',
					'public' 			  => true,
					'exclude_from_search' => true,
					'supports'			  => [ 'title', 'editor', 'thumbnail' ]
				]
			);
		}

		/**
		 * Create webcam post page.
		 */
		public function fi_create_webcam_page() {
			$title       = 'Webcam Access Page';
			$webcam_page = get_page_by_title( $title );
			$page_id     = -1;

			if( $webcam_page ) {
				$page_id = $webcam_page->ID;
			} else {
				$webcam_page = [
					'comment_status' => 'closed',
					'ping_status'    => 'closed',
					'post_author'    => 1,
					'post_name'      => 'webcam-page',
					'post_title'     => $title,
					'post_status'    => 'publish',
					'post_type'      => 'page',
				];

				$page_id = wp_insert_post( $webcam_page );
			}

			if( !$page_id ) {
				wp_die( 'Error creating webcam access page.' );
			} else {
				update_post_meta( $page_id, '_wp_page_template', 'webcam-access-page.php' );
			}
		}

		/**
		 * Redirect webcam access page & webcam single page
		 */
		public function fi_webcam_page_redirect( $template ) {
			if( is_page_template( 'webcam-access-page.php' ) ) {
				$template = WPCAM_PATH . 'templates/template-webcam-access.php';
			}
			if( is_singular( 'webcam' ) ) {
				$template = WPCAM_PATH . 'templates/single-webcam.php';
			}
			return $template;
		}

		/**
		 * Register styles
		 */
		public function fi_webcam_scripts() {
			if( is_page_template( 'webcam-access-page.php' ) ) {
				wp_enqueue_style( 'webcam-css', WPCAM_URL . '/assets/webcam-post.css', array() );
				wp_enqueue_script( 'webcam', 'https://cdnjs.cloudflare.com/ajax/libs/webcamjs/1.0.25/webcam.min.js', array( 'jquery' ) );
				wp_enqueue_media();
			}
			if( is_singular( 'webcam' ) ) {
				wp_enqueue_style( 'webcam-css', WPCAM_URL . '/assets/webcam-post.css', array() );
			}
		}

		/**
		 * Handling submission
		 */
		public function fi_webcam_post_image() {
			if( isset( $_POST['webcam-nonce-field'] ) && wp_verify_nonce( $_POST['webcam-nonce-field'], 'webcam-nonce' ) ) {
				
				require_once( ABSPATH . 'wp-admin/includes/image.php' );
				require_once( ABSPATH . 'wp-admin/includes/file.php' );
				require_once( ABSPATH . 'wp-admin/includes/media.php' );

				$post_title    = isset( $_POST['post-title'] ) ? sanitize_text_field( $_POST['post-title'] ) : '';
				$post_content  = isset( $_POST['post-content'] ) ? $_POST['post-content'] : '';
				$image_data    = isset( $_POST['post-image-raw'] ) ? $_POST['post-image-raw'] : '';
				$alternate_img = isset( $_FILES['post-image'] ) ? $_FILES['post-image'] : '';
				$attachment_id = 1;

				if( ! empty( $image_data ) ) {
					// handle webcam image
					$filename       = 'image_'.date('m-d-Y_his') . '.jpg';
					$upload_dir     = wp_upload_dir();
					$upload_path    = str_replace( '/', DIRECTORY_SEPARATOR, $upload_dir['path'] ) . DIRECTORY_SEPARATOR;
					$decoded_image  = base64_decode( $image_data );
					$wp_filetype    = wp_check_filetype( $filename, null );
					
					file_put_contents( $upload_path . $filename, $decoded_image );
					
					$attachment     = array(
						'post_mime_type' => $wp_filetype['type'],
						'post_title'     => sanitize_file_name( $filename ),
						'post_content'   => '',
						'post_status'    => 'inherit'
					);
					$attachment_id   = wp_insert_attachment( $attachment, $upload_path . $filename );
					$attachment_data = wp_generate_attachment_metadata( $attachment_id, $upload_path . $filename );
					wp_update_attachment_metadata( $attachment_id, $attachment_data );
				} elseif( !empty( $alternate_img ) ) {
					// handle alternate image
					$attachment_id = media_handle_upload( 'post-image', 0 );
				}

				if( empty( $post_title ) ) {
					$post_title = get_the_title( $attachment_id );
				}

				// webcam post data
				$post_data = array(
					'post_title'   => $post_title,
					'post_content' => $post_content,
					'post_status'  => 'publish',
					'post_author'  => 1,
					'post_type'    => 'webcam',
				);

				// insert new post
				$post_id = wp_insert_post( $post_data );

				// set post thumbnail
				set_post_thumbnail( $post_id, $attachment_id );

				wp_safe_redirect( get_permalink( $post_id ) );

			}
		}
	}

	WP_Webcam_Post::init();
}
