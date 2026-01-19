# SendPress Security Audit and Fixes

**Date**: 2025-01-14
**Version**: 1.24.8.19 (Post-Security Patch)
**Audited By**: Security Review Team

## Executive Summary

This document details the security vulnerabilities identified and patched in the SendPress WordPress plugin. The audit addressed three known WPScan vulnerabilities (including CVE-2023-47517) and discovered 30+ additional XSS vulnerabilities and 4 SQL injection vulnerabilities that were also remediated.

**Total Files Modified**: 40
**Vulnerability Categories Fixed**:
- Cross-Site Scripting (XSS) - Reflected and Stored
- SQL Injection (SQLi)
- Deprecated PHP Functions (FILTER_SANITIZE_STRING)

---

## WPScan Reported Vulnerabilities (Fixed)

### 1. CVE-2023-47517 - Reflected XSS (CVSS 7.1)

**Severity**: High
**Description**: The plugin did not sanitize or escape parameters before outputting them back in the page, leading to Reflected Cross-Site Scripting.

**Root Cause**: The `SendPress_Security` class used deprecated `FILTER_SANITIZE_STRING` which was removed in PHP 8.1+, causing input validation to fail silently.

**File Modified**: [class-sendpress-security.php](classes/class-sendpress-security.php)

**Fix Applied**:
```php
// Before (vulnerable):
return filter_input(INPUT_GET, $field, FILTER_SANITIZE_STRING);

// After (secure):
return sanitize_text_field( wp_unslash( isset( $_GET[$field] ) ? $_GET[$field] : '' ) );
```

---

### 2. Admin+ Stored XSS via Settings

**Severity**: Medium (requires admin access)
**Description**: Multiple settings pages allowed stored XSS through unescaped form inputs and database values.

**Files Modified**:
- [class-sendpress-view-settings-shared.php](classes/views/class-sendpress-view-settings-shared.php)
- [class-sendpress-view-settings-styles.php](classes/views/class-sendpress-view-settings-styles.php)
- [class-sendpress-view-emails-social.php](classes/views/class-sendpress-view-emails-social.php)
- [class-sendpress-view-settings-notifications.php](classes/views/class-sendpress-view-settings-notifications.php)

**Fix Pattern Applied**:
```php
// Before (vulnerable):
value="<?php echo $options['email']; ?>"

// After (secure):
value="<?php echo esc_attr( $options['email'] ); ?>"
```

---

### 3. Admin+ Stored XSS via Form Settings

**Severity**: Medium (requires admin access)
**Description**: The widget/form settings page contained multiple unescaped outputs of stored settings values.

**File Modified**: [class-sendpress-view-settings-widgets.php](classes/views/class-sendpress-view-settings-widgets.php)

**Fix Applied**: 19+ instances of proper escaping added using `esc_attr()`, `esc_html()`, `esc_textarea()`, and `intval()` functions.

---

## Additional Vulnerabilities Discovered and Fixed

### 4. SQL Injection Vulnerabilities (4 instances)

**Severity**: High
**File Modified**: [class-sendpress-db.php](classes/db/class-sendpress-db.php)

**Vulnerable Methods Fixed**:
- `get()` - Primary key lookup
- `get_by()` - Column-based lookup
- `get_column()` - Single column value lookup
- `get_column_by()` - Column value by another column

**Fix Pattern Applied**:
```php
// Before (vulnerable):
return $wpdb->get_row( "SELECT * FROM $this->table_name WHERE $column = '$row_id' LIMIT 1;" );

// After (secure):
$column = sanitize_key( $column );
return $wpdb->get_row( $wpdb->prepare( "SELECT * FROM $this->table_name WHERE $column = %s LIMIT 1;", $row_id ) );
```

---

### 5. CSV Import XSS (High Severity)

**File Modified**: [class-sendpress-view-subscribers-csvprep.php](classes/views/class-sendpress-view-subscribers-csvprep.php)

**Description**: User-uploaded CSV content was displayed without escaping, allowing malicious JavaScript in CSV files to execute.

**Fix Applied**: All CSV values now escaped with `esc_html()`.

---

### 6. Subscriber Data XSS (Multiple Files)

**Files Modified**:
- [class-sendpress-view-subscribers-subscriber.php](classes/views/class-sendpress-view-subscribers-subscriber.php)
- [class-sendpress-view-subscribers-listedit.php](classes/views/class-sendpress-view-subscribers-listedit.php)
- [class-sendpress-view-subscribers-listcreate.php](classes/views/class-sendpress-view-subscribers-listcreate.php)
- [class-sendpress-view-subscribers-customfields.php](classes/views/class-sendpress-view-subscribers-customfields.php)
- [class-sendpress-view-subscribers-subscribers.php](classes/views/class-sendpress-view-subscribers-subscribers.php)
- [class-sendpress-subscribers-table.php](classes/class-sendpress-subscribers-table.php)

**Fixes Applied**: Subscriber emails, names, custom fields, and list data now properly escaped.

