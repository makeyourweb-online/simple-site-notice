<?php
/**
 * Plugin Name: Simple Site Notice
 * Description: Display a customizable notice banner on your WordPress site. Supports HTML, custom colors, and sticky (fixed) option.
 * Version: 1.1.1
 * Author: MakeYourWeb
 * Author URI: https://plugins.makeyourweb.online/
 * License: GPL v2 or later
 */

if (!defined('ABSPATH')) {
    exit;
}

// Register settings
function ssn_register_settings()
{
    add_option('ssn_enale', 1);
    add_option('ssn_notice_text', 'This is your site notice!');
    add_option('ssn_background_color', '#fffbcc');
    add_option('ssn_text_color', '#333333');
    add_option('ssn_font_size', '16px');
    add_option('ssn_fixed', 0);
    add_option('ssn_notice_position', 'footer'); // Default to footer

    $args = array(
        'type' => 'string',
        'sanitize_callback' => 'sanitize_text_field',
        'default' => NULL,
    );

    register_setting('ssn_options_group', 'ssn_enable', $args);
    register_setting('ssn_options_group', 'ssn_notice_text', $args);
    register_setting('ssn_options_group', 'ssn_background_color', $args);
    register_setting('ssn_options_group', 'ssn_text_color', $args);
    register_setting('ssn_options_group', 'ssn_fixed', $args);
    register_setting('ssn_options_group', 'ssn_notice_position', $args); // Register the position option

    // Add JS
    add_action('admin_enqueue_scripts', function ($hook) {
        if ($hook !== 'settings_page_simple-site-notice')
            return;

        wp_enqueue_script(
            'ssn-admin-script',
            plugin_dir_url(__FILE__) . 'notice-script.js',
            array('jquery'),
            '1.0',
            true
        );

        wp_localize_script('ssn-admin-script', 'ssn_ajax_object', [
            'ajax_url' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('ssn_ajax_nonce'),
        ]);
    });

    // Validate license
    add_action('wp_ajax_ssn_validate_license', 'ssn_validate_license_ajax');
    add_action('wp_ajax_nopriv_ssn_validate_license', 'ssn_validate_license_ajax');
}
add_action('admin_init', 'ssn_register_settings');

// Add settings page
function ssn_register_options_page()
{
    add_options_page('Simple Site Notice', 'Simple Site Notice', 'manage_options', 'simple-site-notice', 'ssn_options_page');
}
add_action('admin_menu', 'ssn_register_options_page');

// Settings page content
function ssn_options_page()
{
    ?>
    <div class="wrap">
        <h1>Simple Site Notice Settings</h1>
        <form method="post" action="options.php">
            <?php settings_fields('ssn_options_group'); ?>
            <table class="form-table">
                <tr valign="top">
                    <th scope="row">Enable notice</th>
                    <td><input type="checkbox" name="ssn_enable" value="1" <?php checked(1, get_option('ssn_enable'), true); ?> /> Show notification on page</td>
                </tr>
                <tr valign="top">
                    <th scope="row">Notice Text (HTML allowed)</th>
                    <td><textarea name="ssn_notice_text" rows="5"
                            cols="50"><?php echo esc_textarea(get_option('ssn_notice_text')); ?></textarea></td>
                </tr>
                <tr valign="top">
                    <th scope="row">Background Color</th>
                    <td><input type="color" name="ssn_background_color"
                            value="<?php echo esc_attr(get_option('ssn_background_color')); ?>" /></td>
                </tr>
                <tr valign="top">
                    <th scope="row">Text Color</th>
                    <td><input type="color" name="ssn_text_color"
                            value="<?php echo esc_attr(get_option('ssn_text_color')); ?>" /></td>
                </tr>
                <tr valign="top">
                    <th scope="row">Font size <?php if (!get_option('ssn_license_valid')): ?>🔒 <i>(PRO
                                Version)</i><?php endif; ?></th>
                    <td><input type="text" name="ssn_font_size" value="<?php echo esc_attr(get_option('ssn_font_size')); ?>"
                            <?php if (!get_option('ssn_license_valid')): ?>disabled<?php endif; ?> /></td>
                </tr>
                <tr valign="top">
                    <th scope="row">Fixed Position?</th>
                    <td><input type="checkbox" name="ssn_fixed" value="1" <?php checked(1, get_option('ssn_fixed'), true); ?> /> Stick to top of the screen</td>
                </tr>
                <tr valign="top">
                    <th scope="row">Notice Position</th>
                    <td>
                        <select name="ssn_notice_position">
                            <option value="header" <?php selected('header', get_option('ssn_notice_position')); ?>>Header
                            </option>
                            <option value="footer" <?php selected('footer', get_option('ssn_notice_position')); ?>>Footer
                            </option>
                        </select>
                    </td>
                </tr>
            </table>
            <?php submit_button(); ?>
        </form>
        <hr>
        <h2>License Activation</h2>
        <p>
            <a href="https://plugins.makeyourweb.online/product/simple-site-notice-pro/" target="_blank"
                style="font-weight:600;color:red;font-size:18px;">GO PRO Version - only 4,99 $</a>
        </p>
        <form method="post" action="options.php" id="license_key_form">
            <table class="form-table">
                <tr valign="top">
                    <th scope="row">License key</th>
                    <td>
                        <input type="text" name="ssn_license_key"
                            value="<?php echo esc_attr(get_option('ssn_license_key')); ?>"
                            placeholder="eg. XDE6-1Z2Q-1E24-1ZE1-CXE3-Q124" />
                        <span class="spinner" style="float: none; visibility: hidden;"></span>
                    </td>
                </tr>
            </table>
            <?php submit_button('Activate License'); ?>
        </form>
        <hr>
        <p>Support this plugin: <a href="https://buymeacoffee.com/makeyourweb" target="_blank">Buy me a coffee</a> ☕ | <a
                href="https://github.com/sponsors/makeyourweb" target="_blank">Sponsor on GitHub</a> ❤️</p>
    </div>
    <?php
}

