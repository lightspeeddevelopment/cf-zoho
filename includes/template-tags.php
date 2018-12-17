<?php
/**
 * Template Tags
 *
 * @package   CF Zoho
 * @author    LightSpeed
 * @license   GPL3
 * @link
 * @copyright 2018 LightSpeed
 */

/**
 * CF Zoho Options page URL.
 * Used to populate redirect_uri field in Zoho requests.
 * Can't use menu_page_url in Zoho requests so built this instead.
 *
 * @return string CF Zoho Options page URL.
 */
function cf_zoho_redirect_url() {
	return admin_url( add_query_arg( 'page', 'cfzoho', 'options-general.php' ) );
}

/**
 * Returns the names and ids of the current available caldera forms.
 * @return bool
 */
function cf_zoho_get_caldera_forms() {
	$results = \Caldera_Forms_Forms::get_forms( true );
	$forms   = false;

	if ( ! empty( $results ) ) {
		foreach ( $results as $form => $form_data ) {
			$forms[ $form ] = $form_data['name'];
		}
	}
	return $forms;
}

/**
 * Registers a caldera form to output as a modal in the footer
 * @param $caldera_id string
 * @param $field_id string
 * @param $limit int
 */
function cf_zoho_register_modal( $caldera_id = '', $field_id = '', $limit = 1 ) {
	if ( '' !== $caldera_id && '' !== $field_id ) {
		$cf_zoho = cf_zoho\includes\CF_Zoho::init();
		$cf_zoho->field->add_modal( $caldera_id, $field_id, $limit );
	}
}


function cf_zoho_get_form_title( $caldera_id = '' ) {
	$title = '';
	if ( '' !== $caldera_id ) {
		$form = Caldera_Forms_Forms::get_form( $caldera_id );
		if ( isset( $form['name'] ) ) {
			$title = $form['name'];
		}
	}
	return $title;
}