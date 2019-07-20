<?php

namespace Sifuen\BackendGoogleSso\Block\Adminhtml\Form\Field;

use Magento\Config\Block\System\Config\Form\Field\FieldArray\AbstractFieldArray;

/**
 * Class ItemList
 * @package Sifuen\BackendGoogleSso\Block\Adminhtml\Form\Field
 */
class ItemList extends AbstractFieldArray
{
    protected function _prepareToRender()
    {
        $this->addColumn('value', ['label' => __('Value')]);

        $this->_addAfter = false;
        $this->_addButtonLabel = __('Add');
    }
}