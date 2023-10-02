<?php
namespace ImpreseeAI\ImpreseeVisualSearch\Block;


class RegisterSearchResultsBlock extends \Magento\Framework\View\Element\Template
{
    public function getParsedSearchAttributes(){
        $params = $this->getRequest()->getParams();
        $url_data = '';
        foreach ($params as $key => $value) {
            if (gettype($value) == 'array')
            {
                $url_data .= '&'.urlencode($key).'='.urlencode(join('|', $value));
            }
            else
            {
                $url_data .= '&'.urlencode($key).'='.urlencode($value);
            }
            
        }
        return $url_data;
    } 
}