<?php
/**
 * Plugin Name: Simple Site Notice - Top Bar & Bottom Bar
 * Description: Display a customizable notice banner on your WordPress site. Supports HTML, custom colors, and sticky (fixed) option.
 * Version: 1.2.0
 * Author: MakeYourWeb
 * Author URI: https://plugins.makeyourweb.online/
 * License: GPL v2 or later
 */

if (!defined('ABSPATH')) {
    exit;
}

// Register settings
function simsino_myw_register_settings()
{
    add_option('simsino_myw_enable', 1);
    add_option('simsino_myw_notice_text', 'This is your site notice!');
    add_option('simsino_myw_background_color', '#fffbcc');
    add_option('simsino_myw_text_color', '#333333');
    add_option('simsino_myw_font_size', '16px');
    add_option('simsino_myw_padding', '10px 15px');
    add_option('simsino_myw_fixed', 0);
    add_option('simsino_myw_notice_position', 'footer'); // Default to footer
    add_option('simsino_myw_enable_close', 1); // New: enable close button
    add_option('simsino_myw_remember_close', 1); // New: remember close with cookie

    $args = array(
        'type' => 'string',
        'sanitize_callback' => 'sanitize_text_field',
        'default' => NULL,
    );

    register_setting('simsino_myw_options_group', 'simsino_myw_enable', $args);
    register_setting('simsino_myw_options_group', 'simsino_myw_notice_text', array('sanitize_callback' => 'wp_kses_post'));
    register_setting('simsino_myw_options_group', 'simsino_myw_background_color', $args);
    register_setting('simsino_myw_options_group', 'simsino_myw_text_color', $args);
    register_setting('simsino_myw_options_group', 'simsino_myw_font_size', $args);
    register_setting('simsino_myw_options_group', 'simsino_myw_padding', $args);
    register_setting('simsino_myw_options_group', 'simsino_myw_fixed', $args);
    register_setting('simsino_myw_options_group', 'simsino_myw_only_desktop');
    register_setting('simsino_myw_options_group', 'simsino_myw_custom_css');
    register_setting('simsino_myw_options_group', 'simsino_myw_notice_position', $args);
    register_setting('simsino_myw_options_group', 'simsino_myw_enable_close', $args); // New
    register_setting('simsino_myw_options_group', 'simsino_myw_remember_close', $args); // New
}
add_action('admin_init', 'simsino_myw_register_settings');

function simple_site_notice_enqueue_assets() {
    // Only enqueue if notice is enabled and (if close is enabled) not closed via cookie
    $enable = get_option('simsino_myw_enable');
    $enable_close = get_option('simsino_myw_enable_close');
    $remember_close = get_option('simsino_myw_remember_close');

    if (!$enable) return;
    if ($enable_close && $remember_close && isset($_COOKIE['simple_site_notice_closed'])) return;

    wp_enqueue_style('simple-site-notice-css', plugins_url('simple-site-notice.css', __FILE__));
    wp_enqueue_script('simple-site-notice-js', plugins_url('simple-site-notice.js', __FILE__), array(), false, true);

    // Pass the remember_close option to JS
    wp_localize_script('simple-site-notice-js', 'SimpleSiteNoticeSettings', array(
        'rememberClose' => $remember_close ? true : false,
    ));
}
add_action('wp_enqueue_scripts', 'simple_site_notice_enqueue_assets');

// Add settings page
function simsino_myw_register_options_page()
{
    add_menu_page(
        'Simple Site Notice Settings',
        'Simple Site Notice',
        'manage_options',
        'simple-site-notice',
        'simsino_myw_options_page',
        'dashicons-megaphone',
        80
    );
}
add_action('admin_menu', 'simsino_myw_register_options_page');

// Styles for toggle switches
add_action('admin_head', function () {
    echo '<style>
    input[type="checkbox"].simsino-toggle {
        appearance: none;
        width: 40px;
        height: 20px;
        background: #ccc;
        border-radius: 10px;
        position: relative;
        outline: none;
        cursor: pointer;
        transition: background 0.3s;
    }
    input[type="checkbox"].simsino-toggle:checked {
        background: #2c67b4;
    }
    input[type="checkbox"].simsino-toggle::before {
        content: "";
        position: absolute;
        top: 1px;
        left: 3px;
        width: 16px;
        height: 16px;
        background: white;
        border-radius: 50%;
        transition: transform 0.3s;
    }
    input[type="checkbox"].simsino-toggle:checked::before {
        transform: translate(20px, 3px);
    }
    </style>';
});