---

### 7. Email/Template View XSS (Multiple Files)

**Files Modified**:
- [class-sendpress-view-emails-send.php](classes/views/class-sendpress-view-emails-send.php)
- [class-sendpress-view-emails-send-confirm.php](classes/views/class-sendpress-view-emails-send-confirm.php)
- [class-sendpress-view-emails-send-cancel.php](classes/views/class-sendpress-view-emails-send-cancel.php)
- [class-sendpress-view-emails-send-queue.php](classes/views/class-sendpress-view-emails-send-queue.php)
- [class-sendpress-view-emails-edit.php](classes/views/class-sendpress-view-emails-edit.php)
- [class-sendpress-view-emails-create.php](classes/views/class-sendpress-view-emails-create.php)
- [class-sendpress-view-emails-createauto.php](classes/views/class-sendpress-view-emails-createauto.php)
- [class-sendpress-view-emails-autoedit.php](classes/views/class-sendpress-view-emails-autoedit.php)
- [class-sendpress-view-emails-header.php](classes/views/class-sendpress-view-emails-header.php)
- [class-sendpress-view-emails-footer.php](classes/views/class-sendpress-view-emails-footer.php)
- [class-sendpress-view-emails-headerpage.php](classes/views/class-sendpress-view-emails-headerpage.php)
- [class-sendpress-view-emails-footerpage.php](classes/views/class-sendpress-view-emails-footerpage.php)
- [class-sendpress-view-emails-tempstyle.php](classes/views/class-sendpress-view-emails-tempstyle.php)
- [class-sendpress-view-emails-tempclone.php](classes/views/class-sendpress-view-emails-tempclone.php)
- [class-sendpress-view-emails-tempdelete.php](classes/views/class-sendpress-view-emails-tempdelete.php)
- [class-sendpress-view-emails-systememailcreate.php](classes/views/class-sendpress-view-emails-systememailcreate.php)
- [class-sendpress-view-emails-systememailedit.php](classes/views/class-sendpress-view-emails-systememailedit.php)

**Fixes Applied**: Post IDs, user IDs, template IDs, post titles, and URLs properly escaped.

---

### 8. Base View Class XSS

**File Modified**: [class-sendpress-view.php](classes/views/class-sendpress-view.php)

**Fixes Applied**:
- `home_url()` output escaped with `esc_url()`
- `SendPress_Admin::link()` outputs escaped with `esc_url()`
- `SENDPRESS_VERSION` constant escaped with `esc_html()`
- `select()` function outputs escaped with `esc_attr()` and `esc_html()`

---

### 9. Frontend Shortcode XSS

**Files Modified**:
- [class-sendpress-shortcode-manage.php](classes/class-sendpress-shortcode-manage.php)
- [class-sendpress-sc-forms.php](classes/sc/class-sendpress-sc-forms.php)

**Description**: Subscriber management shortcodes displayed on frontend needed escaping.

**Fixes Applied**: List IDs, subscriber emails, statuses, and dates properly escaped.

---

### 10. Queue Table XSS

**File Modified**: [class-sendpress-queue-table.php](classes/class-sendpress-queue-table.php)

**Fixes Applied**: Email addresses and subscriber IDs in admin tables now properly escaped.

---

### 11. Overview Dashboard XSS

**File Modified**: [class-sendpress-view-overview.php](classes/views/class-sendpress-view-overview.php)

**Fixes Applied**: Recent subscriber emails in dashboard widgets now escaped.

---

## Escaping Functions Used

| Function | Purpose | Example Use |
|----------|---------|-------------|
| `esc_html()` | Escape text for HTML context | `echo esc_html( $text );` |
| `esc_attr()` | Escape for HTML attributes | `value="<?php echo esc_attr( $value ); ?>"` |
| `esc_url()` | Escape URLs | `href="<?php echo esc_url( $url ); ?>"` |
| `esc_textarea()` | Escape for textarea content | `<?php echo esc_textarea( $content ); ?>` |
| `intval()` | Cast to integer | `value="<?php echo intval( $id ); ?>"` |
| `sanitize_text_field()` | Sanitize text input | `$clean = sanitize_text_field( $input );` |
| `sanitize_key()` | Sanitize database column names | `$column = sanitize_key( $column );` |
| `wp_unslash()` | Remove magic quotes from input | `wp_unslash( $_POST['field'] )` |
| `$wpdb->prepare()` | Prepare SQL statements | `$wpdb->prepare( "SELECT * FROM table WHERE id = %d", $id )` |

---

## Testing Recommendations

### Manual Testing Checklist

1. **Settings Pages**
   - [ ] Navigate to SendPress > Settings > Account
   - [ ] Navigate to SendPress > Settings > Shared Settings
   - [ ] Navigate to SendPress > Settings > Styles
   - [ ] Navigate to SendPress > Settings > Widgets/Forms
   - [ ] Verify all settings save and display correctly

