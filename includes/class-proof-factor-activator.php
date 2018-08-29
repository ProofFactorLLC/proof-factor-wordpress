<?php

/**
 * Fired during plugin activation
 *
 * @link       https://prooffactor.com
 * @since      1.0.0
 *
 * @package    Proof_Factor
 * @subpackage Proof_Factor/includes
 */

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      1.0.0
 * @package    Proof_Factor
 * @subpackage Proof_Factor/includes
 * @author     Proof Factor LLC <enea@prooffactor.com>
 */
class Proof_Factor_Activator {

	/**
	 * Short Description. (use period)
	 *
	 * Long Description.
	 *
	 * @since    1.0.0
	 */
	public static function activate() {
	    $plugin_name = 'proof-factor';
	    if (get_option($plugin_name) == false) {
            $payload = [
                'url' => home_url(),
                'email' => get_option('admin_email'),
                'store_name' => get_option('blogname')
            ];
            $new_user = Proof_Factor_Activator::remote_json_post("https://api.prooffactor.com/v1/partners/woo_commerce/connect", $payload);
            if (!isset($new_user["error"])) {
                update_option($plugin_name, array( "account_id" => $new_user['account_id'], "user_id" => $new_user['user_id'] ));
            }
        }
	}

	public static function remote_json_post($url, $params) {
		$args = array( 'headers' => array( 'Content-Type' => 'application/json' ), 
                      'body' => json_encode($params), 
                      'timeout' => 30 );
		$response = wp_remote_post($url, $args);

		if (is_wp_error($response)) {
            if ($response->get_error_message() == 'cURL error 35: SSL connect error') {
                try {
                    return json_decode(file_get_contents($url), true);
                } catch (Exception $e) {
                    echo '<p class="warning">';
                    _e('Error accrued while contacting our server!');
                    echo '<br />';
                    _e('Error data: ');
                    echo $e->getMessage();
                    echo '</p>';
                }
            } else {
                echo '<p class="warning">';
                _e('Error accrued while contacting our server!');
                echo '<br />';
                _e('Error data: ');
                echo $response->get_error_data();
                echo '<br />';
                _e('Error message: ');
                echo $response->get_error_message();
                echo '<br />';
                _e('Error code: ');
                echo $response->get_error_code();
                echo "</p>";
            }
            // error, CURL not installed, firewall blocked or Fomo server down
        } else {
            if ($response != null && isset($response['body'])) {
                return json_decode($response['body'], true);
            }
        }
	}
}
