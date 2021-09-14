<?php
/**
 * Add our "similarity" sort criteria to result sort options
 */
namespace ImpreseeAI\ImpreseeVisualSearch\Plugin\Model;

use Magento\Framework\App\Request\Http;

class Config
{
  /**
   * To fetch route
   * @var Magento\Framework\App\Request\Http
   */
    protected $_request;

  /**
   * Constructor
   * @param Magento\Framework\App\Request\Http
   */
    public function __construct(Http $request)
    {
        $this->_request = $request;
    }
  /**
   * Adding custom option to result sort options
   * @param \Magento\Catalog\Model\Config $catalogConfig
   * @param [] $options
   * @return []
   */
    public function afterGetAttributeUsedForSortByArray(
        \Magento\Catalog\Model\Config $catalogConfig,
        $options
    ) {
        return $options;
    }
}
