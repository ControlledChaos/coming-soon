<?php
/**
 * Admin Options Page
 *
 * @package     Coming_Soon
 * @subpackage  Admin/Settings
 * @copyright   Copyright (c) 2013, Pippin Williamson
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       1.0.0
*/

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Options Page
 *
 * Renders the options page contents.
 *
 * @since 1.0.0
 * @global $seedcs_options Array of all the EDD Options
 * @return void
 */
function seedcs_options_page() {
	global $seedcs_options;

	$active_tab = isset( $_GET[ 'tab' ] ) && array_key_exists( $_GET['tab'], seedcs_get_settings_tabs() ) ? $_GET[ 'tab' ] : 'general';

	ob_start();
	?>
	<div class="wrap seedcs">
		<h2>Coming Soon<!-- <span class="seedcs-version"> <?php echo SeedCS::VERSION; ?></span> --></h2>
		<h2 class="nav-tab-wrapper">
			<?php
			foreach( seedcs_get_settings_tabs() as $tab_id => $tab_name ) {

				$tab_url = add_query_arg( array(
					'settings-updated' => false,
					'tab' => $tab_id
				) );

				$active = $active_tab == $tab_id ? ' nav-tab-active' : '';

				echo '<a href="' . esc_url( $tab_url ) . '" title="' . esc_attr( $tab_name ) . '" class="nav-tab' . $active . '">';
					echo esc_html( $tab_name );
				echo '</a>';
			}
			?>
		</h2>
		<div id="tab_container">
			<form method="post" action="options.php">
				<table class="form-table">
				<?php
				settings_fields( 'seedcs_settings' );
				do_settings_fields( 'seedcs_settings_' . $active_tab, 'seedcs_settings_' . $active_tab );
				?>
				</table>
				<?php submit_button(); ?>
			</form>
		</div><!-- #tab_container-->
	</div><!-- .wrap -->
	<?php
	echo ob_get_clean();
}


function seedcs_do_settings_fields($page, $section) {
      global $wp_settings_fields;
  
      if ( !isset($wp_settings_fields) || !isset($wp_settings_fields[$page]) || !isset($wp_settings_fields[$page][$section]) )
          return;
  
      foreach ( (array) $wp_settings_fields[$page][$section] as $field ) {
          echo '<tr valign="top">';
          if ( !empty($field['args']['label_for']) )
              echo '<th scope="row"><label for="' . $field['args']['label_for'] . '">' . $field['title'] . '</label></th>';
          else
              echo '<th scope="row"><strong>' . $field['title'] . '</strong><!--<br>'.$field['args']['desc'].'--></th>';
          echo '<td>';
          call_user_func($field['callback'], $field['args']);
          echo '</td>';
          echo '</tr>';
    }
}
