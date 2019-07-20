<?php
/**
 * Copyright © 2013-2017 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Sifuen\BackendGoogleSso\Block\Adminhtml;

/**
 * Class SetPassword
 * @package Sifuen\BackendGoogleSso\Block\Adminhtml
 */
class SetPassword extends \Magento\Backend\Block\Widget\Form\Container
{
    protected function _construct()
    {
        $this->_controller = 'adminhtml';
        $this->_blockGroup = 'Sifuen_BackendGoogleSso';
        $this->_mode = 'setPassword';

        parent::_construct();

        $this->buttonList->update('save', 'label', __('Set Password'));
        $this->buttonList->remove('delete');
        $this->buttonList->remove('back');
        $this->buttonList->remove('reset');
    }

    /**
     * @return \Magento\Framework\Phrase
     */
    public function getHeaderText()
    {
        return __("Set Your Password");
    }
}
