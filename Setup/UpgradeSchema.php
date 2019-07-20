<?php

namespace Sifuen\BackendGoogleSso\Setup;

use Magento\Framework\DB\Ddl\Table;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\Setup\UpgradeSchemaInterface;

/**
 * Class UpgradeSchema
 * @package Sifuen\BackendGoogleSso\Setup
 */
class UpgradeSchema implements  UpgradeSchemaInterface
{
    /**
     * @param SchemaSetupInterface $setup
     * @param ModuleContextInterface $context
     */
    public function upgrade(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();

        if (version_compare($context->getVersion(), '1.0.1') < 0) {
            $this->createUserNoPasswordColumn($setup, $context);
        }

        $setup->endSetup();
    }

    /**
     * @param SchemaSetupInterface $setup
     * @param ModuleContextInterface $context
     */
    public function createUserNoPasswordColumn(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $tableAdmins = $setup->getTable('admin_user');

        $setup->getConnection()->addColumn(
            $tableAdmins,
            'needs_to_set_password',
            [
                'type' => Table::TYPE_SMALLINT,
                'nullable' => false,
                'default' => 0,
                'comment' => 'Whether this account needs to set their password before they can use their account'
            ]
        );
    }
}