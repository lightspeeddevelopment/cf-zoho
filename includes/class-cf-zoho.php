<?php
/**
 * The file that defines the core plugin class.
 *
 * @package lsx_cf_zoho/includes.
 */

namespace lsx_cf_zoho\includes;

use lsx_cf_zoho\admin;

/**
 * Main CF_Zoho Class.
 */
final class CF_Zoho {


	/**
	 * Holds instance of the class
	 */
	private static $instance;

	/**
	 * Holds the Fields class
	 *
	 * @var \lsx_cf_zoho\admin\Settings()
	 */
	var $settings;

	/**
	 * Holds the Fields class
	 *
	 * @var \lsx_cf_zoho\includes\Field()
	 */
	var $field;

	/**
	 * Holds the Templates class
	 *
	 * @var \lsx_cf_zoho\includes\Templates()
	 */
	var $templates;

	/**
	 * Holds the Pre Populate class
	 *
	 * @var \lsx_cf_zoho\includes\Pre_Populate()
	 */
	var $pre_populate;

	/**
	 * Holds the Error Logging class
	 *
	 * @var \lsx_cf_zoho\includes\WP_Logging()
	 */
	var $logging;

	/**
	 * Return an instance of this class.
	 *
	 * @return object
	 */
	public static function init() {
		// If the single instance hasn't been set, set it now.
		if ( ! isset( self::$instance ) ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 * setup the plugin.
	 */
	public function setup() {
		// Admin Settings.
		$this->settings = new admin\Settings();

		add_action( 'admin_menu', array( $this->settings, 'settings_page' ) );
		add_action( 'admin_init', array( $this->settings, 'settings_api_init' ) );

		// Caldera Forms Processors.
		$cf_processors = new CF_Processors();
		add_filter( 'caldera_forms_get_form_processors', array( $cf_processors, 'register_processors' ) );

		// WP Logs.
		if ( true === (bool) $this->settings->options->get_option( 'lsx_cf_zoho_enable_debug' ) ) {
			$this->logging = new WP_Logging();
			add_action( 'wp_ajax_cf_zoho_error_log', 'cf_zoho_ajax_log_js_error' );
			add_action( 'wp_ajax_nopriv_cf_zoho_error_log', 'cf_zoho_ajax_log_js_error' );
		}

		// WP Logs template.
		$this->templates = Templates::init();
		add_filter( 'template_include', array( $this->templates, 'template_handler' ), 99 );

		// Register the new field
		$this->field = Field::init();
		add_action( 'init', array( $this->field, 'setup' ) );

		$this->pre_populate = Pre_Populate::init();
		add_filter( 'caldera_forms_render_pre_get_entry', array( $this->pre_populate, 'pre_populate_form' ), 10, 2 );

		add_action( 'wp_enqueue_scripts', array( $this, 'scripts' ), 5 );

		add_filter(
			'caldera_forms_ajax_return',
			array(
				$this,
				'filter_ajax_return',
			),
			10,
			2
		);
	}

	/**
	 * Enqueues the parent and the child theme styles.
	 *
	 * @package    giltedgeafrica-lsx-child
	 * @subpackage setup
	 */
	public function scripts() {
		if ( true === (bool) $this->settings->options->get_option( 'lsx_cf_zoho_enable_form_blocker' ) ) {
			wp_enqueue_script( 'blockUI', LSX_CFZ_URL . '/assets/js/jquery.blockUI.js', array( 'jquery' ), LSX_CFZ_VERSION, true );
			wp_enqueue_script( 'lsx-cf-zoho-js', LSX_CFZ_URL . '/assets/js/lsx-cf-zoho.js', array( 'blockUI' ), LSX_CFZ_VERSION, true );
			$zoho_args =
			array(
				'blockMessage'   => __( 'Please wait while we capture your details', 'lsx-cf-zoho' ),
				'headerSelector' => '.header-wrap',
				'ajaxurl' => admin_url( 'admin-ajax.php' ),
			);
			$zoho_args = apply_filters( 'lsx_cf_zoho_js_args', $zoho_args );
			wp_localize_script(
				'lsx-cf-zoho-js',
				'lsxCfZohoArgs',
				$zoho_args
			);
		}
	}

	/**
	 * Filter the ajax return and maybe add our output
	 *
	 * @param $out
	 * @param $form
	 *
	 * @return mixed
	 */
	public function filter_ajax_return( $out, $form ) {
		if ( true === (bool) $this->settings->options->get_option( 'lsx_cf_zoho_enable_form_blocker' ) ) {
			$out['html'] .= '<script>lsx_cf_zoho.unblockForms();</script>';
		}
		return $out;
	}
}
