<?php
namespace ImpreseeAI\ImpreseeVisualSearch\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;

class ImpreseeConfigSaveAfter extends ImpreseeCatalogObserver
{
    public function execute(Observer $observer)
    {
        try {
            $uuid = $this->getImpreseeCatalogUuid();
            $accessToken = $this->getImpreseeAccessToken();

            $postData = ['accessToken' => $accessToken];
            $this->doRequest("updateAccessToken/{$uuid}", endPointFinal:"about/", jsonBody:$postData);
        } catch (\Exception $e) {
            $this->_logger->debug($e->getMessage());
        }
    }
}

