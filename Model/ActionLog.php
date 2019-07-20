<?php

namespace Sifuen\BackendGoogleSso\Model;

use Magento\Framework\Model\AbstractModel;
use Sifuen\BackendGoogleSso\Model\ResourceModel\ActionLog as ActionLogResourceModel;

/**
 * Class ActionLog
 * @package Sifuen\BackendGoogleSso\Model
 */
class ActionLog extends AbstractModel
{
    const TABLE_NAME = 'google_sso_action_log';
    const USER_ID = 'user_id';
    const MESSAGE = 'message';
    const CREATED_AT = 'created_at';

    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(ActionLogResourceModel::class);
    }

    /**
     * @return string
     */
    public function getUserId()
    {
        return $this->getData(self::USER_ID);
    }

    /**
     * @param string $userId
     * @return ActionLog
     */
    public function setUserId($userId)
    {
        return $this->setData(self::USER_ID, $userId);
    }

    /**
     * @return string
     */
    public function getMessage()
    {
        return $this->getData(self::MESSAGE);
    }

    /**
     * @param string $message
     * @return ActionLog
     */
    public function setMessage($message)
    {
        return $this->setData(self::MESSAGE, $message);
    }

    /**
     * @return string
     */
    public function getCreatedAt()
    {
        return $this->getData(self::CREATED_AT);
    }

    /**
     * @param string $createdAt
     * @return ActionLog
     */
    public function setCreatedAt($createdAt)
    {
        return $this->setData(self::CREATED_AT, $createdAt);
    }
}