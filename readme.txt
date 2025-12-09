=== Learning Certificates Manager ===
Contributors: hazman 
Email : h@zman.my
Requires at least: 6.0
Tested up to: 6.7
Requires PHP: 7.4
Stable tag: 1.3
License: GPLv2 or later


== Description ==

Learning Certificates Manager lets you organise your learning achievements by group (e.g. School, University A, University B) and display them in a modern card layout on any page.

Features:

* Custom post type "Certificates"
* Grouping via "Certificate Groups" taxonomy
* Certificate file selection via the Media Library
* Modern, responsive card layout
* Hover tooltips for descriptions
* Image preview tooltip when certificate file is an image

== Installation ==

1. Upload the `learning-certificates-manager` folder to the `/wp-content/plugins/` directory, or install the plugin through the WordPress Plugins screen.
2. Activate the plugin through the 'Plugins' screen in WordPress.
3. Go to "Certificates" → "Certificate Groups" and create your groups (e.g. School certificates, University A, University B).
4. Go to "Certificates" → "Add New" to create certificates:
   * Title = certificate name
   * Content = description (shown in tooltip)
   * Certificate File = pick from the Media Library
   * Assign a Certificate Group.
5. Add the shortcode `[learning_certificates]` to any page or post.

== Frequently Asked Questions ==

= Can I show only one group on a page? =

Not yet. In the current version, the shortcode shows all groups. A future update may add a `group` parameter.

== Screenshots ==

1. Admin screen showing Certificates and Certificate Groups.
2. Front-end grid of certificate groups with cards.
3. Hover tooltip with image preview.

== Changelog ==

= 1.0.0 =
* Initial release.

== Upgrade Notice ==

= 1.0.0 =
Initial release.

