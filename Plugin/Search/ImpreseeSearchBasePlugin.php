<?php

namespace ImpreseeAI\ImpreseeVisualSearch\Plugin\Search;

use Magento\Framework\App\RequestInterface;
use Psr\Log\LoggerInterface;
use ImpreseeAI\ImpreseeVisualSearch\Helper\Codes as CodesHelper;
use ImpreseeAI\ImpreseeVisualSearch\Helper\Requests as RequestsHelper;

abstract class ImpreseeSearchBasePlugin
{
    const FILTER_SEARCH = 'FILTER_SEARCH';
    protected $_logger;
    protected $_request;
    protected $_requestsHelper;
    protected $_codesHelper;

    public function __construct(
        RequestInterface $request,
        LoggerInterface $logger,
        CodesHelper $codesHelper,
        RequestsHelper $requestsHelper
        )
    {
        $this->_logger = $logger;
        $this->_request = $request;
        $this->_requestsHelper = $requestsHelper;
        $this->_codesHelper = $codesHelper;
    }

    protected function execute()
    {
        try {
            $params = $this->_request->getParams();
            $impresee_code = $this->_codesHelper->getImpreseeUuid(\Magento\Store\Model\ScopeInterface::SCOPE_STORE);
            if (!$impresee_code) return null;
            $parsed_client = $this->_requestsHelper->parseClientData();
            $url_data = 'q='.urlencode($queryText).'&evt='.urlencode(static::FILTER_SEARCH).'&a='.urlencode($this->_codesHelper->getRegisterEventsAction()).'&'.$parsed_client;
            foreach ($params as $key => $value) {
                $url_data .= '&'.urlencode($key).'='.urlencode($value);
            }
            $this->_logger->debug($url_data);
            $this->_requestsHelper->callRegisterEventUrl($impresee_code, $url_data);
        } catch (\Magento\Framework\Exception\LocalizedException $e) {
            $this->_logger->debug($e->getMessage());
        }

        return null;
    }
}