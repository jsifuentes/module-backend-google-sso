<?php

namespace Sifuen\BackendGoogleSso\Model\Config\Source;

use Magento\Framework\Option\ArrayInterface;

/**
 * Class EmailMatchingSystem
 * @package Sifuen\BackendGoogleSso\Model\Config\Source
 */
class EmailMatchingSystem implements ArrayInterface
{
    const IN_DOMAIN = 'in_domain';
    const IN_LIST = 'in_list';
    const REGEX = 'regex';

    /**
     * @return array
     */
    public function toOptionArray()
    {
        return [
            self::IN_DOMAIN => __('Any e-mail address in a domain'),
            self::IN_LIST => __('Specific e-mail addresses'),
            self::REGEX => __('Regular Expression')
        ];
    }
}