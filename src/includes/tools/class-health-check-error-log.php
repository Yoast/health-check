<?php

class Health_Check_Error_Log extends Health_Check_Tool {

	public function __construct() {
		$this->label       = __( 'JavaScript error log', 'health-check' );
		$this->description = __(  'In the table below you can see every JavaScript error that WordPress has logged on your site. You can use this information to understand which plugins are causing errors and to help plugin authors more quickly debug a JavaScript problem once one occurs.' , 'health-check' );

		parent::__construct();
	}

	public function tab_content() {
		?>
        <table class="wp-list-table widefat fixed striped" id="health-check-errors">
            <thead>
            <tr>
                <th class="health-check-error-message"><?php _e( 'Message', 'health-check' ); ?></th>
                <th class="health-check-error-url"><?php _e( 'Origin', 'health-check' ); ?></th>
                <th class="health-check-error-line"><?php _e( 'Line', 'health-check' ); ?></th>
                <th class="health-check-error-column"><?php _e( 'Column', 'health-check' ); ?></th>
                <th class="health-check-error-screen"><?php _e( 'Screen', 'health-check' ); ?></th>
            </tr>
            </thead>
            <tbody>
                <?php
                $errors = get_option( 'health-check-js-errors', array() );

                foreach ( $errors as $error ) {
                    ?>
                    <tr>
                        <td class="health-check-error-message"><strong><?php echo $error['message']; ?></strong></td>
                        <td class="health-check-error-url"><?php echo $error['url']; ?></td>
                        <td class="health-check-error-line"><?php echo $error['line']; ?></td>
                        <td class="health-check-error-column"><?php echo $error['column']; ?></td>
                        <td class="health-check-error-screen"><?php echo $error['screen']; ?></td>
                    </tr>
                    <?php
                }
                ?>
            </tbody>
        </table>
		<?php
	}
}

new Health_Check_Error_Log();
