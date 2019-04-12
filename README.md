# ImpreseeVisualSearch

This is a Magento® 2 Module to connect your store with Impresee

## Installation

#### Install from GitHub

1. Download zip package by clicking "Clone or Download" and select "Download ZIP" from the dropdown.

2. Create an app/code/ImpreseeAI/ImpreseeVisualSearch directory in your Magento® 2 root folder.

3. Extract the contents from the "ImpreseeVisualSearch-master" zip and copy or upload everything to:       
   app/code/ImpreseeAI/ImpreseeVisualSearch

4. Run the following commands from the Magento® 2 root folder to install and enable the module:

   ```
   php bin/magento module:enable ImpreseeAI_ImpreseeVisualSearch
   php bin/magento setup:upgrade
   php bin/magento cache:clean
   ```

5. If Magento® is running in production mode, deploy static content with the following command: 

   ```
   php bin/magento setup:static-content:deploy
   ```
#### Marketplace

This extension will also be available on the Magento® Marketplace once approved.
   
