<?php

namespace Sifuen\BackendGoogleSso\Block\Adminhtml;

use League\OAuth2\Client\Provider\Google as GoogleOAuthProvider;
use Magento\Backend\Block\Template;
use Magento\Backend\Block\Template\Context;
use Sifuen\BackendGoogleSso\Helper\Config;
use Sifuen\BackendGoogleSso\Helper\Google;

/**
 * Class Login
 * @package Sifuen\BackendGoogleSso\Block\Adminhtml
 */
class Login extends Template
{
    /**
     * @var
     */
    protected $configHelper;

    /**
     * @var Google
     */
    protected $googleHelper;

    /**
     * @var GoogleOAuthProvider
     */
    protected $googleOAuthProvider;

    /**
     * Login constructor.
     * @param Context $context
     * @param Config $configHelper
     * @param Google $googleHelper
     * @param array $data
     */
    public function __construct(
        Context $context,
        Config $configHelper,
        Google $googleHelper,
        array $data = []
    )
    {
        parent::__construct($context, $data);

        $this->configHelper = $configHelper;
        $this->googleHelper = $googleHelper;
        $this->googleOAuthProvider = $this->googleHelper->getGoogleOAuthProvider();
    }

    /**
     * @return bool
     */
    public function isModuleConfigured()
    {
        return $this->configHelper->isConfigured();
    }

    /**
     * @return string
     */
    public function getSignInUrl()
    {
        $url = $this->googleOAuthProvider->getAuthorizationUrl();

        // Save OAuth state for validation use later
        $this->_backendSession->setOAuthState($this->googleOAuthProvider->getState());

        return $url;
    }
}