<?php

namespace Sifuen\BackendGoogleSso\Block\Adminhtml\SetPassword;

use Magento\Backend\Block\Widget\Form\Generic;
use Magento\Framework\Data\Form as DataForm;
use Magento\Framework\Exception\LocalizedException;

/**
 * Class Form
 * @package Sifuen\BackendGoogleSso\Block\Adminhtml\SetPassword
 */
class Form extends Generic
{
    /**
     * @return $this
     * @throws LocalizedException
     */
    protected function _prepareForm()
    {
        $this->_initFormValues();

        // Prepare form
        /** @var DataForm $form */
        $form = $this->_formFactory->create([
            'data' => [
                'id' => 'edit_form',
                'class' => 'setpassword_form',
                'use_container' => true,
                'method' => 'post'
            ]
        ]);

        $form->setAction($this->_urlBuilder->getUrl('*/*/save'));

        $fieldset = $form->addFieldset(
            'base_fieldset',
            [
                'legend' => __('Set Your Password')
            ]
        );

        $fieldset->addField('instructions', 'note', ['text' => $this->getChildHtml('instructions')]);

        $fieldset->addField(
            'password',
            'password',
            [
                'name' => 'password',
                'label' => 'New Password',
                'id' => 'customer_pass',
                'title' => 'New Password',
                'class' => 'input-text admin__control-text required-entry _required',
                'required' => true
            ]
        );
        $fieldset->addField(
            'confirm_password',
            'password',
            [
                'name' => 'confirm_password',
                'label' => 'Confirm New Password',
                'id' => 'confirmation',
                'title' => 'Confirm New Password',
                'class' => 'input-text admin__control-text required-entry _required',
                'required' => true
            ]
        );

        $this->setForm($form);

        return parent::_prepareForm();
    }
}
