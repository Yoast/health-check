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
    /**
     * Logs JS error to an option.
     */
    static function log_error() {
        echo "yolo";
        check_ajax_referer( 'health-check-error-logger' );

        if ( ! current_user_can( 'edit_posts' ) ) {
            wp_send_json_error();
        }

        $error = json_decode( filter_input( INPUT_POST, 'error' ), true );
        $option = self::get_option();

        $error_hash = sha1( $error['message'] . '.' . $error['url'] . '.' . $error['screen'] );

        $option[ $error_hash ] = $error;
        update_option( 'health-check-js-errors', $option, false );

        wp_send_json( true );

        die();
    }

    /**
     *
     */
    static function get_option() {
        $option = get_option( 'health-check-js-errors', array() );

        $one_month_ago = time() - MONTH_IN_SECONDS;
        $option = array_filter( $option, function ( $error ) use ( $one_month_ago ) {
            return $one_month_ago < $error['timestamp'];
        } );

        return $option;
    }
}
