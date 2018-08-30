<?php

/**
 * Provide a admin area view for the plugin
 *
 * This file is used to markup the admin-facing aspects of the plugin.
 *
 * @link       https://prooffactor.com
 * @since      1.0.0
 *
 * @package    Proof_Factor
 * @subpackage Proof_Factor/admin/partials
 */

$options = get_option($this->plugin_name);
$proof_account_id = $options['account_id'];
$proof_user_id = $options['user_id'];

$should_show_link = true;
$proof_account_exists = false;

if ($proof_user_id) {
    try {
        $request = wp_remote_get("https://api.prooffactor.com/v1/partners/woo_commerce/validate?account_id=" . $proof_account_id . "&user_id=" . $proof_user_id);
        $response_code = wp_remote_retrieve_response_code($request);
        if (!is_wp_error($request) && $response_code == 200) {
            $body = wp_remote_retrieve_body($request);
            if (!is_wp_error($body)) {
                $data = json_decode($body, true);
                if (isset($data) && !empty($data) && !is_null($data)) {
                    if (array_key_exists('valid', $data)) {
                        $should_show_link = ($data['valid'] == false);
                    }
                    if (array_key_exists('exists', $data)) {
                        $proof_account_exists = $data['exists'];
                    }
                }
            }
        }
    } catch (Exception $e) {
    }
}
?>

<!-- This file should primarily consist of HTML with a little bit of PHP. -->
<div class="wrap">
    <h2><?php echo esc_html(get_admin_page_title()); ?></h2>
    <hr>
    <?php if ($proof_account_exists == false) { ?>
        <div class="alert alert-danger">
            Proof Account Credentials Invalid. Please Re-Enter!
        </div>
    <?php } else { ?>
        <p>&#9432; A Proof Factor account was created with the email <b> <?= get_option('admin_email') ?> </b>> when you
            activated this plugin. Please check your email for a password reset link to set a new password and login to
            your Proof Factor dashboard.</p>
    <?php } ?>

    <form method="post" name="proof_plugin_options" action="options.php">
        <?php
        settings_fields($this->plugin_name);
        do_settings_sections($this->plugin_name);
        ?>
        <div class="form-group">
            <label for="proof-factor[account_id]">Account Id:</label>
            <input class="proof-input-account" type="text" id="proof_account_id" value="<?= $options['account_id'] ?>"
                   name="<?php echo $this->plugin_name; ?>[account_id]">
        </div>
        <div class="form-group">
            <label for="proof-factor[user_id]">User Id:</label>
            <input class="proof-input-account" type="text" id="proof_user_id" value="<?= $options['user_id'] ?>"
                   name="<?php echo $this->plugin_name; ?>[user_id]">
        </div>
        <div class="form-group">
            <input type="submit" name="submit" id="submit" class="pf-button pf-button-sm pf-button-secondary"
                   value="Update Proof Factor Credentials">
        </div>
        <?php
        if ($should_show_link) {
            ?>
            <p>&#9432; You can find your <b>Account Id</b> and <b>User Id</b> from the Account Details section of the
                Proof Factor Settings Page: <a href="https://app.prooffactor.com/settings" target=”_blank”>https://app.prooffactor.com/settings</a>
            </p>
            <?php
        }
        ?>
    </form>

    <?php
    if ($should_show_link && $proof_account_exists) {
        $store_url = home_url();
        $endpoint = '/wc-auth/v1/authorize';
        $params = [
            'app_name' => 'ProofFactor',
            'scope' => 'read_write',
            'user_id' => $options['user_id'],
            'return_url' => admin_url('options-general.php?page=' . $this->plugin_name),
            'callback_url' => 'https://api.prooffactor.com/v1/partners/woo_commerce/oauth'
        ];
        $query_string = http_build_query($params);
        $auth_url = $store_url . $endpoint . '?' . $query_string;
        ?>
        <hr>
        <h2>It looks like Your WooCommerce Account is not yet linked to Proof Factor!</h2>
        <p>In order to capture purchases from WooCommerce you will need to link WooCommerce with your Proof Factor
            Account!</p>
        <a class="pf-button pf-button-primary" href="<?= $auth_url ?>">Link WooCommerce!</a>
        <?php
    } else if ($should_show_link == false && $proof_account_exists) {
        ?>
        <p>&#9432; You can configure additional notifications options at <a href="https://app.prooffactor.com"
                                                                            target=”_blank”>https://app.prooffactor.com</a>
        </p>
        <?php
    }
    ?>
</div>