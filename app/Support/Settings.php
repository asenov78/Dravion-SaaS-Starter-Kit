<?php

namespace App\Support;

/**
 * Typed constants for all Setting keys used in the application.
 * Use instead of bare strings to catch typos at code-read time.
 */
final class Settings
{
    // General
    const APP_NAME         = 'app_name';
    const APP_URL          = 'app_url';
    const TIMEZONE         = 'timezone';
    const DATE_FORMAT      = 'date_format';
    const DEFAULT_LANGUAGE = 'default_language';

    // Mail
    const MAIL_FROM        = 'mail_from';
    const MAIL_FROM_NAME   = 'mail_from_name';
    const MAIL_WELCOME     = 'mail_welcome';
    const ADMIN_EMAIL      = 'admin_email';

    // Auth
    const REGISTRATION     = 'registration';
    const MAINTENANCE      = 'maintenance';

    // Branding
    const LOGO             = 'logo';
    const FOOTER_TEXT      = 'footer_text';
    const FOOTER_COPYRIGHT = 'footer_copyright';
    const HEADER_TAGLINE   = 'header_tagline';
    const BROADCAST_BANNER = 'broadcast_banner';

    // Activity log categories
    const ACTIVITY_LOG_AUTH     = 'activity_log_auth';
    const ACTIVITY_LOG_USERS    = 'activity_log_users';
    const ACTIVITY_LOG_PROFILE  = 'activity_log_profile';
    const ACTIVITY_LOG_SETTINGS = 'activity_log_settings';
}
