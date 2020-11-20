<?php
/**
 * Plugin Name: IQCalc - COVID-19 Isolation and Quarantine Calculator
 * Description: Generates estimate isolation and quarantine periods based on available best practices.
 * Version:     1.3
 * Author:      John C
 * License:     GPLv2 or later.
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: iqcalc
 * General Disclaimer: Use at your own risk/reward. Provided with no warranty. 
 * Special Disclaimer: Not medical advice - please consult your primary care physician for recommendations. Calculations generated using best practices current as of time of publishing.
*/

// Admin page to set a custom affiliate disclosure and disclaimer
add_action( 'admin_menu', 'iqcalc_add_admin_menu' );
add_action( 'admin_init', 'iqcalc_settings_init' );

	// Admin page settings
	function iqcalc_add_admin_menu(  ) { 
		add_menu_page( 'IQCalc', 'IQCalc', 'manage_options', 'iqcalc', 'iqcalc_options_page', 'dashicons-calendar-alt');
	}

	// Admin page section descriptions
	function iqcalc_settings_init(  ) { 
		register_setting( 'pluginPage', 'iqcalc_settings' );
		add_settings_section(
			'iqcalc_pluginPage_section', 
			__( 'Change the IQCalc message.', 'iqcalc' ), 
			'iqcalc_settings_section_callback', 
			'pluginPage'
		);
		add_settings_field( 
			'iqcalc_custom_message', 
			__( 'IQCalc supports basic HTML.', 'iqcalc' ), 
			'iqcalc_custom_message_render', 
			'pluginPage', 
			'iqcalc_pluginPage_section' 
		);
	}
	
	// Admin page custom textarea
	function iqcalc_custom_message_render(  ) { 
		$iqcalc_options = get_option( 'iqcalc_settings' );
		?>
		<p>
			<textarea cols='50' rows='10' name='iqcalc_settings[iqcalc_custom_message]'><?php echo $iqcalc_options['iqcalc_custom_message']; ?></textarea>
		</p>
		<?php
	}

	// Admin page render
	function iqcalc_options_page(  ) { 
			?>
			<form action='options.php' method='post'>
				<h2>IQCalc - Custom Message</h2>
				<?php
				settings_fields( 'pluginPage' );
				do_settings_sections( 'pluginPage' );
				submit_button();
				?>
			</form>
			<?php
	}
	
function iqcalc_function() {
	$iqcalc_date = $_GET['date'];
	$iqcalc_options = get_option( 'iqcalc_settings' );
	$iqcalc_result = "<h4>Please use the date your symptoms began, the date of your test (if no symptoms), or the date your were last in contact with a COVID-19 positive individual (if identified as a close contact by a contact tracer).</h4>";
	$iqcalc_result .= '<form action="index.php" method="get"><input type="date" name="date"><br /><input type="submit"></form>';
	if( isset( $iqcalc_date ) ) { 
		$iqcalc_result .= "<p>If you have tested positive and you had symptoms (assuming your symptoms have improved and you've gone 24+ hours without fever or medication) or you have no symptoms:</p>";
		$iqcalc_result .= "<p><strong>Your last full day of isolation is " . date('F jS, Y', strtotime($iqcalc_date . ' + 10 days')) . ".</strong></p>";
		$iqcalc_result .= "<p>If you have been identified as someone who is a close contact of a COVID-19 case, and you do not have any symptoms:</p>";
		$iqcalc_result .= "<p><strong>Your last full day of quarantine is " . date('F jS, Y', strtotime($iqcalc_date . ' + 14 days')) . ".</strong></p>";
		$iqcalc_result .= "<p>" . $iqcalc_options['iqcalc_custom_message'] . "</p>";
		$iqcalc_result .= "<p><em>Not medical advice - please consult your primary care physician for recommendations. Calculations generated using best practices current as of time of publishing.</em></p>";
	}
	return $iqcalc_result;
}

add_shortcode('iqcalc', 'iqcalc_function');

?>