// Settings page content
function simsino_myw_options_page()
{
    ?>
    <div class="wrap">
        <h1>Simple Site Notice Settings</h1>
        <form method="post" action="options.php">
            <?php settings_fields('simsino_myw_options_group'); ?>
            <table class="form-table">
                <tr valign="top">
                    <th scope="row">Enable notice</th>
                    <td><input type="checkbox" class="simsino-toggle" name="simsino_myw_enable" value="1" <?php checked(1, get_option('simsino_myw_enable'), true); ?> /> Show notification on page</td>
                </tr>
                <tr valign="top">
                    <th scope="row">Notice Text (HTML allowed)</th>
                    <td><textarea name="simsino_myw_notice_text" rows="5"
                            cols="50"><?php echo esc_textarea(get_option('simsino_myw_notice_text')); ?></textarea></td>
                </tr>
                <tr valign="top">
                    <th scope="row">Background Color</th>
                    <td><input type="color" name="simsino_myw_background_color"
                            value="<?php echo esc_attr(get_option('simsino_myw_background_color')); ?>" /></td>
                </tr>
                <tr valign="top">
                    <th scope="row">Text Color</th>
                    <td><input type="color" name="simsino_myw_text_color"
                            value="<?php echo esc_attr(get_option('simsino_myw_text_color')); ?>" /></td>
                </tr>
                <tr valign="top">
                    <th scope="row">Font size </th>
                    <td><input type="text" name="simsino_myw_font_size" value="<?php echo esc_attr(get_option('simsino_myw_font_size')); ?>" placeholder="e.g. 16px"
                            /></td>
                </tr>
                <tr valign="top">
                    <th scope="row">Padding </th>
                    <td><input type="text" name="simsino_myw_padding" value="<?php echo esc_attr(get_option('simsino_myw_padding')); ?>" placeholder="e.g. 10px 20px"
                            /></td>
                </tr>
                <tr valign="top">
                    <th scope="row">Fixed Position?</th>
                    <td><input type="checkbox" class="simsino-toggle" name="simsino_myw_fixed" value="1" <?php checked(1, get_option('simsino_myw_fixed'), true); ?> /> Stick to the screen</td>
                </tr>
                <tr valign="top">
                    <th scope="row">Display only on desktop</th>
                    <td>
                        <input type="checkbox" class="simsino-toggle" name="simsino_myw_only_desktop" value="1" <?php checked(1, get_option('simsino_myw_only_desktop'), true); ?> />
                        <label for="simsino_myw_only_desktop">Hide the notice bar on mobile devices (under 768px)</label>
                    </td>
                </tr>
                <tr valign="top">
                    <th scope="row">Custom CSS</th>
                    <td>
                        <textarea name="simsino_myw_custom_css" rows="5" cols="50" placeholder=".simsino-myw-notice { font-family: Arial; text-decoration: underline; }"><?php echo esc_textarea(get_option('simsino_myw_custom_css')); ?></textarea>
                        <p class="description">Add custom CSS to style the notice bar (without &lt;style&gt; tags).</p>
                    </td>
                </tr>
                <tr valign="top">
                    <th scope="row">Notice Position</th>
                    <td>
                        <select name="simsino_myw_notice_position">
                            <option value="header" <?php selected('header', get_option('simsino_myw_notice_position')); ?>>Header</option>
                            <option value="footer" <?php selected('footer', get_option('simsino_myw_notice_position')); ?>>Footer</option>
                        </select>
                    </td>
                </tr>
                <tr valign="top">
                    <th scope="row">Enable close button</th>
                    <td>
                        <input type="checkbox" class="simsino-toggle" name="simsino_myw_enable_close" value="1" <?php checked(1, get_option('simsino_myw_enable_close'), true); ?> />
                        <label for="simsino_myw_enable_close">Allow users to close the notice bar</label>
                    </td>
                </tr>
                <tr valign="top">
                    <th scope="row">Remember closure with cookie</th>
                    <td>
                        <input type="checkbox" class="simsino-toggle" name="simsino_myw_remember_close" value="1" <?php checked(1, get_option('simsino_myw_remember_close'), true); ?> />
                        <label for="simsino_myw_remember_close">Remember closed state with a cookie</label>
                    </td>
                </tr>
            </table>
            <?php submit_button(); ?>
        </form>
        <hr>
            <p>Support this plugin: <a href="https://buymeacoffee.com/makeyourweb" target="_blank">Buy me a coffee</a> ☕ 
            </p>
            <h3>Have an idea for a new feature in the free version?</h3>
            <strong>One donation (minimum $7) = one new option added just for you (and everyone else!).</strong><br>
            Just let me know what you'd like to see via 
            <a href="https://plugins.makeyourweb.online/new-feature/" target="_blank">this form</a> or email 
            <a href="mailto:hello@makeyourweb.online">hello@makeyourweb.online</a>.</p>
    </div>
    <?php
}

