<?php

namespace ImpreseeAI\ImpreseeVisualSearch\Api;

interface CustomerRepositoryInterface
{
    /**
     * Return logged in customer.
     *
     * @param string $id
     * @return \ImpreseeAI\ImpreseeVisualSearch\Api\ResponseCustomerInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getLoggedIdCustomer(string $id);

}