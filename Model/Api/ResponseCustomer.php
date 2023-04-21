<?php

namespace ImpreseeAI\ImpreseeVisualSearch\Model\Api;

use ImpreseeAI\ImpreseeVisualSearch\Api\ResponseCustomerInterface;
use Magento\Framework\DataObject;

/**
 * Class ResponseItem
 */
class ResponseCustomer extends DataObject implements ResponseCustomerInterface
{
    public function getId() : int
    {
        return $this->_getData(self::DATA_ID);
    }

    public function getEmail() : string
    {
        return $this->_getData(self::DATA_EMAIL);
    }

    public function getName() : string
    {
        return $this->_getData(self::DATA_NAME);
    }
      /**
     * @param int $id
     * @return $this
     */
    public function setId(string $id) : mixed
    {
        return $this->setData(self::DATA_ID, $id);
    }

    /**
     * @param string $email
     * @return $this
     */
    public function setEmail(string $email) : mixed
    {
        return $this->setData(self::DATA_EMAIL, $email);
    }

    /**
     * @param string $name
     * @return $this
     */
    public function setName(string $name) : mixed
    {
        return $this->setData(self::DATA_NAME, $name);
    }

}
