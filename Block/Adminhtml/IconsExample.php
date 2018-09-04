<?php
/**
 *   Display Impresee default icons
 */
namespace Impresee\ImpreseeVisualSearch\Block\Adminhtml;

use Magento\Config\Block\System\Config\Form\Field;
use Magento\Framework\Data\Form\Element\AbstractElement;
use Magento\Backend\Block\Template\Context;

class IconsExample extends Field
{
    /**
     * @param Context
     * @param Impresee\ImpreseeVisualSearch\Helper\Codes
     */
    public function __construct(
        Context $context,
        \Magento\Framework\App\Request\Http $request
    ) {
        parent::__construct($context);
    }
    /**
     * Displays icons comment on config
     * @param AbstractElement
     * @return string
     */
    public function render(AbstractElement $element)
    {
        $html  = '<div class="ignore-validate">';
        $html .= 'We do our best to display the visual search buttons (';
        $html .= '  <img src="https://api.impresee.com/icons/photo1.svg" style="height:15px; width:auto"/>,';
        $html .= '  <img src="https://api.impresee.com/icons/sketch1.svg" style="height:15px; width:auto"/> )';
        $html .= '  next to your search bar but if you wish to customize these buttons, you can disable this option and add your own in the search layout of your theme (form.mini.phtml).';
        $html .= '  Just remember to use the css classes "impresee-sketch-button" and "impresee-photo-button" for the search by sketch and search by photo buttons, respectively.';
        $html .= '</div></br>';
        return $html;
    }
}
