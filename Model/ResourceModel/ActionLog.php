<?php

namespace Sifuen\BackendGoogleSso\Model\ResourceModel;

/**
 * Class ActionLog
 * @package Sifuen\BackendGoogleSso\Model\ResourceModel
 */
class ActionLog extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    /**
     * ActionLog constructor.
     * @param \Magento\Framework\Model\ResourceModel\Db\Context $context
     * @param null $resourcePrefix
     */
    public function __construct(
        \Magento\Framework\Model\ResourceModel\Db\Context $context,
        $resourcePrefix = null
    ) {
        parent::__construct($context, $resourcePrefix);
    }

    /**
     * @return void
     */
    protected function _construct()
    {
        $this->_init(\Sifuen\BackendGoogleSso\Model\ActionLog::TABLE_NAME, 'entity_id');
    }
}
