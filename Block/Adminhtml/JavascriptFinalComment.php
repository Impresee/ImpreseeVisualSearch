<?php
/**
 *   Display client code in system configuration page
 */
namespace ImpreseeAI\ImpreseeVisualSearch\Block\Adminhtml;

use Magento\Config\Block\System\Config\Form\Field;
use Magento\Framework\Data\Form\Element\AbstractElement;
use Magento\Backend\Block\Template\Context;
use ImpreseeAI\ImpreseeVisualSearch\Helper\Codes as CodesHelper;

class JavascriptFinalComment extends Field
{
    /**
     *  To fetch client code from db
     * @var CodesHelper
     */
    public $codesHelper;
    /**
     * To fetch base url of the client server
     * @var StoreManagerInterface
     */
    protected $_storeManagerInterface;
    /**
     * Id of current store
     * @var int
     */
    protected $_storeId;
    /**
     * @param Context
     * @param ImpreseeAI\ImpreseeVisualSearch\Helper\Codes
     */
    public function __construct(
        Context $context,
        CodesHelper $CodesHelper,
        \Magento\Framework\App\Request\Http $request
    ) {
        $this->_storeId = (int) $request->getParam('store', 0);
        parent::__construct($context);
        $this->codesHelper = $CodesHelper;
        $this->_storeManagerInterface = $this->_storeManager;
    }
    /**
     * Display the help comment
     * @param AbstractElement
     * @return string
     */
    public function render(AbstractElement $element)
    {
        $html =    '<span><ul><li><h4>contact us: <a target="_blank" href="mailto:support@impresee.com">support@impresee.com</a></h4></li></ul></span>';
        return $html;
    }
}
