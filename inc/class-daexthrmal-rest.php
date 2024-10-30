<?php
/**
 * Here the REST API endpoint of the plugin are registered.
 *
 * @package hreflang-manager-lite
 */

/**
 * This class should be used to work with the REST API endpoints of the plugin.
 */
class Daexthrmal_Rest {

	/**
	 * The singleton instance of the class.
	 *
	 * @var null
	 */
	protected static $instance = null;

	/**
	 * An instance of the shared class.
	 *
	 * @var Daexthrmal_Shared|null
	 */
	private $shared = null;

	/**
	 * Constructor.
	 */
	private function __construct() {

		// Assign an instance of the shared class.
		$this->shared = Daexthrmal_Shared::get_instance();

		/**
		 * Add custom routes to the Rest API.
		 */
		add_action( 'rest_api_init', array( $this, 'rest_api_register_route' ) );
	}

	/**
	 * Create a singleton instance of the class.
	 *
	 * @return self|null
	 */
	public static function get_instance() {

		if ( null === self::$instance ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 * Add custom routes to the Rest API.
	 *
	 * @return void
	 */
	public function rest_api_register_route() {

		// Add the GET 'hreflang-manager-lite/v1/options' endpoint to the Rest API.
		register_rest_route(
			'hreflang-manager-lite/v1',
			'/read-options/',
			array(
				'methods'             => 'POST',
				'callback'            => array( $this, 'rest_api_daext_hreflang_manager_read_options_callback' ),
				'permission_callback' => array( $this, 'rest_api_daext_hreflang_manager_read_options_callback_permission_check' ),
			)
		);

		// Add the POST 'hreflang-manager-lite/v1/options' endpoint to the Rest API.
		register_rest_route(
			'hreflang-manager-lite/v1',
			'/options',
			array(
				'methods'             => 'POST',
				'callback'            => array( $this, 'rest_api_daext_hreflang_manager_update_options_callback' ),
				'permission_callback' => array( $this, 'rest_api_daext_hreflang_manager_update_options_callback_permission_check' ),

			)
		);
	}

	/**
	 * Callback for the GET 'hreflang-manager-lite/v1/options' endpoint of the Rest API.
	 *
	 * @return WP_REST_Response
	 */
	public function rest_api_daext_hreflang_manager_read_options_callback() {

		// Generate the response.
		$response = array();
		foreach ( $this->shared->get( 'options' ) as $key => $value ) {
			$response[ $key ] = get_option( $key );
		}

		// Prepare the response.
		$response = new WP_REST_Response( $response );

		return $response;
	}

	/**
	 * Check the user capability.
	 *
	 * @return true|WP_Error
	 */
	public function rest_api_daext_hreflang_manager_read_options_callback_permission_check() {

		if ( ! current_user_can( 'manage_options' ) ) {
			return new WP_Error(
				'rest_read_error',
				'Sorry, you are not allowed to read the Hreflang Manager options.',
				array( 'status' => 403 )
			);
		}

		return true;
	}

	/**
	 * Callback for the POST 'hreflang-manager-lite/v1/options' endpoint of the Rest API.
	 *
	 * This method is in the following contexts:
	 *
	 *  - To update the plugin options in the "Options" menu.
	 *
	 * @param object $request The request data.
	 *
	 * @return WP_REST_Response
	 */
	public function rest_api_daext_hreflang_manager_update_options_callback( $request ) {

		// Get and sanitize data --------------------------------------------------------------------------------------.

		$options = array();

		// Get and sanitize data --------------------------------------------------------------------------------------.

		// General ----------------------------------------------------------------------------------------------------.
		$options['daexthrmal_detect_url_mode']     = $request->get_param( 'daexthrmal_detect_url_mode' ) !== null ? sanitize_key( $request->get_param( 'daexthrmal_detect_url_mode' ) ) : null;
		$options['daexthrmal_https']               = $request->get_param( 'daexthrmal_https' ) !== null ? intval( $request->get_param( 'daexthrmal_https' ), 10 ) : null;
		$options['daexthrmal_auto_trailing_slash'] = $request->get_param( 'daexthrmal_auto_trailing_slash' ) !== null ? intval( $request->get_param( 'daexthrmal_auto_trailing_slash' ), 10 ) : null;

		$options['daexthrmal_auto_alternate_pages'] = $request->get_param( 'daexthrmal_auto_alternate_pages' ) !== null ? intval( $request->get_param( 'daexthrmal_auto_alternate_pages' ), 10 ) : null;

		$options['daexthrmal_auto_delete'] = $request->get_param( 'daexthrmal_auto_delete' ) !== null ? intval( $request->get_param( 'daexthrmal_auto_delete' ), 10 ) : null;
		$options['daexthrmal_show_log']    = $request->get_param( 'daexthrmal_show_log' ) !== null ? intval( $request->get_param( 'daexthrmal_show_log' ), 10 ) : null;

		// Defaults ---------------------------------------------------------------------------------------------------.
		for ( $i = 1; $i <= 10; $i++ ) {
			$options[ 'daexthrmal_default_language_' . $i ] = $request->get_param( 'daexthrmal_default_language_' . $i ) !== null ? sanitize_key( $request->get_param( 'daexthrmal_default_language_' . $i ) ) : null;
			$options[ 'daexthrmal_default_script_' . $i ]   = $request->get_param( 'daexthrmal_default_script_' . $i ) !== null ? sanitize_text_field( $request->get_param( 'daexthrmal_default_script_' . $i ) ) : null;
			$options[ 'daexthrmal_default_locale_' . $i ]   = $request->get_param( 'daexthrmal_default_locale_' . $i ) !== null ? sanitize_key( $request->get_param( 'daexthrmal_default_locale_' . $i ) ) : null;
		}

		// Update the options -----------------------------------------------------------------------------------------.
		foreach ( $options as $key => $option ) {
			if ( null !== $option ) {
				update_option( $key, $option );
			}
		}

		$response = new WP_REST_Response( 'Data successfully added.', '200' );

		return $response;
	}

	/**
	 * Check the user capability.
	 *
	 * @return true|WP_Error
	 */
	public function rest_api_daext_hreflang_manager_update_options_callback_permission_check() {

		if ( ! current_user_can( 'manage_options' ) ) {
			return new WP_Error(
				'rest_update_error',
				'Sorry, you are not allowed to update the Hreflang Manager options.',
				array( 'status' => 403 )
			);
		}

		return true;
	}
}
