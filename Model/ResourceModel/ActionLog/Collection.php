<?php

namespace Sifuen\BackendGoogleSso\Model\ResourceModel\ActionLog;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use Sifuen\BackendGoogleSso\Model\ActionLog;
use Sifuen\BackendGoogleSso\Model\ResourceModel\ActionLog as ActionLogResourceModel;

/**
 * Class Collection
 * @package Sifuen\BackendGoogleSso\Model\ResourceModel\ActionLog
 */
class Collection extends AbstractCollection
{
    /**
     * @return void
     */
    protected function _construct()
    {
        $this->_init(ActionLog::class, ActionLogResourceModel::class);
    }
}
