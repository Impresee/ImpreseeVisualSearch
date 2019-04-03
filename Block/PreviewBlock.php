<?php
/**
 *   Usefull to preview changes added on config
 */
namespace ImpreseeAI\ImpreseeVisualSearch\Block;

use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;

class PreviewBlock extends Template
{
  /**
   * @var StoreManagerInterface
   */
    protected $_storeManagerInterface;
    public function __construct(
        Context $context
    ) {
        parent::__construct($context);
        $this->_storeManagerInterface = $this->_storeManager;
    }
}
