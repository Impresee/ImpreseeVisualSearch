<?php
namespace ImpreseeAI\ImpreseeVisualSearch\Model\Config;

class DisplayButtonOption implements \Magento\Framework\Option\ArrayInterface
{
    public function toOptionArray()
    {
        return [
              ['value' => '1', 'label' => __('Use Icons from Impresee Icon Catalog')],
              ['value' => '0', 'label' => __('Load your own Icons')]
        ];
    }
}
