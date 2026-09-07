<?php
/**
 * File Upload Handler (Pro).
 *
 * Handles AJAX file uploads, mime type checks, size limits, and secure storage.
 *
 * @package APF_Aslam
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class APF_File_Uploader {

	/**
	 * Constructor.
	 */
	public function __construct() {
		add_action( 'wp_ajax_apf_upload_file', array( $this, 'handle_upload' ) );
		add_action( 'wp_ajax_nopriv_apf_upload_file', array( $this, 'handle_upload' ) );

		add_action( 'wp_ajax_apf_remove_file', array( $this, 'handle_remove' ) );
		add_action( 'wp_ajax_nopriv_apf_remove_file', array( $this, 'handle_remove' ) );
	}

	/**
	 * Handle AJAX file upload.
	 */
	public function handle_upload() {
		check_ajax_referer( 'apf_frontend_nonce', 'nonce' );

		if ( empty( $_FILES['apf_file'] ) ) {
			wp_send_json_error( array( 'message' => __( 'No file was submitted.', 'apf-aslam' ) ) );
		}

		$file = $_FILES['apf_file'];

		if ( $file['error'] !== UPLOAD_ERR_OK || ! is_uploaded_file( $file['tmp_name'] ) ) {
			wp_send_json_error( array( 'message' => __( 'File upload security verification failed.', 'apf-aslam' ) ) );
		}

		// Field configuration retrieval.
		$field_id   = sanitize_key( $_POST['field_id'] ?? '' );
		$product_id = absint( $_POST['product_id'] ?? 0 );

		$allowed_exts = array( 'jpg', 'jpeg', 'png', 'gif', 'webp', 'pdf', 'txt', 'doc', 'docx', 'zip' );
		$max_size_mb  = 10;

		if ( $product_id && $field_id ) {
			$loader = APF_Loader::get_instance();
			$fields = $loader->fields_manager->get_fields_for_product( $product_id );
			if ( isset( $fields[ $field_id ] ) ) {
				$f = $fields[ $field_id ];
				if ( ! empty( $f['allowed_extensions'] ) ) {
					$allowed_exts = array_map( 'trim', explode( ',', strtolower( $f['allowed_extensions'] ) ) );
				}
				if ( ! empty( $f['max_file_size_mb'] ) ) {
					$max_size_mb = floatval( $f['max_file_size_mb'] );
				}
			}
		}

		// Check file size.
		$max_bytes = $max_size_mb * 1024 * 1024;
		if ( $file['size'] > $max_bytes ) {
			wp_send_json_error( array( 'message' => sprintf( __( 'File exceeds the maximum limit of %s MB.', 'apf-aslam' ), $max_size_mb ) ) );
		}

		// Check file extension.
		$file_info = wp_check_filetype( $file['name'] );
		$ext       = strtolower( $file_info['ext'] );

		// Strict security: Block any server-side script extensions regardless of settings.
		$forbidden_exts = array( 'php', 'php3', 'php4', 'php5', 'phtml', 'phps', 'exe', 'sh', 'pl', 'cgi', 'py', 'asp', 'aspx', 'jsp', 'js', 'html', 'htm' );
		if ( empty( $ext ) || in_array( $ext, $forbidden_exts, true ) || ! in_array( $ext, $allowed_exts, true ) ) {
			wp_send_json_error( array( 'message' => sprintf( __( 'Invalid file extension. Allowed types: %s', 'apf-aslam' ), implode( ', ', $allowed_exts ) ) ) );
		}

		// Target secure folder.
		$upload_dir = wp_upload_dir();
		$apf_dir    = $upload_dir['basedir'] . '/apf-uploads';
		$apf_url    = $upload_dir['baseurl'] . '/apf-uploads';

		if ( ! file_exists( $apf_dir ) ) {
			wp_mkdir_p( $apf_dir );
			@file_put_contents( $apf_dir . '/.htaccess', "deny from all\n<FilesMatch \"\.(?i:jpg|jpeg|png|gif|webp|pdf|svg|txt|doc|docx|zip)$\">\nallow from all\n</FilesMatch>" );
			@file_put_contents( $apf_dir . '/index.php', '<?php /* Silence is golden */' );
		}

		$clean_name = sanitize_file_name( pathinfo( $file['name'], PATHINFO_FILENAME ) );
		$unique_filename = $clean_name . '-' . wp_generate_password( 12, false ) . '.' . $ext;
		$target_file     = $apf_dir . '/' . $unique_filename;
		$target_url      = $apf_url . '/' . $unique_filename;

		if ( ! move_uploaded_file( $file['tmp_name'], $target_file ) ) {
			wp_send_json_error( array( 'message' => __( 'Could not save file to disk.', 'apf-aslam' ) ) );
		}

		$is_image = in_array( $ext, array( 'jpg', 'jpeg', 'png', 'gif', 'webp' ), true );

		wp_send_json_success(
			array(
				'url'      => $target_url,
				'name'     => $file['name'],
				'is_image' => $is_image,
				'size'     => size_format( $file['size'] ),
			)
		);
	}

	/**
	 * Handle AJAX file removal.
	 */
	public function handle_remove() {
		check_ajax_referer( 'apf_frontend_nonce', 'nonce' );

		$file_url = esc_url_raw( $_POST['file_url'] ?? '' );
		if ( empty( $file_url ) ) {
			wp_send_json_error();
		}

		$upload_dir = wp_upload_dir();
		$apf_url    = $upload_dir['baseurl'] . '/apf-uploads/';
		$apf_dir    = $upload_dir['basedir'] . '/apf-uploads';

		// Security: verify URL belongs to apf-uploads directory and prevent directory traversal.
		if ( 0 === strpos( $file_url, $apf_url ) ) {
			$file_name   = basename( $file_url );
			$target_path = $apf_dir . '/' . $file_name;
			$real_target = realpath( $target_path );
			$real_dir    = realpath( $apf_dir );

			if ( $real_target && $real_dir && 0 === strpos( $real_target, $real_dir ) && file_exists( $real_target ) ) {
				@unlink( $real_target );
			}
		}

		wp_send_json_success();
	}
}
