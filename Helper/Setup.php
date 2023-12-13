<?php
/**
 *  Functions to get impresee codes
 */
namespace ImpreseeAI\ImpreseeVisualSearch\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;

class Setup extends AbstractHelper
{
    private $_isDebug;
    /**
     * General constructor.
     * @param Context $context
     */
    public function __construct(Context $context)
    {
        parent::__construct($context);
        $this->_isDebug = FALSE;
    }

    public function getIsDebug()
    {
        return $this->_isDebug;
    }
}
