=== Restrict Content Pro - Campaign Monitor ===
Author URI: https://ithemes.com
Author: iThemes
Contributors: jthillithemes, layotte, ithemes
Tags: Restrict content, member only, registered, logged in, restricted access, restrict access, limiit access, read-only, read only, campaign monitor, newsletter, email list
Requires at least: 3.2
Tested up to: 5.8.0
Stable tag: 1.2.3

Add Campaign Monitor integration to Restrict Content Pro.

== Description ==

**On October 14th, 2021, all Restrict Content Pro add-ons will be removed from the WordPress plugin repository.**

**This plugin and all other Restrict Content Pro add-ons will remain available to download in your <a href="https://members.ithemes.com/panel/downloads.php">iThemes Member's Panel</a>.**

This is an add-on for the [Restrict Content Pro plugin](https://restrictcontentpro.com/). It does not function on its own.

This plugin will add a Campaign Monitor Newsletter signup option to the member registration form in Restrict Content Pro.

Learn more about Restrict Content Pro: https://restrictcontentpro.com/

== Installation ==

1. Download
2. Activate
3. Go to Restrict > Campaign Monitor
3. Enter your API Key and Client ID
4. Click "Save Options"
5. Choose the mailing list and click "Save Options" again

== Screenshots ==

1. Campaign Monitor settings page in Restrict Content Pro

== Changelog ==

= 1.2.3 =

* New: Version Bump

= 1.2.2 =

* New: Version Bump for history.txt file

= 1.2.1 =

* New: Added iThemes Updater

= 1.2 =

* Improvement: Updating the version of the create-send php library to support Php 7.4
* Improvement: Added visual obfuscation to API Key and Client ID input fields
* Improvement: Added relevant placeholders to the API Key and Client ID input fields
* Improvement: Added
* New: Added ConsentToTrack to add users to list

= 1.1 =

* New: Membership status tracked via a custom field.
* New: Updated to use new RCP 3.0 hooks where possible.
* New: Add default form label text.
* Tweak: Updated plugin author name and URL.

= 1.0.3 =

* New: Add setting to choose default checked state for opt-in.
* Fix: Use and load unique text domain.

= 1.0.2 =

* New: Added ID to opt-in paragraph tag.
* Fix: Uncaught exception 'CurlException' error when response from Campaign Monitor times out.

= 1.0.1 =

* Fix: Update Campaign Monitor library to avoid conflicts with other plugins that use it.
* Fix: Members added to list before payment is taken.
* Tweak: Clean up code and add PHPDocs.

= 1.0 =

* Initial release.

== Upgrade Notice ==

= 1.0.3 =
Added setting to choose default checked state for opt-in.

= 1.0.2 =
Added ID to opt-in paragraph tag and fix uncaught exception error.

= 1.0.1 =
Improved code base, updated Campaign Monitor library, and only add users to list after payment is taken.
