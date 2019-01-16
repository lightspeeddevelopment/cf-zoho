<?php
/**
 * The file that defines admin settings.
 *
 * @package lsx_cf_zoho/admin.
 */

namespace lsx_cf_zoho\admin;

use lsx_cf_zoho;
use lsx_cf_zoho\includes;
use lsx_cf_zoho\includes\zohoapi;

/**
 * Settings API.
 */
class Settings {

	/**
	 * Options class.
	 *
	 * @var object.
	 */
	private $options;

	/**
	 * Tokens class.
	 *
	 * @var object.
	 */
	private $tokens;

	/**
	 * Register a CF Zoho Settings page.
	 */
	public function settings_page() {

		add_options_page(
			'CF Zoho Options',
			'CF Zoho',
			'manage_options',
			'cfzoho',
			[ $this, 'cfzoho_settings_page_html' ]
		);
	}

	/**
	 *  Settings page.
	 */
	public function cfzoho_settings_page_html() {

		// Check user capabilities.
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}

		$this->options = new includes\Options();
		$this->tokens  = new zohoapi\Tokens();

		// Test for transient flush.
		if ( true === (bool) $this->options->get_option( 'flush_transients' ) ) {
			$this->flush_transients();
		}

		// Show error/update messages.
		settings_errors( 'cfzoho_messages' );

		// Template.
		include_once LSX_CFZ_TEMPLATE_PATH . 'settings-form.php';
	}

	/**
	 * Inits the WP Settings API.
	 */
	public function settings_api_init() {

		// Test for redirect after tokens.
		if ( isset( $_GET['state'] ) ) {
			$this->request_token();
		}

		// Register app details.
		add_settings_section(
			'cfzoho_section_developers',
			__( 'Registering a Zoho app for use with the Caldera Forms Zoho plugin.', 'cfzoho' ),
			[ $this, 'cfzoho_settings_field_cb' ],
			'cfzoho'
		);

		// API Details.
		add_settings_section(
			'cfzoho_section_api_keys',
			__( 'API Settings.', 'cfzoho' ),
			[ $this, 'cfzoho_settings_field_cb' ],
			'cfzoho'
		);

		// Region.
		add_settings_field(
			'cfzoho_url',
			__( 'ZOHO Oauth URL', 'cfzoho' ),
			[ $this, 'cfzoho_settings_field_cb' ],
			'cfzoho',
			'cfzoho_section_api_keys',
			[
				'label_for'          => 'cfzoho_url',
				'class'              => 'cfzoho_row',
				'cfzoho_custom_data' => 'custom',
			]
		);

		// Client ID.
		add_settings_field(
			'cfzoho_client_id',
			__( 'ZOHO Client ID', 'cfzoho' ),
			[ $this, 'cfzoho_settings_field_cb' ],
			'cfzoho',
			'cfzoho_section_api_keys',
			[
				'label_for'          => 'cfzoho_client_id',
				'class'              => 'cfzoho_row',
				'cfzoho_custom_data' => 'custom',
			]
		);

		// Client Secret.
		add_settings_field(
			'cfzoho_client_secret',
			__( 'ZOHO Client Secret', 'cfzoho' ),
			[ $this, 'cfzoho_settings_field_cb' ],
			'cfzoho',
			'cfzoho_section_api_keys',
			[
				'label_for'          => 'cfzoho_client_secret',
				'class'              => 'cfzoho_row',
				'cfzoho_custom_data' => 'custom',
			]
		);

		// Tokens.
		add_settings_field(
			'cfzoho_tokens',
			__( 'Generate Tokens', 'cfzoho' ),
			[ $this, 'cfzoho_tokens_cb' ],
			'cfzoho',
			'cfzoho_section_api_keys',
			[
				'label_for'          => 'cfzoho_tokens',
				'class'              => 'cfzoho_row',
				'cfzoho_custom_data' => 'custom',
			]
		);

		// Flush transients.
		add_settings_section(
			'flush_transients',
			__( 'Flush Transients.', 'cfzoho' ),
			[ $this, 'cfzoho_settings_field_cb' ],
			'cfzoho'
		);
	}

	/**
	 * Section templates.
	 */
	private $templates = [
		'cfzoho_section_developers' => 'settings-section.php',
		'cfzoho_section_api_keys'   => 'settings-api.php',
		'cfzoho_url'                => 'settings-url.php',
		'cfzoho_client_id'          => 'settings-client-id.php',
		'cfzoho_client_secret'      => 'settings-client-secret.php',
		'flush_transients'          => 'settings-flush-transients.php',
	];

	/**
	 * Settings field callback.
	 *
	 * @param array $args Settings arguments.
	 */
	public function cfzoho_settings_field_cb( $args ) {

		$id       = isset( $args['id'] ) ? esc_attr( $args['id'] ) : esc_attr( $args['label_for'] );
		$name     = LSX_CFZ_OPTION_SLUG . '[' . $id . ']';
		$value    = $this->options->get_option( $id );
		$template = $this->templates[ $id ];

		include_once LSX_CFZ_TEMPLATE_PATH . $template;
	}

	/**
	 * Generate tokens callback.
	 *
	 * @param array $args Settings arguments.
	 */
	public function cfzoho_tokens_cb() {

		$url       = $this->options->get_option( 'cfzoho_url' ) . '/auth';
		$url_text  = false === $this->tokens->has_refresh_token() ? 'Generate ' : 'Re-generate ';
		$url_text .= 'Access and Refresh Tokens';

		/**
		 * NB You can set scope to ZohoCRM.modules.leads.CREATE,ZohoCRM.modules.contacts.CREATE,ZohoCRM.modules.tasks.CREATE,
		 * however the response to this does not appear to include a refresh token.
		 */
		$params = [
			'scope'         => 'ZohoCRM.settings.all,ZohoCRM.users.all,ZohoCRM.modules.all',
			'client_id'     => $this->options->get_option( 'cfzoho_client_id' ),
			'state'         => wp_create_nonce( 'zohotoken' ),
			'response_type' => 'code',
			'redirect_uri'  => cf_zoho_redirect_url(),
			'access_type'   => 'offline',
		];

		foreach ( $params as $key => $value ) {
			$url = add_query_arg( $key, $value, $url );
		}

		include_once LSX_CFZ_TEMPLATE_PATH . 'settings-tokens.php';
	}

	/**
	 * Called when a temporary oauth token has been generated.
	 */
	public function request_token() {

		$nonce = filter_input( INPUT_GET, 'state', FILTER_SANITIZE_STRING );

		if ( ! wp_verify_nonce( $nonce, 'zohotoken' ) ) {
			add_settings_error( 'cfzoho_messages', 'cfzoho_message', 'The token request is invalid.', 'error' );
			return;
		}

		$connect  = new zohoapi\Connect();
		$response = $connect->generate_token( 'authorization_code' );

		if ( true !== $response ) {
			add_settings_error( 'cfzoho_messages', 'cfzoho_message', $response, 'error' );
			return;
		}

		$url = menu_page_url( 'cfzoho', false );

		// Redirect back to settings page to prevent resubmission.
		header( "Location: {$url}" );
	}

	/**
	 * Flushes any stored module data.
	 */
	public function flush_transients() {

		$cache = new includes\Cache();
		$cache->flush_plugin_cache();

		$this->options->reset_cache_option();

		add_settings_error( 'cfzoho_messages', 'cfzoho_message', 'Plugin cache successfully flushed', 'updated' );
	}
}
