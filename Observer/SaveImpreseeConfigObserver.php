<?php
/**
 *   Used to register purchases in Impresee's servers
 *   provided by Impresee
 */
namespace ImpreseeAI\ImpreseeVisualSearch\Observer;
use ImpreseeAI\ImpreseeVisualSearch\ImpreseeObserver;

class SaveImpreseeConfigObserver extends ImpreseeObserver
{
    const API_REQUEST_URI = '';
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        try {
            $impresee_app = $this->_codesHelper->getImpreseeUuid(\Magento\Store\Model\ScopeInterface::SCOPE_STORE);
            $consumer_key = $this->_codesHelper->getConsumerKey(\Magento\Store\Model\ScopeInterface::SCOPE_STORE);
            $consumer_secret = $this->_codesHelper->getConsumerSecret(\Magento\Store\Model\ScopeInterface::SCOPE_STORE);
            $access_token = $this->_codesHelper->getAccessToken(\Magento\Store\Model\ScopeInterface::SCOPE_STORE);
            $access_token_secret = $this->_codesHelper->getAccessTokenSecret(\Magento\Store\Model\ScopeInterface::SCOPE_STORE);
            if (!$impresee_app || !$access_token) return;
            
            $response = $this->doJsonRequest(static::API_REQUEST_ENDPOINT . $impresee_app, array(
                'consumer_key' => $consumer_key,
                'consumer_secret' => $consumer_secret,
                'access_token' => $access_token,
                'access_token_secret' => $access_token_secret
            ));
            $status = $response->getStatusCode(); // 200 status code
            $responseBody = $response->getBody();
            $responseContent = $responseBody->getContents(); // here you will have the API response in JSON format

        } catch (\Exception $e) {
            $this->_logger->debug($e->getMessage());
        }
    }

}