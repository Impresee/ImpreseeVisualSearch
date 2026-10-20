<?php
namespace ImpreseeAI\ImpreseeVisualSearch\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;

class ImpreseeConfigSaveAfter extends ImpreseeCatalogObserver
{
    public function execute(Observer $observer)
    {
        try {
            // Read the scope being saved, rather than the current admin store.
            $event = $observer->getEvent();
            $scope = ScopeConfigInterface::SCOPE_TYPE_DEFAULT;
            $scopeId = 0;
            if ($event->getStore()) {
                $scope = ScopeInterface::SCOPE_STORE;
                $scopeId = $event->getStore();
            } elseif ($event->getWebsite()) {
                $scope = ScopeInterface::SCOPE_WEBSITE;
                $scopeId = $event->getWebsite();
            }

            $context = ['scope' => $scope, 'scope_id' => $scopeId];
            $uuid = $this->_codesHelper->getImpreseeCatalogUuid($scope, $scopeId);
            $accessToken = $this->_codesHelper->getAccessToken($scope, $scopeId);
            if (!$uuid || !$accessToken) {
                $this->_logger->error(
                    'Impresee token update skipped: catalog UUID or access token is missing in the saved scope.',
                    $context
                );
                return;
            }

            $postData = ['accessToken' => $accessToken];
            $this->_logger->info('Sending Impresee access token update.', $context);
            $response = $this->doRequest("updateAccessToken/{$uuid}", 'about/', $postData);
            $this->_logger->info('Impresee access token update completed.', array_merge(
                $context,
                ['status' => $response->getStatusCode()]
            ));
        } catch (\Exception $e) {
            $this->_logger->debug($e->getMessage());
        }
    }
}

