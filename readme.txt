=== Simple Site Notice – Top Bar & Bottom Bar ===
Contributors: makeyourwebonline
Donate link: https://buymeacoffee.com/makeyourweb
Tags: top bar, bottom bar, notice, banner, announcement
Requires at least: 5.0
Tested up to: 6.8
Stable tag: 1.2.0
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Display a customizable notification bar at the top or bottom of your site. Perfect for notices, promotions, or announcements.

== Description ==

**Simple Site Notice – Top Bar & Bottom Bar** lets you display a customizable notification banner on your WordPress site, either fixed to the top of the screen or placed inline at the bottom.

Perfect for cookie notices, promotions, announcements, donation requests, or any other message you want to highlight.

Key features:
* Place the notice at the top or bottom of your site
* Use plain text or HTML in your message
* Choose background and text colors
* Customize font size and padding
* Add your own CSS styles (without `<style>` tags)
* Option to hide the notice on mobile devices
* Option to enable a close button so users can dismiss the notice
* Option to remember closure with a cookie (notice stays hidden after closing)
* Lightweight and clean – no JavaScript required (unless close button is enabled)
* Works instantly – no setup complexity

== Installation ==

1. Upload the `simple-site-notice` folder to the `/wp-content/plugins/` directory.
2. Activate the plugin through the "Plugins" menu in WordPress.
3. Go to **Settings → Simple Site Notice** to customize your banner.

== Frequently Asked Questions ==

= Can I use HTML in the notice text? =
Yes, you can safely use HTML like `<a>`, `<strong>`, `<em>`, and similar tags.

= Can I control the appearance (font size, spacing)? =
Yes – there are dedicated options for font size and padding.

= Can I hide the bar on mobile? =
Yes – just enable the "Show only on desktop" checkbox in the settings.

= Can I add custom styles? =
Yes! Use the custom CSS textarea to add your own styling rules.  
**Important:** Do not include `<style>` tags – just the raw CSS rules.

= Can I let users close the notice bar? =
Yes – just enable the "Enable close button" option in the plugin settings.

= Can I make the notice stay hidden after closing? =
Yes – enable the "Remember closure with cookie" option. When a user closes the notice, it will stay hidden until the cookie expires or is cleared.

Example:
`.simsino-myw-notice { font-family: Arial; border-radius: 10px; }`

= Can I place the bar at the bottom of the screen? =
Yes – choose the "Bottom position" option in the plugin settings.

== Screenshots ==

1. Example notice bar at the top of a site
2. Settings panel inside the WordPress dashboard

== Changelog ==

= 1.2.0 =
* Added option to enable a close button for the notice bar
* Added option to remember closure state using a cookie

= 1.1.4 =
* Added "Show only on desktop" option to hide the notice bar on mobile devices
* Added support for custom CSS via a textarea field in plugin settings
* Minor code refactoring for improved maintainability

= 1.1.3 =
* Added font size and padding controls
* Updated readme.txt with external service disclosure (later deprecated)

= 1.1.2 =
* Improved compatibility with WordPress 6.8
* Fixed minor CSS issues in admin panel
* Enhanced sanitization of notice content

= 1.1.1 =
* Added option for fixed (sticky) notice
* Added support for HTML in notice text
* Added custom text color and background color options
* Improved settings page

= 1.1.0 =
* Initial release

== Upgrade Notice ==

= 1.2.0 =
* Added option to enable a close button for the notice bar
* Added option to remember closure state using a cookie

= 1.1.4 =
Adds option to hide notice on mobile and allows custom CSS styling via settings.

= 1.1.3 =
Adds font size and padding controls to the free version and improves overall compatibility.

= 1.1 =
Adds customization options for color, position, and HTML support.