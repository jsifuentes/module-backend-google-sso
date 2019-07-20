<?php

namespace Sifuen\BackendGoogleSso\Helper;

use League\OAuth2\Client\Provider\Google as GoogleOAuthProvider;
use League\OAuth2\Client\Provider\GoogleFactory as GoogleOAuthProviderFactory;
use Magento\Backend\Model\Url;
use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;

/**
 * Class Google
 * @package Sifuen\BackendGoogleSso\Helper
 */
class Google extends AbstractHelper
{
    /**
     * @var Config
     */
    protected $configHelper;

    /**
     * @var Url
     */
    protected $backendUrlBuilder;

    /**
     * @var GoogleOAuthProvider
     */
    protected $googleProvider;

    /**
     * @var GoogleOAuthProviderFactory
     */
    protected $googleProviderFactory;

    /**
     * Google constructor.
     * @param Context $context
     * @param Config $configHelper
     * @param Url $backendUrlBuilder
     * @param GoogleOAuthProviderFactory $googleProviderFactory
     */
    public function __construct(
        Context $context,
        Config $configHelper,
        Url $backendUrlBuilder,
        GoogleOAuthProviderFactory $googleProviderFactory
    )
    {
        parent::__construct($context);

        $this->configHelper = $configHelper;
        $this->backendUrlBuilder = $backendUrlBuilder;
        $this->googleProviderFactory = $googleProviderFactory;
    }

    /**
     * @param array $arguments
     * @return GoogleOAuthProvider
     */
    public function getGoogleOAuthProvider(array $arguments = [])
    {
        if (!$this->googleProvider) {
            $this->googleProvider = $this->googleProviderFactory->create(array_merge_recursive([
                'options' => [
                    'clientId' => $this->configHelper->getClientId(),
                    'clientSecret' => $this->configHelper->getClientSecret(),
                    'redirectUri' => $this->getSsoRedirectUri()
                ]
            ], $arguments));
        }

        return $this->googleProvider;
    }

    /**
     * @return string
     */
    public function getSsoRedirectUri()
    {
        return $this->backendUrlBuilder->getUrl('google_sso/auth/callback', ['_nosecret' => true]);
    }
}