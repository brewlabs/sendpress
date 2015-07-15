=== SendPress Newsletters ===
Plugin URI: https://sendpress.com
Contributors: brewlabs
Tags: newsletter, newsletters, manager newsletter, newsletter signup, newsletter widget, subscribers, subscription, email marketing, email, emailing, smtp, sendpress, sendgrid, mandrill
Requires at least: 3.7
Tested up to: 4.0
Stable tag: 1.0.2
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Easy to use Email Newsletter Plugin for WordPress to create, send, manage and track your Newsletters.

== Description ==

SendPress is a plugin for WordPress that allows to write and send newsletters, and to gather and manage the subscribers. Built on the WordPress UI you already know. It's just as easy as creating a new Post.

Sending great emails and newsletters is something that should be easy and not require a third party system forget MailChimp, Aweber, etc.

= Check out our 2 minute run through video =

http://vimeo.com/56978344

Features of the plugin include:

* Simple editor. With an html-free experience
* Easy to use theme styler with ability to create a default style
* Stat tracking for each email: clicks, opens and unsubscribes. Bounce handling and report details with [SendPress Pro](http://sendpress.com/).
* Add a subscription form as a sidebar widget or in your pages
* Send with your web host or Gmail for free (Mandrill, Sendgrid, Amazon SES and more with PRO).
* Scheduled Sending of emails
* Sync Lists to WordPress Roles

More information at [SendPress.com](http://sendpress.com/).

= Support =

Please check out our Docs site [http://docs.sendpress.com/](http://docs.sendpress.com/) if you need help with anything. Also feel free to post to the WordPress support forum.

**Follow this plugin on [Git Hub](https://github.com/brewlabs/sendpress)**

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

1. Email Lists.
2. Email Edit Example.
3. Send Screen.
4. Confirm Send.
5. Subscriber Lists.
6. All Subscribers.
7. Subscriber Edit.
8. Settings & Style.
9. Emails & Pages.
10. Sending Account.	
11. Permissions.
12. Notifications.
13. Reports.
14. Queue.
15. Pro Tab.

== Changelog ==

= 1.0.2 - 2014.10.14 =
* Fix: form shortcode missing list id
* Fix: Multisite install now creates all tables
* Fix: widget setup
* Fix: Template names getting reset


= 1.0.1 - 2014.9.23 =
* Fix: Stop extra template creations
* Fix: Remove extra settings from new settings posts
* Fix: Template spacing and disable issues
* New: Added link to template setup


= 1.0 - 2014.9.5 =
* Fix: Mising variables in cron
* Update: Pro table easier to use
* Update: Better style inliner
* New: Email tag system
* New: Form builder/Widget
* New: Responsive Templates
* New: Email Editor and styler
* New: Email Cache Builder - reduce memory usage

= 0.9.9.9.9 - 2014.8.18 =
* Fix: Array index issue for new template system

= 0.9.9.9.8 - 2014.8.15 =
* Fix: Template showing php code in some clients
* Fix: SendPress options full of extra data
* Fix: Switched everything to esc_sql()
* Fix: DomDocument Error when sending emails if class missing

= 0.9.9.9.7 - 2014.7.23 =
* Fix: Issue with email cache

= 0.9.9.9.6 - 2014.7.22 =
* Fix: sending stoping at random points
* Fix: Linebreaks not formated correctly

= 0.9.9.9.5 - 2014.7.21 =
* Fix: Blank email sending
* Fix: Lower memory usage when sending
* Fix: Notification email fixed to support multiples

= 0.9.9.9.4 - 2014.7.16 =
* Fix: Overview stat fix
* Fix: Duplicate email sending to subscribers
* Fix: Translation code added
* Fix: Support to turn off mailto tracking
* Update: Added some more unit tests

= 0.9.9.9.3 - 2014.6.16 =
* Fix: Shortcode conflict (WooCommerce)
* Fix: Speed of email rendering
* New: Settings post type (Moving settings to custom post)
* New: Overview has preview of Pro Charts 2.0

= 0.9.9.9.2 - 2014.5.21 =
* Fix: requeue all emails fixed
* Fix: UI display issues
* Fix: Overview subscriber count

= 0.9.9.9.1 - 2014.5.14 =
* Fix: Javascript error on some pages
* Fix: CSS fixes for views
* Fix: TinyMCE error fix
* New: DB Support for PRO
* New: Stats caching to reduce db load
* Update: Passed 100K Downloads - Thanks for using SendPress

= 0.9.9.9 - 2014.5.8 =
* Fix: Role list sync updated
* Fix: CSS Update for Widget Form
* Fix: Widget post data
* New: Views for Queue health
* New: AutoCron activation pro discount
* New: Logging Class for Errors

= 0.9.9.8 - 2014.4.15 =
* Fix: Import changing status of users
* Fix: Import not check for valid csv
* Fix: ob_start in shortcode (fixes broken front-ends)
* Fix: Pro key management

= 0.9.9.7 - 2014.4.10 =
* Fix: styling on wpengine.com

= 0.9.9.6 - 2014.4.8 =
* Fix: List Sync not updating all users
* Fix: multiple sptemplates being created on some hosts
* New: Duplicate template check added to Advanced Settings
* Update: Trvix CLI tests updated
* Update: Pro installer should always use SSL
 

= 0.9.9.5 - 2014.3.27 =
* Fix: Code fix for PHP 5.2 - T_PAAMAYIM_NEKUDOTAYIM Error

= 0.9.9.4 - 2014.3.26 =
* Fix: CSS conflicts on WPEngine
* New: Unsubscribe Shortcode
* New: Recent Posts Shortcode
* New: Signup Shortcode
* New: Shortcode Docs on Help Page

= 0.9.9.3 - 2014.3.20 =
* Fix: Bug in SendPress_View that may cause white screen in SendPress

= 0.9.9.2 - 2014.3.20 =
* Fix: Saving Sending Account
* Fix: Social Icon sorting 
* Fix: Converted all SendPress API calls to SSL
* New: App.net Socail Icon
* New: SendPress Pro update reminder
* New: Set add Emails to Queue Ajax call count

= 0.9.9.1 - 2014.3.16 =
* Fix: Error sending when no social icons set
* Fix: Error message on CSV Import
* Fix: Template no saving correctly

= 0.9.9 - 2014.3.14 =
* Fix: Default Sender no longer set to old
* Update: Settings tabs renamed
* Update: Moved testing to sending account
* Update: On state added to menu
* New: Social Icon Setup

= 0.9.8.7 - 2014.3.3 =
* Fix: Hourly and Daily send count being off
* New: Mark lists and Reports as tests
* New: Added tabs to Reports section to support tests

= 0.9.8.6 - 2014.2.28 =
* New: Queue History Tab - see what has been sent
* Fix: Added option to repair events table
* Fix: Bugs from updated UI
* Fix: Pro missing send count on reports
* Fix: removed Presstrends Code
* New: Sending alert so you have to pick list


= 0.9.8.5 - 2014.2.24 =
* Fix: Shortcodes from WooCommerce causing conflict
* Update: Reports sending and queue totals more accurate
* Update: Message sending should be slightly faster ( Less DB calls per message )


= 0.9.8.4 - 2014.2.20 =
* Fix: Shortcodes not running with AutoCron
* Update: Added higher limits for AutoCron sending
* Update: Menu item says `SendPress Pro` if pro is installed	
* New: [sendpress-posts] shortcode 

= 0.9.8.3 - 2014.2.17 =
* Fix: Permissions on custom roles
* Fix: dbDelta duplicate key warning
* Fix: static missing from plugin activation function
* Fix: Widget not saving info
* Fix: ' sometimes showing odd characters in emails
* Fix: lists_checked not always set
* Fix: WordPress role sync now AJAX based
* New: Export option on all subscribers screen
* New: Advanced Settings for fine tuning SendPress install
* New: Youtube and Vimeo videos converted to images
* New: increased Minimum required WordPress version to 3.7 


= 0.9.8.2 - 2014.2.4 =
* Fix: Error being logged for notifications
* Fix: Errors when trying to view email that has been deleted
* Fix: Missing UI styles from bootstrap3 update
* Fix: Subscriber Total on overview
* New: Shortcode now allows for lists unchecked by default

= 0.9.8.1 - 2014.1.31 =
* Fix: SendPress editor button not working

= 0.9.8 - 2014.1.29 =
* Fix: Pro Activation
* Fix: Reque Buttons fixed
* New: Admin UI Upgrade
* New: New Overview Page

= 0.9.7.3 - 2014.1.14 =
* Fix: Reports missing title
* Fix: Variable assignment causing errors
* New: Faster AutoCron

= 0.9.7.2 - 2014.1.6 =
* Fix: 3.8 display issue
* Fix: Delete Subscribers fixed
* Fix: Notifications Update
* New: Hipchat notifications

= 0.9.7.1 - 2013.12.22 =
* Fix: 3.8 display issues
* Fix: Shortcode for signup
* Fix: List create and Role sync settings
* Fix: Report counts and times
* Fix: AutoCron causing empty entry in error logs
* Fix: uninstall errors
* Fix: PHPmailer email encoding 

= 0.9.7 - 2013.12.9 =
* Fix: Queue count being off
* Fix: Unistall fixed
* Fix: Character encoding fixes
* Fix: Notification sometimes showing wrong list name

= 0.9.6.4 - 2013.11.21 =
* Fix: error on uninstall
* Fix: notification showing wrong text
* Fix: console.log in script file

= 0.9.6.3 - 2013.11.15 =
* Fix: unsubscribe not being register in some cases
* Fix: admin notifications sometimes creating error
* Fix: DB updates faster and better on large lists
* Fix: call to static function SendPress_Error::log
* Fix: Pro activation checking without Pro installed
* New: Option to try and install missing tables added to Advanced

= 0.9.6.2 - 2013.11.14 =
* Fix Update timeout on 100K+ subscriber lists

= 0.9.6.1 - 2013.11.13 =
* WordPress role sync first and last name
* fix blank confirmation email
* fix confirm email link now uses home_url
* New send test email for edit screen
* ajax loading image for widgets
* Update script now uses dbDelta

= 0.9.6 - 2013.11.6 =
* WordPress role sync
* Scheduled Sending
* Better character encoding support
* Fix for bulk actions
* Support for MP6 UI
* New Logos added

= 0.9.5.4 - 2013.10.19 =
* Fix for notifications
* Fix for events being saved

= 0.9.5.3 - 2013.10.16 =
* Bug with 'Send Emails Now' fixed
* Fixed bad table name in SendPress_Data

= 0.9.5.2 - 2013.10.16 =
* Improved CSV import for different column formats
* Auto Cron now more stable
* Unsubscribe link redirects to manage page
* New Error Logging Class
* Added new indexes to Custom Tables
* WebHook for Bounce Handling Event- requires Pro

= 0.9.5.1 - 2013.10.7 =
* CSV Import
* cron sending fixed
* email sometimes missing body text fixed
* AutoCron update 
* New Screenshots

= 0.9.5 - 2013.9.30 =
* Translation Updates
* Delete Subscriber from system
* Better Queue Info and Stats
* New "All Subscribers" View
* Full text search of subscribers
* Unsubscribe link not always working

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

= 1.0.2 =
Template fixes and shortcode updates

= 1.0.1 =
Templates and Settings fixes

= 1.0 =
New Templates and Bug Fixes

= 0.9.9.9.9 =
Small bug fixes for 1.0 prep release

= 0.9.9.9.4 =
Sending and stats update

= 0.9.9.9.4 =
Sending and stats update

= 0.9.9.9.3 =
Shortcode issue fix when in email

= 0.9.9.9.2 =
Minor bug fixes

= 0.9.9.9.1 =
Bug fixes

= 0.9.9.9 =
Queue update and bug fixes

= 0.9.9.8 =
Bug fixes for shortcods and imports

= 0.9.9.7 =
Styling fix for WPEngine Customers

= 0.9.9.5 =
Fix for systems running php 5.2

= 0.9.9.4 =
Bug Fixes and Shortcodes added

= 0.9.9.3 =
Fix for possible view error causing white screen on SendPress Tab

= 0.9.9.2 =
Bug fix for activating sending account

= 0.9.9.1 =
Bug fix for sending and csv import

= 0.9.9 =
New Socail Icons and bug fixes

= 0.9.8.7 =
Queue Hourly and Daily send counts fix

= 0.9.8.6 =
Queue History and Bug Fixes

= 0.9.8.3 =
Video embed as images support added

= 0.9.8.2 =
Maintenance and Bug Fixes

= 0.9.8.1 =
Editor Button Fixed

= 0.9.8 =
New UI and bug fixes

= 0.9.7.3 =
Missing Report title fix

= 0.9.7.2 =
Delete Subscriber fix

= 0.9.7.1 =
Various Bug Fixes

= 0.9.7 =
Multiple bug fixes

= 0.9.6.4 =
Uninstall bug fix

= 0.9.6.3 =
Minor bug fixes

= 0.9.6.2 =
Fix for db timeout in 0.9.6.1

= 0.9.6.1 =
Confirmation email fix and role sync first and last names

= 0.9.6 =
WordPress role syncing and Scheduled sending

= 0.9.5.3 =
Bug fix for Send Email Now stopping

= 0.9.5.2 =
Better sending and import updates

= 0.9.5.1 =
CSV Import and cron fixes

= 0.9.5 =
New subscriber screens and bug fixes

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