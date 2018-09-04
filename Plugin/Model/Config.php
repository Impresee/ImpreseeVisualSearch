<?php
/**
 * Add our "similarity" sort criteria to result sort options
 */
namespace Impresee\ImpreseeVisualSearch\Plugin\Model;

use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\App\Action\Action;

class Config
{
  /**
   * Store Context
   * @var Magento\Store\Model\StoreManagerInterface
   */
    protected $_storeManager;
  /**
   * To fetch route
   * @var Magento\Framework\App\Action\Action
   */
    protected $_action;
  /**
   * Constructor
   * @param Magento\Store\Model\StoreManagerInterface
   * @param Magento\Framework\App\Action\Action
   */
    public function __construct(
        StoreManagerInterface $storeManager,
        Action $action
    ) {
        $this->_storeManager = $storeManager;
        $this->_action = $action;
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
        $store = $this->_storeManager->getStore();
        $customOption = [];
        unset($options['position']);
        $route = $this->_action->getRequest()->getRouteName();
        if ((strcmp($route, 'impresee')) == 0) {
            $customOption['similarity'] = 'Similarity';
        }
        $options = array_merge($customOption, $options);
        return $options;
    }
}
