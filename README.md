# Backend user IP verification

* Prevent users from logging into the backend until they verify their IP address
* Each backend user is required to verify each IP address they login from using a random generated 128 character token
* Token are regenerated and overwritten with each login attempt effectively expiring any previously emailed tokens

## How it works
The plugin listens for the backend user login event to insert its functionality

## How to use
Functionality is switched off by default and is turned on from the backend system settings. This allows developers to bypass the verification check when not required.

## Customising the email
An email template is registered by the plugin which allows you to fully customise the design and content.

## Future updates
Feature requests and contributions are welcome via [Github](https://github.com/daykinandstorey/oc-ip-verify)

## Bugs
Please open an issue on [Github](https://github.com/daykinandstorey/oc-ip-verify)