// Display the notice
function ssn_display_notice()
{
    $enable = get_option('ssn_enable');

    if ($enable) {
        $notice_text = get_option('ssn_notice_text');
        $background_color = get_option('ssn_background_color');
        $text_color = get_option('ssn_text_color');
        $position = get_option('ssn_notice_position', 'footer'); // Default to footer

        if ($position === 'header') {
            $fixed = get_option('ssn_fixed') ? 'position: fixed; top: 0; left: 0; width: 100%; z-index: 9999;' : '';
        } elseif ($position === 'footer') {
            $fixed = get_option('ssn_fixed') ? 'position: fixed; bottom: 0; left: 0; width: 100%; z-index: 9999;' : '';
        }

        // Style for the notice
        $style = 'background-color: ' . esc_attr($background_color) . '; color: ' . esc_attr($text_color) . '; padding: 10px; text-align: center;' . $fixed;

        $allowed_html = array(
            'a' => array(
                'href' => array(),
                'title' => array(),
                '_blank' => array(),
            ),
            'span' => array(),
        );

        // Display the notice in the chosen position
        if ($position === 'header') {
            // Only add the notice in the header
            echo '<div style="' . esc_attr($style) . '">' . wp_kses($notice_text, $allowed_html) . '</div>';
        } elseif ($position === 'footer') {
            // Only add the notice in the footer
            echo '<div style="' . esc_attr($style) . '">' . wp_kses($notice_text, $allowed_html) . '</div>';
        }
    } else {
        return;
    }
}

// Validate license
function ssn_validate_license_ajax()
{
    if (
        !isset($_POST['_ajax_nonce']) ||
        !wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['_ajax_nonce'])), 'ssn_ajax_nonce')
    ) {
        wp_send_json_error('Security check failed.');
    }

    if (!isset($_POST['license_key'])) {
        wp_send_json_error(array('data' => 'No license key provided.'));
    }

    $license_key = sanitize_text_field(wp_unslash($_POST['license_key']));

    // Basic Authentication credentials
    $username = 'ck_ab9f1b4674b762538c140a11ec163dc09910cb13';
    $password = 'cs_d2ca0fabcdae1b705c3e70530cdb49da2807dadd';

    // API endpoint URL
    $url = "https://plugins.makeyourweb.online/wp-json/lmfwc/v2/licenses/validate/" . urlencode($license_key);

    // Perform the request
    $response = wp_remote_get($url, array(
        'headers' => array(
            'Authorization' => 'Basic ' . base64_encode($username . ':' . $password),
        ),
    ));

    // Check for errors
    if (is_wp_error($response)) {
        wp_send_json_error(array('data' => 'License validation failed.'));
    }

    $body = wp_remote_retrieve_body($response);
    $data = json_decode($body, true);

    $is_license_valid = false;
    if (!empty($data['success']) && $data['success'] === true) {
        $is_license_valid = true;
        update_option('ssn_license_valid', $is_license_valid);
        update_option('ssn_license_key', $license_key);

        wp_send_json_success(array('data' => '<div class="notice notice-success"><p>License is valid.</p></div>'));
    } else {
        update_option('ssn_license_valid', $is_license_valid);
        update_option('ssn_license_key', $license_key);

        wp_send_json_error(array('data' => '<div class="notice notice-error"><p>Invalid license key.</p></div>'));
    }
}

// Only load the notice in header or footer based on the selected option
function ssn_add_notice_to_correct_position()
{
    $position = get_option('ssn_notice_position', 'footer'); // Default to footer

    if ($position === 'header') {
        add_action('wp_head', 'ssn_display_notice');
    } else {
        add_action('wp_footer', 'ssn_display_notice');
    }
}
add_action('wp', 'ssn_add_notice_to_correct_position'); // Add action to load the notice in the correct position

// Add Settings and Donate links on plugins list
add_filter('plugin_action_links_' . plugin_basename(__FILE__), function ($links) {
    $settings_link = '<a href="' . admin_url('options-general.php?page=simple-site-notice') . '">Settings</a>';
    $donate_link = '<a href="https://buymeacoffee.com/makeyourweb" target="_blank">★ Donate</a>';

    array_unshift($links, $settings_link, $donate_link);

    return $links;
});