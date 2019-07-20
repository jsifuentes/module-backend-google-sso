<?php

namespace Sifuen\BackendGoogleSso\Setup;

use Magento\Framework\DB\Ddl\Table;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\Setup\UpgradeSchemaInterface;
use Sifuen\BackendGoogleSso\Model\ActionLog;

/**
 * Class UpgradeSchema
 * @package Sifuen\BackendGoogleSso\Setup
 */
class UpgradeSchema implements  UpgradeSchemaInterface
{
    /**
     * @param SchemaSetupInterface $setup
     * @param ModuleContextInterface $context
     * @throws \Zend_Db_Exception
     */
    public function upgrade(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();

        if (version_compare($context->getVersion(), '1.0.1', '<')) {
            $this->addUserNoPasswordColumn($setup);
        }

        if (version_compare($context->getVersion(), '1.0.2', '<')) {
           $this->addUserCreatedByGoogleSsoColumn($setup);
        }

        if (version_compare($context->getVersion(), '1.0.2', '<')) {
            $this->createGoogleSsoActionLogTable($setup);
        }

        if (version_compare($context->getVersion(), '1.0.3', '<')) {
            $this->addCanUsePasswordAuthenticationColumn($setup);
        }

        $setup->endSetup();
    }

    /**
     * @param SchemaSetupInterface $setup
     */
    public function addUserNoPasswordColumn(SchemaSetupInterface $setup)
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

    /**
     * @param SchemaSetupInterface $setup
     */
    public function addUserCreatedByGoogleSsoColumn(SchemaSetupInterface $setup)
    {
        $tableAdmins = $setup->getTable('admin_user');

        $setup->getConnection()->addColumn(
            $tableAdmins,
            'created_by_google_sso',
            [
                'type' => Table::TYPE_SMALLINT,
                'nullable' => false,
                'default' => 0,
                'comment' => 'Whether this account was created by Google SSO'
            ]
        );
    }

    /**
     * @param SchemaSetupInterface $setup
     * @throws \Zend_Db_Exception
     */
    public function createGoogleSsoActionLogTable(SchemaSetupInterface $setup)
    {
        if (!$setup->tableExists(ActionLog::TABLE_NAME)) {
            $table = $setup->getConnection()->newTable(ActionLog::TABLE_NAME)
                ->addColumn(
                    'entity_id',
                    Table::TYPE_INTEGER,
                    null,
                    [
                        'identity' => true,
                        'unsigned' => true,
                        'nullable' => false,
                        'primary' => true
                    ],
                    'Entity ID'
                )->addColumn(
                    'user_id',
                    Table::TYPE_INTEGER,
                    null,
                    [
                        'unsigned' => true,
                        'nullable' => true
                    ],
                    'Admin User ID'
                )->addColumn(
                    'message',
                    Table::TYPE_TEXT,
                    256,
                    [
                        'nullable' => true
                    ],
                    'Message'
                )->addColumn(
                    'created_at',
                    Table::TYPE_TIMESTAMP,
                    null,
                    [
                        'nullable' => false,
                        'default' => Table::TIMESTAMP_INIT
                    ],
                    'Created At'
                );

            $setup->getConnection()->createTable($table);
        }
    }

    /**
     * @param SchemaSetupInterface $setup
     */
    public function addCanUsePasswordAuthenticationColumn(SchemaSetupInterface $setup)
    {
        $tableAdmins = $setup->getTable('admin_user');

        $setup->getConnection()->addColumn(
            $tableAdmins,
            'can_use_password_authentication',
            [
                'type' => Table::TYPE_SMALLINT,
                'nullable' => false,
                'default' => 1,
                'comment' => 'Whether this account can use password authentication'
            ]
        );
    }
}