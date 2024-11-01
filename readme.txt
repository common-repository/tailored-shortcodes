=== Tailored Shortcodes ===
Contributors:		tailoredweb, ajferg
Tags:				
Requires at least:	4.9
Tested up to:		4.9.6
Stable tag:			1.0.1
Requires PHP:		5.6

Allows you to create shortcodes which output HTML.  This is useful for including forms or other blocks of custom HTML wherever you need them.

== Description ==

This plugin contains a helper to quickly add custom code (like forms or surveys) using shortcodes.  It's built by [Tailored Media](https://www.tailoredmedia.com.au "Tailored Media") for use on our sites, but anyone is welcome to use it.

== Installation ==

1. Upload the plugin or install through WordPress plugins search.
1. Activate the plugin.
1. Go to Tailored Shortcodes in the admin area to start adding shortcodes.

For each shortcode, specify the code, and the markup.

== Frequently Asked Questions ==

= How do I use this plugin? =

Create a shortcode with a CODE and MARKUP.  When you use the shortcode [CODE], then your MARKUP will be inserted.

Each CODE must be unique, and used only once.  You should avoid using any CODEs that match existing shortcodes.

The MARKUP can be HTML, CSS, JS etc.  It will be embedded raw, so be careful with what you use.

== Changelog ==

= 1.0.1 =
* Fix so admin metabox doesn't appear on other pages/posts

= 1.0 =
* Initial release
* Using CodeMirror for formatting
