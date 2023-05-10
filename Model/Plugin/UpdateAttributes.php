<?php
namespace ImpreseeAI\ImpreseeVisualSearch\Model\Plugin;

use Magento\Catalog\Model\Product\Action\Interceptor;

class UpdateAttributes
{

    protected $logger;

    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    /**
     * @param Interceptor $interceptor
     * @param \Closure $closure
     * @param $productIds
     * @param $attrData
     * @param $storeId
     * @return Interceptor
     */
    public function aroundUpdateAttributes(
        Interceptor $interceptor,
        \Closure $closure,
        $productIds,
        $attrData,
        $storeId
    ) {
        //execute the original method and remember the result;
        $result = $closure($productIds, $attrData, $storeId);
        //do something with $productIds here
        $this->logger->debug(print_r($attrData, true));
        return $result;
    }
}