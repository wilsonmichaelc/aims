<?php
  // Database config
  define('DB_HOST', 'localhost');
  define('DB_NAME', '_ims');
  define('DB_USER', 'ims');
  define('DB_PASS', 'password');

  // Domain and Email Config
  define('DOMAIN', 'yourdomain.com');
  define('EMAIL', 'you@' . DOMAIN);

  // Cookies
  define('COOKIE_RUNTIME', 1209600);
  define('COOKIE_DOMAIN', '.' . DOMAIN);
  define('COOKIE_SECRET_KEY', 'secretkeycookie');
  define('HASH_COST_FACTOR', 5);

  // SMTP
  define('EMAIL_USE_SMTP', true);
  define('EMAIL_SMTP_HOST', 'relay.' . DOMAIN);
  define('EMAIL_SMTP_AUTH', false);
  define('EMAIL_SMTP_USERNAME', EMAIL);
  define('EMAIL_SMTP_PASSWORD', '');
  define('EMAIL_SMTP_PORT', 25);
  define('EMAIL_SMTP_ENCRYPTION', '');

  // Password Reset
  define('EMAIL_PASSWORDRESET_URL', 'https://' . DOMAIN . '/passwordReset.php');
  define('EMAIL_PASSWORDRESET_FROM', EMAIL);
  define('EMAIL_PASSWORDRESET_FROM_NAME', 'Instrument Management System');
  define('EMAIL_PASSWORDRESET_SUBJECT', 'Password reset for Instrument Management System');
  define('EMAIL_PASSWORDRESET_CONTENT', 'Please click on this link to reset your password:');

  // Verification Email
  define('EMAIL_VERIFICATION_URL', 'https://' . DOMAIN . '/register.php');
  define('EMAIL_VERIFICATION_FROM', EMAIL);
  define('EMAIL_VERIFICATION_FROM_NAME', 'SOD Maldi');
  define('EMAIL_VERIFICATION_SUBJECT', 'Account Activation for Instrument Management System');
  define('EMAIL_VERIFICATION_CONTENT', 'Please click on this link to activate your account:');

  // New FFS Request Email
  define('EMAIL_NEW_FFS_FROM', EMAIL);
  define('EMAIL_NEW_FFS_FROM_NAME', 'Instrument Management System');
  define('EMAIL_NEW_FFS_FROM_SUBJECT', 'New Fee For Service Request');

  // Invoice line
  define('INVOICE_REPLY_LINE', 'Please review your invoice and report any discrepancies to ADMINISTRATOR at '.EMAIL.' within');
  define('INVOICE_DAYS_TO_RESPOND', '3');

  // Who to email about new fee-for-service requests
  define('EMAIL_FFS_ALERTS', serialize (array (EMAIL)));

  // Ownser info
  define('MAINTAINER_NAME', 'System Administrator');
  define('MAINTAINER_EMAIL', EMAIL);
  define('COPYRIGHT', date('Y').'Your Company');
?>
