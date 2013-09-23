=== SendPress: Email Marketing and Newsletters ===
Plugin URI: http://sendpress.com
Contributors: brewlabs
Tags: newsletter, newsletters, manager newsletter, newsletter signup, newsletter widget, subscribers, subscription, email marketing, email, emailing, smtp, sendpress, sendgrid,
Requires at least: 3.4
Tested up to: 3.6.1
Stable tag: 0.9.4.7
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Easily create, send, manage and track your newsletters and emails right from WordPress.

== Description ==

Sending great emails and newsletters is something that should be easy and not require a third party system. This plugin aims to fix that and allow you to manage newsletters and emails with easy within WordPress. Instead of focusing on providing every single feature under the sun, SendPress tries to provide only the ones that you really need. It aims to make email marketing through WordPress easy, complete and extensible.

= Check out our 2 minute run through video =

http://vimeo.com/56978344

**Follow this plugin on [Git Hub](https://github.com/brewlabs/sendpress)**

Features of the plugin include:

* Simple editor. With an html-free experience
* Easy to use theme styler with ability to create a default style
* Stat tracking for each email: clicks, opens and unsubscribes. Bounces and report details will be available as [add-ons](http://sendpress.com/) soon.
* Add a subscription form as a sidebar widget or in your pages
* Send with your web host or Gmail with more to come.
* Extensible with many [add-ons](http://sendpress.com/). Coming Soon.
* Developer friendly with dozens of actions and filters

More information at [SendPress.com](http://sendpress.com/).


= Support =

Please check out our support site [http://sendpress.com/support/knowledgebase/](http://sendpress.com/support/) if you need help with anything.



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

= Getting a 404 error? =

To get rid of the 404 error when viewing an email, you need to resave your permalink structure. Go to Settings > Permalinks and click "Save Changes".

= How do I test Beta releases? =

We only recommend you do this only in a test enviroment. You can get the [SendPress Beta Tester Plugin](https://github.com/brewlabs/sendpress-beta-tester) from GitHub. This will pull the most recent code from github using the WordPress updater.


= How do you contact us? =

Main Site: [http://sendpress.com](http://sendpress.com)

Twitter: [@sendpress](http://twitter.com/sendpress)





== Screenshots ==

1. Post Insert.
2. Email editor and styler.
3. Template settings.
4. Default styles setup.
5. List Management.
6. Subscriber Management.
7. Reports.


== Changelog ==

= 0.9.4.7 - 2013.9.23 =
* Free SendPress Pro Autocron added
* Queue updated to run faster
* DB Tables updated for Windows
* Switched front-end pages to check home_url()


= 0.9.4.6 - 2013.9.11 =
* email encoding not always saving

= 0.9.4.5 - 2013.9.10 =
* email encoding options
* forms and iframe updates
* s2member conflict fix
* sending speed saving


= 0.9.4.4 - 2013.9.4 =
* forms not posting emails correctly
* fixed key activation
* fixed manage link in email unsubscribing users
* fixed reports not showing correct emails

= 0.9.4.3 - 2013.9.2 =
* added Support for s2member roles
* Updated senders to have the correct return path
* added new queue creator - help reduce memory usage and timeouts
* better support for large lists

= 0.9.4.2 - 2013.8.29 =
* added manage subscription link to template
* Support for Mandrill sending in Pro
* Added check before sending confirmation email
* change template encoding to 8bit
* update subscriber add UI

= 0.9.4.1 - 2013.6.19 =
* Fix for Send Confirm Screen


= 0.9.4 - 2013.6.12 =
* SendPress Pro Amazon SES Enabled
* New rewrite rules for email previews
* Added new translations
* Fixed bad url on cancel button
* Added SendPress image size
* Template rendering bug fix for public views

= 0.9.3.4 - 2013.6.3 =
* Premalink 404 error fix
* add support for custom WordPress roles mapped to SP permisions
* DB Table check in Help Section
* added clickable link to confirmation emails

= 0.9.3.3 - 2013.5.5 =
* Bug fix for public pages

= 0.9.3.2 - 2013.5.4 =
* Simple Notifications added
* Extra spacing in template fixed
* Fixed broken links

= 0.9.3.1 - 2013.4.18 =
* View link errors fixed
* Minor updates for php 5.2
* Subscriber Widget saving update
* Added more tracking to subscriber events

= 0.9.3 - 2013.3.29 =
* New Pro options
* Better php 5.2 support
* Author Editing email fix
* Sign-up Post fix
* New View access controls


= 0.9.2 - 2013.3.6 =
* Pro tab activated
* Email HTML filter
* Fixed for confirmation link in href
* Fixed a few typos
* Remove all custom post type posts on uninstall

= 0.9.1 - 2013.2.3 =
* Fix for cron not sending on some hosts


= 0.9 - 2013.1.31 =
* New Qeueue manager for emails per day and per hour
* Ability to use theme template for SendPress Pages
* New forms page for each list 
* Updated Widget to support multiple lists
* Screen options for table views
* Added Queue info to reports screen
* Auto create text version of emails
* Improved Cron and sending overall
* HTML Support in Widget description
* Insert Post with title links back to post
* Multilple other bug fixes and code updates


= 0.8.8.1 =
* uninstall file error fixed.
* Security check added to all files
* DB Error on unistall and reinstall
* Active Subscribers bug in 3.5 fixed
* Spanish Translation Started
* Remove all subscribers from list added
* PHP 5.2 bug fixed

= 0.8.8 =
* Double Optin added
* Code optimized for memory usage and loading
* Added better Language/Translation Support 
* Added option to remove all emails from Queue
* Fixed extra \ showing in some settings
* added email template text to translation code
* Simplified Settings and created Help section
* Added checks for W3 Total Cache

= 0.8.7.1 =
* Stopped plugin from creating multiple cron calls
* Fixed link for setup notice
* Removed CAN-SPAM as required

= 0.8.7 =
* Added Permisions settings for WordPress Roles
* Implimented SendPress View class
* Updated link tracking to use a single token
* Fixed issue with facebook plugin posting emails to facebook
* Styler bugs fixed - text color, border and links
* Convert Lists to Custom Post Type
* Added more fields to email personalize screen
* Updated Open and Click tracking to be more accurate
* Fixed image alignment when sending emails with images
* Added filters and hooks to reports section
* Added List Settings button on subscribers page
* Code cleanup and refactoring.

= 0.8.6 =
* SP button added to editor 
* -Added Ability to insert post into email
* -Added Ability to insert subscriber info into email
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

= 0.9.4.7 =
Free Pro Feature and Bug Fixes

= 0.9.4.6 =
email encoding not always saving

= 0.9.4.4 =
Form post fix and manage link 

= 0.9.4.3 =
Better memory usage and custom role support

= 0.9.4.2 =
Multiple bug fixes

= 0.9.4 =
Bug fixes and general performance updates

= 0.9.3.4 =
Permalink fix and Confirmation email update

= 0.9.3.3 = 
Fixed bug with public pages

= 0.9.3.2 =
New Notifications option and bug fixes

= 0.9.3.1 =
Minor bug fixes and other updates

= 0.9.3 =
Better support for older versions of PHP

= 0.9.2 =
Pro tab activated and minor bug fixes

= 0.9.1 =
Fix for Automated sending on some hosts

= 0.9 =
New Queue Manager, Theme Support and Forms

= 0.8.8.1 =
Unistall Error fix and bug fixes.

= 0.8.8 =
Double optin and performence updates.

= 0.8.7.1 =
Stopped SendPress from creating multiple crons.

= 0.8.7 =
Link tracking security update and multiple bug fixes.

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