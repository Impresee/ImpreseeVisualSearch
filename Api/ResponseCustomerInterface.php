<?php

namespace ImpreseeAI\ImpreseeVisualSearch\Api;

interface ResponseCustomerInterface
{
    const DATA_ID = 'id';
    const DATA_NAME = 'name';
    const DATA_EMAIL = 'email';

    /**
     * @return int
     */
    public function getId();

    /**
     * @return string
     */
    public function getName();

    /**
     * @return string
     */
    public function getEmail();

}
