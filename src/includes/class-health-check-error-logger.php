<?php
/**
 * Logs JavaScript Errors
 *
 * @package Health Check
 */

// Make sure the file is not directly accessible.
if ( ! defined( 'ABSPATH' ) ) {
    die( 'We\'re sorry, but you can not directly access this file.' );
}

/**
 * Class Health_Check_Error_Logger
 */
class Health_Check_Error_Logger {
    static function log_error() {
        check_ajax_referer( 'health-check-error-logger' );

        if ( ! current_user_can( 'edit-posts' ) ) {
            wp_send_json_error();
        }

        $error = json_decode( filter_input( INPUT_POST, 'error' ), true );
        $option = get_option( 'health-check-js-errors', array() );

        $error_hash = sha1( $error['message'] . '.' . $error['url'] . '.' . $error['screen'] );

        if ( ! array_key_exists( $error_hash, $option ) ) {
            $option[ $error_hash ] = $error;
            update_option( 'health-check-js-errors', $option );
        }

        wp_send_json( true );

        die();
    }
}
