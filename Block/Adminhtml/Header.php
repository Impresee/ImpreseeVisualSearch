<?php
/**
 *   Display client code in system configuration page
 */
namespace Impresee\ImpreseeVisualSearch\Block\Adminhtml;

use Magento\Config\Block\System\Config\Form\Field;
use Magento\Framework\Data\Form\Element\AbstractElement;
use Magento\Backend\Block\Template\Context;

class Header extends Field
{

    /**
     * @param Context
     * @param Impresee\ImpreseeVisualSearch\Helper\Codes
     */
    public function __construct(Context $context)
    {
        parent::__construct($context);
    }
    /**
     * Displays header in system config
     * @param AbstractElement
     * @return string
     */
    public function render(AbstractElement $element)
    {
         $html = '<div class="row impreseeHeader">';
         $html .= ' <div class="col-xs-4"><h5><strong>Powered by</strong></h5> <div><a target="_blank" href="https://impresee.com"><img src="';
         $html .= $this->getViewFileUrl('Impresee_ImpreseeVisualSearch::images/ImpreseeLogo.svg').'" style="height:auto"/> </a></div></div>';
         $html .= ' <div class="col-xs-4"><h1><strong>Need help?</strong></h1><p> contact us: <a target="_blank" href="mailto:support@impresee.com">support@impresee.com</a></p></div>';
         $html .= ' <div class="col-xs-4"><h1><strong>Remember: </Strong></h1><p>Set your configuration on each store view</p></div>';
         $html .= '</div>';
         return $html;
    }
}
