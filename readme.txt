=== Plugin Name ===
Contributors: brewlabs
Donate link: http://sendpress.com/donate
Tags: newsletter, newsletters, manager newsletter, newsletter signup, newsletter widget, subscribers, subscription, email marketing, email, emailing, smtp, sendpress, sendgrid,
Requires at least: 3.3
Tested up to: 3.4.1
Stable tag: 0.8.6
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Easily create, send, manage and track your newsletters right from WordPress.

== Description ==

SendPress is designed to be lightweight and user friendly newsletter and email system for WordPress. Quickly create, design and edit emails to send to your subscribers.

= Features =

* Simple editor. With an html-free experience
* Easy to use theme styler
* Get stats for each newsletter: opens, clicks, unsubscribes
* Add a subscription form as a sidebar widget or in your pages
* Your newsletters will look great on the iPhone, in Gmail, Android, Yahoo, Hotmail, etc.
* Send with your web host or Gmail

Find out more at [SendPress.com](http://sendpress.com/ "Email Marketing and Newsletters for WordPress") 

= Support =

Please check out our support site [http://sendpress.zendesk.com](http://sendpress.zendesk.com) if you need help with anything.


== Installation ==

= Right within WordPress =
1. In your Admin, go to menu Plugins > Add
1. Search for `SendPress`
1. Click to install
1. Activate the plugin
1. A new menu `SendPress` will appear in your Admin

= Uploading a zip file =
1. Download the plugin (.zip file) on the right column of this page
1. In your Admin, go to menu Plugins > Add
1. Select the tab "Upload"
1. Upload the .zip file you just downloaded
1. Activate the plugin
1. A new menu `SendPress` will appear in your Admin

= Good old FTP =
1. Upload `sendpress` folder to the `/wp-content/plugins/` directory
1. Activate the plugin through the 'Plugins' menu in WordPress
1. A new menu `SendPress` will appear in your Admin

== Frequently Asked Questions ==

= How do you contact us? =

Main Site: [http://sendpress.com](http://sendpress.com)

Twitter: [@sendpress](http://twitter.com/sendpress)

Email: help@sendpress.com




== Screenshots ==

1. Post Insert.
2. Email editor and styler.
3. Template settings.
4. Default styles setup.
5. List Management.
6. Subscriber Management.
7. Reports.


== Changelog ==

= 0.8.6 =
* SP button added to editor 
* - Added Ability to insert post into email
* - Added Ability to insert subscriber info into email
* Social Media added to template
* Ability to use Custom CSS 
* Shortcode Documentation added to settings page
* New SP logo added
* Removed special characters from test email title
* Active Subscriber count off on lists screen
* scripts using wp_footer now optional uses wp_head by default
* link styles wrong in some mail clients
* plugin activation updated to streamline table creation
* uninstall updated to remove all tables and options 

= 0.8.5.1 =
* Patch for broken JS on SendPress settings screens.

= 0.8.5 =
* Bug Fix: Removed PHP Shortcode Syntax  
* Bug Fix: Sending through Gmail broken  
* Bug Fix: Email preview broken on send page  
* Bug Fix: Deleting e-mails from queue that did not send does not work
* Bug Fix: Renaming and creating a subscriber list fails without error message
* Bug Fix: Make List public by default on creation.
* Bug Fix: Send e-mail hangs in Safari under WordPress 3.4.1 
* Bug Fix: Send Emails Now behavior in Internet Explorer hangs with strange output 
* Bug Fix: Inserting images into Newsletter body replaces header image
* Bug Fix: Unable to delete a user or list 
* Bug Fix: Bulk delete of subscribers fails  
* Bug Fix: A list cannot be made private  
* Bug Fix: Special characters in the subject line get distorted  
* added exclude_from_search to custom post types


= 0.8.3 =
* removed use of str_getcsv
* added unofficial support of WP 3.2+
* added opt-in feedback option


= 0.8.2 =
* First version on WP.org

== Upgrade Notice ==

= 0.8.6 =
New Insert Post into email + multiple bug fixes

= 0.8.5.1 =
Fixed JS bug causing settings area to become unreponsive.

= 0.8.5 =
Improved Sending and Multiple bug fixes.

= 0.8.3 =
PHP error fix for str_getcsv

= 0.8.2 =
Minor bug fixes and other updates
