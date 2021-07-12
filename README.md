# Postal_MailProvider_For_WHMCS
This module helps WHMCS to use the HTTP API to send emails to clients via [Postal](https://github.com/postalhq/postal) Mail Server

# Requirements
1. WHMCS 8.0 or higher
2. PHP 7.1 or higher

# Installation
1. Download the source code from the [latest release](https://github.com/DMIT-Inc/Postal_MailProvider_For_WHMCS/releases/latest).
2. Upload module source code to `/yourwhmcsdir/modules/mail/`
3. Go to your WHMCS Admin, then go to `System Settings->General Settings->Mail`.
4. Click `Configure Mail Provider` and switch the `Mail Provider` to `PostalMail`.
5. Fill in the URL of your Postal server with the `https://` prefix, For example `https://yourserver.com`
6. Fill in your Postal HTTP API key and click `Test Configuration`. If there are no errors, Postal will send an email to the current administrator.
7. You can Save it at this time.

# License
[MIT-LICENCE](https://github.com/DMIT-Inc/Postal_MailProvider_For_WHMCS/blob/main/LICENSE)
