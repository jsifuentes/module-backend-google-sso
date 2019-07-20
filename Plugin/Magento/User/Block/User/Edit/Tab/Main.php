<?php

namespace Sifuen\BackendGoogleSso\Plugin\Magento\User\Block\User\Edit\Tab;
use Magento\Framework\Registry;

/**
 * Class Main
 * @package Sifuen\BackendGoogleSso\Plugin\Magento\User\Block\User\Edit\Tab
 */
class Main
{
    /**
     * @var Registry
     */
    private $registry;

    /**
     * Main constructor.
     * @param Registry $registry
     */
    public function __construct(
        Registry $registry
    )
    {
        $this->registry = $registry;
    }

    /**
     * @param \Magento\User\Block\User\Edit\Tab\Main $subject
     */
    public function beforeGetFormHtml(
        \Magento\User\Block\User\Edit\Tab\Main $subject
    )
    {
        /** @var $model \Magento\User\Model\User */
        $model = $this->registry->registry('permissions_user');
        $fieldName = 'can_use_password_authentication';

        $form = $subject->getForm();
        $fieldset = $form->getElement('base_fieldset');
        $fieldset->addField(
            $fieldName,
            'select',
            [
                'name' => $fieldName,
                'label' => __('Password Login Allowed'),
                'id' => $fieldName,
                'title' => __('Can use password authentication'),
                'class' => 'input-select',
                'options' => ['1' => __('Yes'), '0' => __('No')]
            ]
        );

        $form->addValues([
            $fieldName => $model->getData($fieldName)
        ]);
    }
}