2. **Subscriber Management**
   - [ ] View subscriber list
   - [ ] Edit a subscriber
   - [ ] Import CSV file
   - [ ] Create/edit mailing list

3. **Email Creation**
   - [ ] Create new email
   - [ ] Edit email template
   - [ ] Send test email
   - [ ] Schedule email

4. **Queue Management**
   - [ ] View queue
   - [ ] Cancel scheduled email

5. **Frontend Shortcodes**
   - [ ] Test `[sendpress_form]` subscription form
   - [ ] Test `[sendpress_manage]` subscription management

---

## Compliance Notes

These fixes align with:
- WordPress VIP coding standards for output escaping
- OWASP XSS Prevention Cheat Sheet guidelines
- WordPress Plugin Security best practices
- PHP 8.1+ compatibility (deprecated function removal)

---

## Patchstack Reported Vulnerabilities (Fixed)

### 1. Cross-Site Request Forgery (CSRF) in Subscribe Form (CVSS 4.3)

**Severity**: Medium
**Type**: CSRF (Unauthenticated)
**Description**: The AJAX subscription endpoint `subscribe_to_list()` lacked nonce verification, allowing attackers to forge subscription requests on behalf of users.

**Files Modified**:
- [class-sendpress-ajax-loader.php](classes/class-sendpress-ajax-loader.php)
- [sendpress.php](sendpress.php)
- [sendpress.signup.js](js/sendpress.signup.js)
- [class-sendpress-sc-forms.php](classes/sc/class-sendpress-sc-forms.php)
- [class-sendpress-sc-signup.php](classes/sc/class-sendpress-sc-signup.php)
- [class-sendpress-signup-shortcode-old.php](classes/class-sendpress-signup-shortcode-old.php)

**Fix Applied**:
```php
// 1. Added nonce to frontend script localization (sendpress.php):
'nonce' => wp_create_nonce( 'sendpress_public_subscribe' )

// 2. JavaScript sends nonce with AJAX request (sendpress.signup.js):
signup['spnonce'] = sendpress.nonce;

// 3. Server-side verification (class-sendpress-ajax-loader.php):
$nonce = SPNL()->validate->_string('spnonce');
if ( ! wp_verify_nonce( $nonce, 'sendpress_public_subscribe' ) ) {
    echo json_encode( array( 'success' => false, 'error' => 'Security check failed.' ) );
    die();
}

// 4. Non-AJAX forms also protected with nonce field:
wp_nonce_field( 'sendpress-form-post', 'sp' );
```

---

### 2. Broken Access Control in API Bounce/Cron Endpoints (CVSS 5.3)

**Severity**: Medium
**Type**: Broken Access Control (Unauthenticated)
**Description**: The `bounce` and `cron` API endpoints allowed unauthenticated access, enabling attackers to:
- Mark any email address as bounced (effectively unsubscribing them)
- Trigger cron jobs on demand (potential DoS vector)

**File Modified**: [class-sendpress-api.php](classes/class-sendpress-api.php)

**Fix Applied**:
```php
// Before (vulnerable) - no auth required:
case 'bounce':
case 'cron':
    $this->is_valid_request = true;
    break;

// After (secure) - webhook secret verification:
case 'bounce':
case 'cron':
    $webhook_secret = SendPress_Option::get( 'webhook_secret' );
    $provided_secret = isset( $wp_query->query_vars['token'] ) ? $wp_query->query_vars['token'] : '';
    
    if ( ! empty( $webhook_secret ) && ! hash_equals( $webhook_secret, $provided_secret ) ) {
        $this->invalid_auth();
        return;
    }
    
    $this->is_valid_request = true;
    break;
```

**Configuration Note**: Administrators should set a `webhook_secret` option to secure these endpoints. Until configured, the endpoints will allow access for backward compatibility.

---

### 3. Additional XSS Fixes (CVSS 5.9 & 7.1)

**Severity**: Medium to High
**Description**: Additional output escaping applied to subscription forms and other frontend outputs.

**Files Modified** (attribute escaping added):
- [class-sendpress-sc-forms.php](classes/sc/class-sendpress-sc-forms.php) - `data-form-id`, `redirect`, `formid` attributes
- [class-sendpress-signup-shortcode-old.php](classes/class-sendpress-signup-shortcode-old.php) - `redirect` attribute
- [class-sendpress-sc-signup.php](classes/sc/class-sendpress-sc-signup.php) - `redirect` attribute

**Fix Pattern Applied**:
```php
// Before:
echo '<input type="hidden" name="redirect" value="'.$redirect_page.'" />';

// After:
echo '<input type="hidden" name="redirect" value="' . esc_attr($redirect_page) . '" />';
```

---

## Version History

| Version | Date | Changes |
|---------|------|---------|
| 1.24.8.21 | 2025-01-14 | Patchstack security fixes - CSRF, Broken Access Control, additional XSS |
| 1.24.8.20 | 2025-01-14 | Security patch - 40 files modified, 3 WPScan CVEs fixed, 34+ additional vulnerabilities fixed |

