=== Heroic Webfonts ===
Contributors: herothemes
Tags: fonts, webfonts, live, customizer, custom fonts, google fonts, websafe fonts, text, no css
Requires at least: 3.5
Tested up to: 4.1
Stable tag: 1.2
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html


== Description ==

Heroic Webfonts allows you to customize your site fonts via the in-built WordPress theme customizer. No CSS knowledge required!

Use websafe fonts or webfonts from the google font library. You can also modify font color, size and style.

There are default styles the plugin supports (body and heading), which can be overridden by a supporting theme and you can add custom fonts with your own selectors and styles.

== Installation ==

It's easy to get started:

1. Upload `ht-webfonts` to the `/wp-content/plugins/` directory or goto Plugins>Add New and search for Heroic Webfonts.
2. Activate the plugin through the 'Plugins' menu in WordPress.
3. In Themes>Customize there will be a new panel for Heroic Webfonts.
4. For each webfont there are a number of different sections which are self explantory, except selector, which is the CSS selector you wish to apply to your font.
5. The live customizer attempts to give you a real-time view of how your site will look as you make changes to the fonts, however these are not alway accurate due to the complex nature of css. You may need to refresh the page to view changes made.



== Frequently Asked Questions ==

= Q. I have a question! =

A. Please raise your question on the Heroic Webfonts support page at WordPress.org


== Screenshots ==

1. A screenshot of the plugin in action with one of our themes.

== Changelog ==

= 1.2 =

Compatibility improvements
Webfonts now in a panel


= 1.1 =

Various fixes and improvements to the UI and logic

= 1.0 =

Initial release.


== Developer Notes ==

To support this product in your theme you must declare theme support for 'ht-webfonts'.
A filter hook called 'ht_webfonts_themefonts' is used to load your default theme fonts onto a blank array, the loaded array must then be returned by the filter function. Set a value to false in order to set an initial value for that control.
Return an array of HT_Custom_Webfonts using the notation in ht-webfonts.php

