# **Magento 2 Keep Contacts GraphQL Extension** #

## Description ##

This extension will add GraphQL support to the Keep Contacts Magento 2 module. Adding support to save contacts when the contact us form called via the contactUs mutation with GraphQL.

Also adding support for get previously saved contacts list for a specific customer which has an account in the store.

## Features ##

- Adding support to save contact while using the contactUs GraphQL mutation
- Adding support to receive list of contacts for registered customers via GraphQL query

It is a separate module that does not change the default Magento files.

Support:
- Magento Community Edition  2.4.x

- Adobe Commerce 2.4.x

## Installation ##

** Important! Always install and test the extension in your development environment, and not on your live or production server. **

1. Backup Your Data
   Backup your store database and whole Magento 2 directory.

2. Install Keep Contacts Magento 2 extension. Please see:
   https://github.com/AttilaSagiDev/keep-contacts/releases

3. Enable extension
   Please use the following commands in your Magento 2 console:

   ```
   bin/magento module:enable Space_KeepContactsGraphQl

   bin/magento setup:upgrade 
   ```

## Change Log ##

Version 1.0.0 - May 2, 2024
- Compatibility with Magento Community Edition  2.4.x

- Compatibility with Adobe Commerce 2.4.x

## Support ##

If you have any questions about the extension, please contact with me.

## License ##

MIT License.
