# Backend Google SSO

This Magento 2 module allows you to sign in to your backend user account using your Google account. You can also have backend accounts created automatically when someone logs in with their Google account.

https://sifuen.com/magento-2/using-google-account-sign-in-magento-backend/

# Requirements

This module requires the following:

* PHP >= 5.5
* Magento >= 2.0

# Installation

This module can be installed using [Composer](https://getcomposer.org/).

```
composer require sifuen/module-backend-google-sso
```

```
{
    "require": {
        "sifuen/module-backend-google-sso": "^1.0"
    }
}
```

After the module is downloaded, run the following command in your Magento root:

```
php bin/magento module:enable Sifuen_BackendGoogleSso
php bin/magento setup:upgrade
```

# Set up

## Setting up your OAuth credentials

Visit the [Google Developer Console](https://console.developers.google.com/) to create your OAuth credentials.

1. Visit the "Credentials" section
2. Click "Create credentials"
3. Choose "OAuth Client ID"
4. Choose the "Web application" application type.
5. When asked for "Authorized Redirect URIs", enter the URL given below, substituting `https://example.com/admin/` with your 
Magento 2 administrator page.

**Redirect URI:** `https://example.com/admin/google_sso/auth/callback`

## Configuring the module

![1](https://i.imgur.com/26ird7f.png)

After you set up your OAuth 2 credentials, login to your Magento backend and navigate to **Stores > Configuration > Advanced > Admin > Google SSO**.

1. Change the module's status to "Enabled".
2. Enter the Client ID you created in the Google Developer Console.
3. Enter the Client Secret you created in the Google Developer Console.
4. Click `Save`

After your configuration saves, clear your store's cache if necessary. The next time you visit the Magento 2 backend login page, you should see a button to `Sign in with Google`.

### Auto Registration

You can enable "Auto Registration" which allows users with access to certain Google accounts to automatically have admin accounts created for them. This is especially useful for agencies who have multiple employees logging into a client's admin. 

You have the following available filter options:

* Only allowing specific e-mails
* Allowing any e-mails in a domain
* Allowing any e-mails that match a regular expression


### Disabling password authentication

You can disable the ability for a user to login to their admin account using a password automatically if they are registered using Google SSO by toggling the setting "Allow Auto-Registered Users To Use Password Login".
This is especially useful if you are in an work environment where when an employee loses their work e-mail, they should no longer be allowed to access client admin accounts.

This can also be toggled on a per-user basis, even if the user was not originally created using the auto-register feature.

![2](https://i.imgur.com/6fr3ZcW.png)


# Concerns

**I want to know when my employees are logging in or auto-registering**

All login actions using Google SSO are logged to the table `google_sso_action_log` in the database. The following actions are logged:

* When a user logs in via Google SSO
* When a user attempts to login using Google SSO, but is denied access
* When a user is auto-registered via Google SSO
* When a user attempts to use password authentication when password authentication is disabled.

**I don't want my employees to be able to access their admin accounts when they leave the company**

You can disable the ability for an admin user to use password authentication, which forces them to use Google SSO to sign in.
Once an employee's work e-mail is revoked, they will no longer be able to access their admin account. If the employee even attempts
to sign into their account using password authentication, that action is logged in the `google_sso_action_log` table


# Contributing

Please refer to [CONTRIBUTING.md](CONTRIBUTING.md)