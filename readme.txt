=== SendPress Newsletters ===
Plugin URI: https://sendpress.com
Contributors: brewlabs, joshl, jaredharbour, itdoug
Tags: newsletter, newsletters, manager newsletter, newsletter signup, newsletter widget, subscribers, subscription, email marketing, email, emailing, smtp, sendpress, sendgrid, mandrill, mailchimp
Requires at least: 4.4
Tested up to: 4.9.4
Stable tag: 1.9.3.29.1
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

A Newsletter Plugin for WordPress to create, send, manage and track your Newsletters in one place. 

== Description ==

SendPress Newsletters is an easy to use WordPress newsletter plugin that has all the features you need. Create newsletter templates with your style and edit content just like you would a post in WordPress. Easily import post content from your site and schedule newsletters to be sent at the right time. Start sending great emails and newsletters today right from WordPress with our newsletter plugin. 

= Newsletter Features =

* **Unlimited Subscribers**
* **Unlimited Responsive Newsletters** with tracking
* Simple editor. With an **code-free experience**
* Customizable **subscription widget**, **page** or **custom form**
* Sync WordPress roles to newsletter subscriber lists
* **Single** And **Double Opt-In**
* Html and Text versions of Newsletters 
* **Customizable Newsletter Templates** with easy to use theme styler
* Stat tracking for each email: clicks, opens and unsubscribes. 
* Send with your web host or Gmail for free
* Verified compatible with: [Postman SMTP Mailer/Email Log](https://wordpress.org/plugins/post-smtp/)
* **Scheduled Sending** of newsletters

= Auto Cron =

We help make sure your newsletters are sent. You can enable Auto Cron from the admin panel and then our systems will check your site every 15 minutes to make sure your newsletters are sent. Check out our knowledge base article [What is AutoCron?](http://docs.sendpress.com/article/65-what-is-autocron) for more info.


= Check out our 2 minute run through video =

http://vimeo.com/56978344

= SendPress Newsletters Pro Features =

* **API Sending** for Mandrill, Sendgrid, Mailgun and Elastic Email
* Automated **Bounce handling** 
* Advanced report details ( see clicked links and subscriber details )
* **Custom HTML Templates** use any newsletter template you want
* Campaign Tracking: **Google Analytics**, KissMetrics, etc…
* Check your **spam** score ( Spamassasin )
* 1 Year of updates and Premium Support

= Support =

Please check out our Docs site [http://docs.sendpress.com/](http://docs.sendpress.com/) if you need help with anything or our [WordPress Forum](https://wordpress.org/support/plugin/sendpress).

Pro customers have direct access to our ticket system via [Your Account](https://sendpress.com/your-account/) page on SendPress.com.

**Follow [SendPress Newsletters on GitHub](https://github.com/brewlabs/sendpress)**

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

To get rid of the 404 error when viewing an email, you need to re-save your permalink structure. Go to Settings > Permalinks and click "Save Changes".

= How do I test Beta releases? =

We only recommend you do this only in a test environment. You can get the [SendPress Beta Tester Plugin](https://github.com/brewlabs/sendpress-beta-tester) from GitHub. This will pull the most recent code from Github using the WordPress updater.


= How do you contact us? =

Main Site: [https://sendpress.com](https://sendpress.com)

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

Previous releases can be downloaded from [GitHub](https://github.com/brewlabs/sendpress/releases)

= 1.9.3.29 - 2018.3.29.1 =
* Attempt to fix issue with wprocket loading

= 1.9.3.29 - 2018.3.29 =
* Fix for SES bounce

= 1.9.3.28 - 2018.3.28 =
* Support for bounce handling via SNS for AWS SES

= 1.9.3.21 - 2018.3.21 =
* Fix encoding on email sending

= 1.9.3.19 - 2018.3.19 =
* Sending update for responsive images


= 1.9.3.5 - 2018.3.5 =
* Add custom field support to email
* Add custom field to exports

= 1.9.2.26 - 2018.2.26 =
* Add custom fields to import

= 1.9.2.23.1 - 2018.2.23 =
* fix echo creating issues in dashboard


= 1.9.2.23 - 2018.2.23 =
* fix for the pro updater


= 1.9.2.22 - 2018.2.22 =
* rebuilt custom fields from the ground up
* fix for php 7.2

= 1.8.12.18 - 2017.12.18 =
* Add support for Sparkpost bounce handling

= 1.8.10.20.1 - 2017.10.20 =
* Update: Cron update

= 1.8.10.20 - 2017.10.20 =
* Update: Modified list sync for roles
* Update: Logger settings and log rotation


= 1.8.10.12 - 2017.10.12 =
* Fix: Email rendering on iOS and Apple Mail
* New: Added filter for showing main nav

= 1.8.9.27 - 2017.9.27 =
* Fix: Pro update
* Fix: Bug fixes
* New: Ability to change readmore from import

= 1.8.8.14 - 2017.8.14 =
* Fix: Error on some sites when it is Multisite
* Fix: PHP7 error on help page
* Info: Jetpack 5.2 causing broken email editor. Issue on file https://github.com/Automattic/jetpack/issues/7598

= 1.8.7.14 - 2017.7.14 =
* Fix: Bug with content not showing in email or template edit screen

= 1.8.7.10.1 - 2017.7.10 =
* Fix: Bug with sign-up form

= 1.8.6.16 - 2017.6.16 =
* New: Update for new Store Website

= 1.8.5.31 - 2017.5.31 =
* Fix: Translation strings
* Fix: Support for 4.8
* Fix: Font settings in Pro

= 1.8.5.24 - 2017.5.24 =
* Fix: Sending Test emails
* Fix: Sending speed
* Fix: Language issues
* Pro: Added support to pick font on templates

= 1.8.3.30 - 2017.3.30 =
* Fix: Saving custom fields
* Fix: Performence updates

= 1.8.2.23 - 2017.2.23 =
* Fix: use of php shorthand
* Fix: custom field error
* Fix: other minor bug fixes

= 1.8.2.18 - 2017.2.18 =
* Fix: Security update use builtin phpmailer class
* Fix: Sending issue from security updates

= 1.8.2.16 - 2017.2.18 =
* New: Add support for basic custom field on subscribers
* Fix: Bug related to link tracking

= 1.8.1.24 - 2017.1.24 =
* Fix: Remove phpmailer refs
* Fix: Meta info on list
* Fix: Video image replace fix

= 1.7.12.15 - 2016.12.15 =
* Fix: Form fix for stuff

= 1.7.12.7 - 2016.12.7 =
* Fix: admin notice fix for WordPress 4.7 ... caused white screen in SendPress

= 1.7.12.1 - 2016.12.1 =
* Fix: Bug with centering text and other styles in email

= 1.7.11.22 - 2016.11.22 =
* Fix: Cron sending speed

= 1.7.11.14 - 2016.11.14 =
* Fix: Unsubscribe from post notification

= 1.7.10.24 - 2016.10.24 =
* Fix: More sending update for faster sending

= 1.7.10.14 - 2016.10.14 =
* Fix: Autocron making multiple calls
* Update: imporve sending speed.

= 1.7.10.13 - 2016.10.13 =
* Fix: error with ajax class.

= 1.7.10.12 - 2016.10.12 =
* Update: Autocron runs for scheduled emails
* Fix: Confirm Template rendering
* Fix: Email sending with send now option


= 1.7.9.19 - 2016.9.19 =
* Update: Autocron now activated when emails added to queue
* Fix: Subscriber tags in links not working
* Fix: Widget errors
* Fix: Faster page load times.
* Other small bug fixes

= 1.7.8.11 - 2016.8.11 =
* New: Added list names to All Subscribers page
* Fix: Order by sorting on table views
* Fix: PHP error on Overview page

= 1.7.7.27 - 2016.7.27 =
* Fix: Post notification error
* Fix: Social Icon alignment
* Fix: Template list limited to 10

= 1.7.7.17 - 2016.7.17 =
* Fix: Error when sending

= 1.7.7.11 - 2016.7.11 =
* Fix: Responsive image fix
* Fix: Image wrapped in link fix
* Fix: CSV Import erroring on file delete

= 1.7.7.7 - 2016.7.7 =
* Fix: View in browser link
* Fix: subscriber search filter
* Update: Added unit test classes for travis-ci

= 1.7.7.6.1 - 2016.7.6 =
* Fix: Domdocument warning
* Fix: Styles not work in emails

= 1.7.7.6 - 2016.7.6 =
* Fix: error when sending test email
* Fix: image alignment when importing full post

= 1.7.7.5 - 2016.7.5 =
* Fix: Allow style tags in all html tags

= 1.7.6.22 - 2016.6.22 =
* Fix: Image align in email content

= 1.7.6.21 - 2016.6.21 =
* Fix: Widget not saving in some configurations

= 1.7.6.17 - 2016.6.17 =
* Fix: Clone templates
* Fix: allow style tags in templates

= 1.7.6.16 - 2016.6.16 =
* Fix: Syler fix for saving
* Fix: Hex colors converts to #000
* Fix: Undefined var issues on multiple screens
* Fix: Saving some pages

= 1.7.6.15 - 2016.6.15 =
* Fix: html tag support in email content
* Fix: import error and subscriber list error
* Fix: unsubscribe shortcode bug
* Fix: ajax security
* Fix: moved view security to main sendpress class
* Fix: added better support for flush_rewrite_rules()

= 1.7.6.11 - 2016.6.11 =
* Fix: Saving emails not working after sercurity updates

= 1.7.6.9 - 2016.6.9 =
* Update: Security fixes around sql queries
* Update: Added better error handling around sending
* Fix: Manage page showing blank in some cases
* Fix: Type in translation domain
* Fix: Undefined var warnings
* Fix: Can-spam HTML code 
* Fix: Forms shortcode variable error
* New: SendGrid and Elastic Email bounce postback (Requires SendPress Pro)


= 1.7.5.24 - 2016.5.24 =
* Fix: AutoCron activation
* Fix: Fix for unsubscribe shortcode missing custom text
* Update: Optimized database calls


= 1.7.5.2 - 2016.5.2 =
* Fix: Update AJAX for widgets to save properly
* Update: Added row count option to subscriber view pages
* New: Added new api route /spnl-api/cron for sending moving away from wp-cron.php

= 1.7.4.27 - 2016.4.27 =
* Fix: Table install script

= 1.7.4.20 - 2016.4.20 =
* Fix: Post search on email edit screen

= 1.7.4.13 - 2016.4.13 =
* New: Subscriber fields for Phone and Salutations
* Fix: Add to Any Share button conflict
* Fix: php shortage usage

= 1.7.3.19 - 2016.3.19 =
* New: Display images option for sp-recent-posts
* New: Added option to send unsubscribe page
* Update: Removed unused code mirror files
* Update: Table instal check updated for new tables
* Fix: SendPress Widgets
* Fix: Pro installer

= 1.7 - 2016.3.3 =
* Update: Security fixes around views
* Update: defined variable conflict
* Fix: admin notifications not sending instant
* Fix: typo in view

= 1.6.2.22 - 2016.2.22 =
* Fix: Cron sending url
* Update: report generation
* Update: link tracking
* Update: Loading and performence updates
* Fix: gerenal bug fixes

= 1.6.1.19 - 2016.1.20 =
* Fix: Update classes for PHP7
* Fix: test report page
* Fix: Updated template order
* Fix: remove unused vars
* Fix: SSL support for links

= 1.5.12.20 - 2015.12.20 =
* Fix: Jetpack sharing icons showing in email
* Fix: Subscriber list not paging


= 1.5.12.13 - 2015.12.13 =
* Fix: remove unused code
* Fix: deleting of uploaded lists
* Fix: added new defines
* Fix: removed loggin messages
* Fix: unsubscribe shortcode redirect fix
* Update: add new text for setup

= 1.5.11.9 - 2015.11.9 =
* Fix: send confirmation emails directly

= 1.5 - 2015.11.5 =
* Fix: return path on boucen handler
* Fix: old template double footer
* Update: add date range to recent posts shortcode
* Update: added more logs around sending
* New: Support for WP Email Delivery https://www.wpemaildelivery.com
* New: System email setup

= 1.2.10.20 -  2015.10.20 =
* Fix: Confirmations not sending fast
* Fix: Emails being marked as bounce when they send correctly
* Update: AutoCron background jobs set to daily and weekly now
* Update: add support for api.wped.co


= 1.2.10.15 -  2015.10.15 =
* Fix: Autoresponders being release early
* Update: Add sslverifiy option to wped

= 1.2.10.12 -  2015.10.12 =
* Fix: Confirmation emails not always sending 

= 1.2.10.10 -  2015.10.10 =
* Fix: manage shortcode url
* Update: Added error handling around autocron
* Update: wped sending with error tracking

= 1.2.10.6.1 -  2015.10.6 =
* Fix: WP Email Delivery Url

= 1.2.10.6 -  2015.10.6 =
* New: WP Email Delivery Support
* Fix: Overview page stats update
* Fix: Make sure subscriber is still active when sending
* Update: text domain settings for wp.org 

= 1.2.9.23 - 2015.9.23 =
* Fix: Old template system using wrong template
* Update: List limit per subscriber now at 500
* Update: added try catch to link tracker to log errors

= 1.2.9.22 - 2015.9.22 =
* Update: Column support for sp-recent-posts
* Fix: List Create Error
* Fix: Post notifiction display errors

= 1.2.9.13 - 2015.9.13 =
* Fix: Widget text showing when it should not
* Update: List Create and Edit
* Fix: Recent posts shortcode
* Update: New filters for limiting sending options


= 1.2.8.28 - 2015.8.28 =
* Fix: bug fix for widget without CSS
* Update: Prep for SendPress Delivery
* Update: Improve sending from autocron

= 1.2.8.16 - 2015.8.16 =
* Update: added check to Overview page for Pro
* Fix: Link tracker for clicks
* New: Check is_email on sending for double verification

= 1.2.8.13 - 2015.8.13 =
* Update: Link tracker
* New: SPNL Logger
* Fix: Issue when using wp_mail for sending

= 1.2.8.9 - 2015.8.10 =
* Update: Ready for WordPress 4.3
* Update: add option to repair db tables
* Update: Updated all reports to new stats
* Fix: Email sending count
* New: Basic system check

= 1.2.8.3 - 2015.8.3 =
* Fix: Missing $wpdb when using prepare
* Fix: Links not always tracked properly
* Update: Add Subscriber Screen

= 1.2.7.29 - 2015.7.29 =
* New: custom filter for list syncing - spnl-role-sync-get-user-args
* Fix: Security update causing files to load on non SendPress pages
* Fix: List sync not pulling all WordPress users

= 1.2.7.27 - 2015.7.27 =
* Fix: Signup widget
* Update: add signup tracker to emails sent out
* Update: add ability to validate hex codes 

= 1.2.7.26 - 2015.7.26 =
* Fix: Import CSV not loading correctly
* Fix: Templates having path saved in database
* Update: SendPress_Data added $wpdb->prepare to some queries
* Update: added hex validation for editor

= 1.2.1 - 2015.7.24 =
* Update: Code cleanup
* Update: Tighten security
* Fix: Some area's not working from security update

= 1.2 - 2015.7.24 =
* Update: Security Release.

= 1.1.7.21 - 2015.7.21 =
* Update: Pro post notification list setup
* Update: Translation files
* New: Linked plugin to http://wp-translations.org/
* Pro: Elastic Email module
* Fix: email tracker in gmail not always getting stats
* Fix: slow link tracker response to click
* Fix: Various spelling and missing translated text

= 1.1.7.14 - 2015.7.14 =
* Update: Language Translations
* Update: link tracker to use wp_remote_post
* New: website sending to support Postman SMTP
* New: add system check to api
* Fix: Post notification bulk update

= 1.1.6.23 - 2015.6.23 =
* Update: Sending Code
* Update: link tracker
* Fix: API bug
* Fix: Slow loading on pages
* Fix: Error when plugin can't connect to sendpress.com

= 1.1.6.12 - 2015.6.12 =
* Fix: install on WP Engine
* Fix: shortcode placeholder issue
* Fix: shortcode post data
* Update: Add new phpmailer code for sending emails

= 1.1.6.4 - 2015.6.4 =
* Fix: sp-recent-posts shortcode update
* Fix: link tracker encoding
* New: SendPress JSON API
* New: User Meta Query SYNC for lists

= 1.1.5.4 - 2015.5.4 =
* Fix: Update redirects causing permission issue
* Fix: Removed unused vars
* Fix: Small bug fixes

= 1.1.4.22 - 2015.4.22 =
* Fix: lowered filter priority on 'template_include'
* Fix: Some redirects missing admin.php

= 1.1.4.21 - 2015.4.21 =
* Fix: XSS Fixes and security review
* Fix: Test report stats
* Fix: Manage shortcode display
* Fix: Link tracking for mailto links
* Update: Recent Post shortcode - responsive update
* Update: Added IP to CSV Import

= 1.1.4.3 - 2015.4.3 =
* Fix: error_log call removed

= 1.1.4.2 - 2015.4.3 =
* Beta: Override header and footer when editing email
* Update: Added links to new docs site
* Update: sp-recent-posts responsive email support
* Update: Confirmation email uses new templates
* Update: Query to calculate total sent
* New: System Starter email Template
* Pro: Run shortcodes on custom templates

= 1.1.3.17 - 2015.3.17 =
* Fix: Manage Page and Shortcode

= 1.1.3.10.1 - 2015.3.10 =
* Fix: Some Ajax calls not working on Front End

= 1.1.3.10 - 2015.3.10 =
* New: Manage Page Shortcode with redirect option
* Fix: Query Optimization for Queue
* Fix: Optimized subscriber tables
* Fix: SP edit button on visual editor
* Update: Better Stat Notification Query
* Update: Ajax subscribe call return status for existing subscribers


= 1.1.2.25 - 2015.2.25 =
* Fix: Autocron check increased to 5 minutes
* Fix: Overview page not loading on some sites
* WP.org SVN cleanup

= 1.1.2.24 - 2015.2.24 =
* Fix: SQL error on some installs

= 1.1.2.22 - 2015.2.22 =
* Fix: Autocron running speed
* Fix: Send now not updating
* Update: Soft delete Reports
* Update: added new stat tracker


= 1.1.2.11 - 2015.2.11 =
* Fix: Sending popup not always responding
* Fix: AutoCron stoping for some sites
* Fix: Loading icon when CSS is off
* Fix: options sometimes returning wp_error
* Fix: Video embeds in emails
* New: AutoCron status check

= 1.1.0.2 - 2015.1.22 =
* Fix: Sending not working in some cases
* Fix: pdfprnt_content conflict
* Fix: mailto: missing @ symbol
* Fix: Email Titles not always showing

= 1.1.0.1 - 2015.1.19 =
* Fix: tables not installing

= 1.1 - 2015.1.19 =
* Fix: Link tracking updates
* Fix: Sending speed improved
* Fix: Update to overview page
* Fix: Widget breaking on some admin screens

= 1.0.12.11 - 2014.12.11 =
* Fix: Removed redirect to Whats New Page

= 1.0.12.10.1 - 2014.12.10 =
* Fix: Whats new page showing more then it should

= 1.0.12.10 - 2014.12.10 =
* Fix: don't track links starting with #
* Fix: pro custom templates not loading 
* Fix: SSL error on template style page
* Fix: Multiple templates created in some cases
* Fix: Moved update script to admin init and check user permissions first
* Fix: Update widget code to prevent broken widgets page
* Fix: Changed plugin widget loader


= 1.0.9 - 2014.12.07 =
* Fix: Template updates
* Update: Translation Strings
* Update: Transifex files updated
* New: Recent Post Shortcode


= 1.0.3 - 2014.11.08 =
* Fix: Multisite Setup
* Fix: Custom tags showing in emails
* Fix: Plugin install when site is created
* Fix: Update sending to improve speed

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
* Fix: Missing variables in cron
* Update: Pro table easier to use
* Update: Better style in-liner
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
* Fix: Line breaks not formatted correctly

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
* Fix: re-queue all emails fixed
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
* Fix: Import not check for valid .CSV
* Fix: ob_start in shortcode (fixes broken front-ends)
* Fix: Pro key management

= 0.9.9.7 - 2014.4.10 =
* Fix: styling on wpengine.com

= 0.9.9.6 - 2014.4.8 =
* Fix: List Sync not updating all users
* Fix: multiple sptemplates being created on some hosts
* New: Duplicate template check added to Advanced Settings
* Update: Travis CLI tests updated
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
* New: App.net Social Icon
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
* Fix: Re-queue Buttons fixed
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
* First version on WordPress.org

== Upgrade Notice ==

= 1.9.3.5 =
* Better support for custom fields

= 1.8.11.5 =
* New translations from Transifex

= 1.8.11.2 =
Minor bug fixes and translation updates

= 1.8.10.26 =
Cron fixes and other minor updates

= 1.8.10.20 =
Update list sync and other minor bugs

= 1.8.10.12 =
Bug fixes amd menu filters

= 1.8.2.15 =
Custom field added and bugs fixed

= 1.7.12.1 =
Minor bug fixes

= 1.7.11.14 =
Unsubscribe from post notification fix (PRO)

= 1.7.10.14 =
Minor bug fixes

= 1.7.8.11 =
Bug fixes and Show lists on All Subscribers page

= 1.7.7.11 =
Image in email bug fixes

= 1.7.7.7 =
View in browser link not showing email

= 1.7.7.5 =
Fixed inline styles in html tags

= 1.7.6.22 =
fixed image align in email content

= 1.7.6.21 =
Widget not always saving new subscribers

= 1.7.6.17 =
Clone templates fixed

= 1.7.6.13 =
Emails not saving

= 1.7.6.11 =
Security fixes and other updates

= 1.7.5.24 =
Fix for AutoCron

= 1.7.4.27 =
Table install fix

= 1.7.4.20 =
Post search on email edit screen fix

= 1.7.3.19 =
Bug fixes and security updates

= 1.7 =
Security fixes place update

