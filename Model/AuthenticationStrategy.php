<?php

namespace Sifuen\BackendGoogleSso\Model;

use Magento\Framework\DataObject;

/**
 * Class IdentityVerificationRequiredState
 * @package Sifuen\BackendGoogleSso\Model
 *
 * @method getStrategy()
 * @method setStrategy(string $strategy)
 */
class AuthenticationStrategy extends DataObject
{
    const NATIVE = 'native';
    const GOOGLE_SSO = 'google_sso';
}