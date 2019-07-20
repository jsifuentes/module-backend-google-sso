<?php

namespace Sifuen\BackendGoogleSso\Model\Config\Source;

use Magento\Authorization\Model\Acl\Role\Group as RoleGroup;
use Magento\Authorization\Model\ResourceModel\Role\Grid\Collection;
use Magento\Authorization\Model\ResourceModel\Role\Grid\CollectionFactory;
use Magento\Authorization\Model\Role;
use Magento\Framework\Option\ArrayInterface;

/**
 * Class Roles
 * @package Sifuen\BackendGoogleSso\Model\Config\Source
 */
class Roles implements ArrayInterface
{
    /**
     * @var CollectionFactory
     */
    protected $collectionFactory;

    /**
     * Roles constructor.
     * @param CollectionFactory $collectionFactory
     */
    public function __construct(
        CollectionFactory $collectionFactory
    )
    {
        $this->collectionFactory = $collectionFactory;
    }

    /**
     * @return array
     */
    public function toOptionArray()
    {
        /** @var Collection $collection */
        $collection = $this->collectionFactory->create();

        $collection->addFieldToFilter('role_type', RoleGroup::ROLE_TYPE);

        $result = [];

        /** @var Role $item */
        foreach ($collection->getItems() as $item) {
            $result[] = ['value' => $item->getId(), 'label' => $item->getRoleName()];
        }

        return $result;
    }
}