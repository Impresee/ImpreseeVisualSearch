<?php

namespace ImpreseeAI\ImpreseeVisualSearch\Plugin\Search;

use ImpreseeAI\ImpreseeVisualSearch\Plugin\Search\ImpreseeSearchBasePlugin;
use Magento\Search\Model\QueryFactory;
use Magento\Framework\App\RequestInterface;
use Psr\Log\LoggerInterface;
use ImpreseeAI\ImpreseeVisualSearch\Helper\Codes as CodesHelper;
use ImpreseeAI\ImpreseeVisualSearch\Helper\Requests as RequestsHelper;

class ImpreseeSearchResultsIndexPlugin extends ImpreseeSearchBasePlugin
{

    public function beforeExecute(\Magento\CatalogSearch\Controller\Result\Index\Interceptor $result)
    {
        return $this->execute();
    }
}