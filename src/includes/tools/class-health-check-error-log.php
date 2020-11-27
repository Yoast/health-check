<?php

class Health_Check_Error_Log extends Health_Check_Tool {

    private $parsed_content_url;

    private $parsed_plugins_url;

    private $parsed_includes_url;

    private $parsed_admin_url;

	public function __construct() {
		$this->label       = __( 'JavaScript error log', 'health-check' );
		$this->description = sprintf(
            __(  'In the table below you can see every JavaScript error that WordPress has logged in your admin in the %1$slast 30 days%2$s. You can use this information to understand which plugins are causing errors and to help plugin authors more quickly debug a JavaScript problem once one occurs.' , 'health-check' ),
            '<strong>',
            '</strong>',
        );

		$this->parsed_content_url  = wp_parse_url( content_url() );
		$this->parsed_plugins_url  = wp_parse_url( plugins_url() );
		$this->parsed_includes_url = wp_parse_url( includes_url() );
		$this->parsed_admin_url    = wp_parse_url( admin_url() );
		$this->parsed_site_url     = wp_parse_url( site_url() );

		parent::__construct();
	}

	public function tab_content() {
        $errors = Health_Check_Error_Logger::get_option();
        if ( count( $errors ) === 0 ) {
            ?>
                <p><em><?php _e( 'No errors logged.', 'health-check' ) ?></em></p>
            <?php
            return;
        }
		?>
        <table class="wp-list-table widefat fixed striped" id="health-check-errors">
            <thead>
            <tr>
                <th class="health-check-error-message"><?php _e( 'Message', 'health-check' ); ?></th>
                <th class="health-check-error-origin"><?php _e( 'Origin', 'health-check' ); ?></th>
                <th class="health-check-error-url"><?php _e( 'File', 'health-check' ); ?></th>
                <th class="health-check-error-screen"><?php _e( 'Screen', 'health-check' ); ?></th>
            </tr>
            </thead>
            <tbody>
                <?php

                foreach ( $errors as $error ) {
                    $origin = $this->get_origin( $error['url'] );
                    ?>
                    <tr>
                        <td class="health-check-error-message"><strong><?php echo $error['message']; ?></strong></td>
                        <td class="health-check-error-origin"><?php echo $origin; ?></td>
                        <td class="health-check-error-url"><?php echo $error['url']; ?></td>
                        <td class="health-check-error-screen"><code><?php echo $error['screen']; ?></code></td>
                    </tr>
                    <?php
                }
                ?>
            </tbody>
        </table>
		<?php
	}

    /**
     * Determines the origin of an asset url (Plugin|Theme|WordPress|Unknown).
     *
     * @param string $url the url of the asset for which to determine the origin.
     *
     * @return string The origin of the file.
     */
	private function get_origin( $url ) {
        $src_url     = wp_parse_url( $url );

        // If the host is the same or it's a relative URL.
        if (
            ( ! isset( $this->parsed_plugins_url['path'] ) || strpos( $src_url['path'], $this->parsed_plugins_url['path'] ) === 0 ) &&
            ( ! isset( $src_url['host'] ) || $src_url['host'] === $this->parsed_plugins_url['host'] )
        ) {
            // Make the src relative the specific plugin.
            if ( isset( $this->parsed_plugins_url['path'] ) ) {
                $relative = substr( $src_url['path'], strlen( $this->parsed_plugins_url['path'] ) );
            } else {
                $relative = $src_url['path'];
            }
            $relative = trim( $relative, '/' );
            $relative = explode( '/', $relative );

            if ( count( $relative ) < 1 ) {
                return "Unknown";
            }
            return sprintf( __('Plugin: %s', 'health-check'), '<code>' . $relative[0] . '</code>' );
        } elseif (
            ( ! isset( $this->parsed_content_url['path'] ) || strpos( $src_url['path'], $this->parsed_content_url['path'] ) === 0 ) &&
            ( ! isset( $src_url['host'] ) || $src_url['host'] === $this->parsed_content_url['host'] )
        ) {
            // Make the src relative the specific plugin or theme.
            if ( isset( $this->parsed_content_url['path'] ) ) {
                $relative = substr( $src_url['path'], strlen( $this->parsed_content_url['path'] ) );
            } else {
                $relative = $src_url['path'];
            }
            $relative = trim( $relative, '/' );
            $relative = explode( '/', $relative );

            if ( count( $relative ) < 2 ) {
                return "Unknown";
            }
            if ( $relative[0] === 'plugins' ) {
                return "Plugin: " . $relative[1];
            } elseif ( $relative[0] === 'mu-plugins' ) {
                return "Must-use Plugin: " . $relative[1];
            } elseif ( $relative[0] === 'themes' ) {
                return "Theme: " . $relative[1];
            }
            return "Unknown";
        } elseif (
            ( ! isset( $this->parsed_includes_url['path'] ) || strpos( $src_url['path'], $this->parsed_includes_url['path'] ) === 0 ) &&
            ( ! isset( $src_url['host'] ) || $src_url['host'] === $this->parsed_includes_url['host'] )
        ) {
            return "WordPress";
        } elseif (
            ( ! isset( $this->parsed_admin_url['path'] ) || strpos( $src_url['path'], $this->parsed_admin_url['path'] ) === 0 ) &&
            ( ! isset( $src_url['host'] ) || $src_url['host'] === $this->parsed_admin_url['host'] )
        ) {
            return "WordPress";
        }
        return "Unknown";
    }
}

new Health_Check_Error_Log();