// Display the notice
function simsino_myw_display_notice()
{
    $enable_close = get_option('simsino_myw_enable_close');
    $remember_close = get_option('simsino_myw_remember_close');

    // If remember_close is enabled and cookie is set, don't show the notice
    if ($enable_close && $remember_close && isset($_COOKIE['simple_site_notice_closed'])) {
        return;
    }

    $enable = get_option('simsino_myw_enable');
    if ($enable) {
        $notice_text = get_option('simsino_myw_notice_text');
        $background_color = get_option('simsino_myw_background_color');
        $text_color = get_option('simsino_myw_text_color');
        $font_size = get_option('simsino_myw_font_size');
        $padding = get_option('simsino_myw_padding');
        $only_desktop = get_option('simsino_myw_only_desktop');
        $custom_css = get_option('simsino_myw_custom_css');
        $position = get_option('simsino_myw_notice_position', 'footer');

        if ($position === 'header') {
            $fixed = get_option('simsino_myw_fixed') ? 'position: fixed; top: 0; left: 0;' : '';
        } elseif ($position === 'footer') {
            $fixed = get_option('simsino_myw_fixed') ? 'position: fixed; bottom: 0; left: 0;' : '';
        }

        $style = 'background-color: ' . esc_attr($background_color) . '; color: ' . esc_attr($text_color) . '; text-align: center;' . $fixed;
        $style .= 'font-size: ' . esc_attr($font_size) . ';';
        $style .= 'padding: ' . esc_attr($padding) . ';';

        $allowed_html = array(
            'a' => array(
                'href' => array(),
                'title' => array(),
                'target' => array(),
            ),
            'span' => array(),
            'br' => array(),
            'em' => array(),
            'strong' => array(),
        );

        $close_btn_html = '';
        if ($enable_close) {
            $close_btn_html = '<button class="simsino-myw-notice-close" aria-label="Close" style="margin-left:10px;">&times;</button>';
        }

        $notice_html = '<div class="simsino-myw-notice" id="simsino-myw-notice" style="' . esc_attr($style) . '">' . wp_kses($notice_text, $allowed_html) . $close_btn_html . '</div>';

        if ($position === 'header') {
            echo $notice_html;
        } elseif ($position === 'footer') {
            echo $notice_html;
        }

        echo '<style type="text/css">';
        if ($only_desktop) {
            echo '@media (max-width: 767px) { .simsino-myw-notice { display: none !important; } }';
        }
        if (!empty($custom_css)) {
            echo $custom_css;
        }
        echo '</style>';
    }
}

// Only load the notice in header or footer based on the selected option
function simsino_myw_add_notice_to_correct_position()
{
    $position = get_option('simsino_myw_notice_position', 'footer');
    if ($position === 'header') {
        add_action('wp_head', 'simsino_myw_display_notice');
    } else {
        add_action('wp_footer', 'simsino_myw_display_notice');
    }
}
add_action('wp', 'simsino_myw_add_notice_to_correct_position');

// Add Settings and Donate links on plugins list
add_filter('plugin_action_links_' . plugin_basename(__FILE__), function ($links) {
    $settings_link = '<a href="' . admin_url('options-general.php?page=simple-site-notice') . '">Settings</a>';
    $donate_link = '<a href="https://buymeacoffee.com/makeyourweb" target="_blank">★ Donate</a>';

    array_unshift($links, $settings_link, $donate_link);

    return $links;
});
