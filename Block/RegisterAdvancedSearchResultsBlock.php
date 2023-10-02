<?php
namespace ImpreseeAI\ImpreseeVisualSearch\Block;
use ImpreseeAI\ImpreseeVisualSearch\Block\RegisterSearchResultsBlock;


class RegisterAdvancedSearchResultsBlock extends RegisterSearchResultsBlock
{
    public function getScreenName(){
        return $this->getRequest()->getFullActionName();
    } 
}