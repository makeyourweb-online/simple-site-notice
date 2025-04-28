<?php
/*
Plugin Name: Simple Site Notice
Description: Display a simple, customizable notice banner on your site. Support development on BuyMeACoffee ☕
Version: 1.1
Author: Make Your Web
Author URI: https://buymeacoffee.com/makeyourweb
*/

if (!defined('ABSPATH')) {
    exit;
}

// Register settings
function ssn_register_settings() {
    add_option('ssn_notice_text', 'This is your site notice!');
    add_option('ssn_background_color', '#fffbcc');
    add_option('ssn_text_color', '#333333');
    add_option('ssn_fixed', 0);

    register_setting('ssn_options_group', 'ssn_notice_text');
    register_setting('ssn_options_group', 'ssn_background_color');
    register_setting('ssn_options_group', 'ssn_text_color');
    register_setting('ssn_options_group', 'ssn_fixed');
}
add_action('admin_init', 'ssn_register_settings');

// Add settings page
function ssn_register_options_page() {
    add_options_page('Simple Site Notice', 'Simple Site Notice', 'manage_options', 'simple-site-notice', 'ssn_options_page');
}
add_action('admin_menu', 'ssn_register_options_page');

// Settings page content
function ssn_options_page() {
?>
    <div class="wrap">
        <h1>Simple Site Notice Settings</h1>
        <form method="post" action="options.php">
            <?php settings_fields('ssn_options_group'); ?>
            <table class="form-table">
                <tr valign="top">
                    <th scope="row">Notice Text (HTML allowed)</th>
                    <td><textarea name="ssn_notice_text" rows="5" cols="50"><?php echo esc_textarea(get_option('ssn_notice_text')); ?></textarea></td>
                </tr>
                <tr valign="top">
                    <th scope="row">Background Color</th>
                    <td><input type="text" name="ssn_background_color" value="<?php echo esc_attr(get_option('ssn_background_color')); ?>" class="regular-text" placeholder="#ffffff" /></td>
                </tr>
                <tr valign="top">
                    <th scope="row">Text Color</th>
                    <td><input type="text" name="ssn_text_color" value="<?php echo esc_attr(get_option('ssn_text_color')); ?>" class="regular-text" placeholder="#000000" /></td>
                </tr>
                <tr valign="top">
                    <th scope="row">Fixed Position?</th>
                    <td><input type="checkbox" name="ssn_fixed" value="1" <?php checked(1, get_option('ssn_fixed'), true); ?> /> Stick to top of the screen</td>
                </tr>
            </table>
            <?php submit_button(); ?>
        </form>
    </div>
<?php
}

// Display the notice
function ssn_display_notice() {
    $notice_text = get_option('ssn_notice_text');
    $background_color = get_option('ssn_background_color');
    $text_color = get_option('ssn_text_color');
    $fixed = get_option('ssn_fixed') ? 'position: fixed; top: 0; left: 0; width: 100%; z-index: 9999;' : '';

    echo '<div style="background-color: ' . esc_attr($background_color) . '; color: ' . esc_attr($text_color) . '; padding: 10px; text-align: center;' . $fixed . '">';
    echo $notice_text;
    echo '</div>';
}
add_action('wp_footer', 'ssn_display_notice');

// Add Settings and Donate links on plugins list
add_filter('plugin_action_links_' . plugin_basename(__FILE__), function ($links) {
    $settings_link = '<a href="' . admin_url('options-general.php?page=simple-site-notice') . '">Settings</a>';
    $donate_link = '<a href="https://buymeacoffee.com/makeyourweb" target="_blank">★ Donate</a>';
    
    array_unshift($links, $settings_link, $donate_link);
    
    return $links;
});
