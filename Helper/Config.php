<?php

namespace Sifuen\BackendGoogleSso\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\Encryption\EncryptorInterface;

/**
 * Class Config
 * @package Sifuen\BackendGoogleSso\Helper
 */
class Config extends AbstractHelper
{
    const XML_BACKEND_GOOGLE_SSO_STATUS = 'admin/backend_google_sso/status';
    const XML_BACKEND_GOOGLE_SSO_CLIENT_ID = 'admin/backend_google_sso/client_id';
    const XML_BACKEND_GOOGLE_SSO_CLIENT_SECRET = 'admin/backend_google_sso/client_secret';
    const XML_BACKEND_GOOGLE_SSO_AUTO_REGISTER_STATUS = 'admin/backend_google_sso/auto_register_status';
    const XML_BACKEND_GOOGLE_SSO_AUTO_REGISTER_ALLOW_PASSWORD_LOGIN = 'admin/backend_google_sso/auto_register_allow_password_login';
    const XML_BACKEND_GOOGLE_SSO_AUTO_REGISTER_DEFAULT_LOCALE = 'admin/backend_google_sso/auto_register_default_locale';
    const XML_BACKEND_GOOGLE_SSO_AUTO_REGISTER_DEFAULT_ROLE = 'admin/backend_google_sso/auto_register_default_role';
    const XML_BACKEND_GOOGLE_SSO_AUTO_REGISTER_EMAIL_MATCHING_SYSTEM = 'admin/backend_google_sso/auto_register_email_matching_system';
    const XML_BACKEND_GOOGLE_SSO_AUTO_REGISTER_ALLOWED_DOMAINS = 'admin/backend_google_sso/auto_register_email_matching_system_domain_list';
    const XML_BACKEND_GOOGLE_SSO_AUTO_REGISTER_ALLOWED_EMAILS = 'admin/backend_google_sso/auto_register_email_matching_system_emails_list';
    const XML_BACKEND_GOOGLE_SSO_AUTO_REGISTER_ALLOWED_REGEX = 'admin/backend_google_sso/auto_register_email_matching_system_regex';

    /**
     * @var EncryptorInterface
     */
    protected $encryptor;

    /**
     * Config constructor.
     * @param Context $context
     * @param EncryptorInterface $encryptor
     */
    public function __construct(
        Context $context,
        EncryptorInterface $encryptor
    )
    {
        parent::__construct($context);
        $this->encryptor = $encryptor;
    }

    /**
     * @param string $value
     * @return mixed
     */
    public function unserialize($value)
    {
        if ($this->isSerialized($value)) {
            $unserializer = ObjectManager::getInstance()->get(\Magento\Framework\Unserialize\Unserialize::class);
        } else {
            $unserializer = ObjectManager::getInstance()->get(\Magento\Framework\Serialize\Serializer\Json::class);
        }

        return $unserializer->unserialize($value);
    }

    /**
     * Check if value is a serialized string
     *
     * @param string $value
     * @return boolean
     */
    private function isSerialized($value)
    {
        return (boolean) preg_match('/^((s|i|d|b|a|O|C):|N;)/', $value);
    }

    /**
     * @return bool
     */
    public function isActive()
    {
        return !!$this->scopeConfig->getValue(self::XML_BACKEND_GOOGLE_SSO_STATUS);
    }

    /**
     * @return bool
     */
    public function isConfigured()
    {
        return $this->isActive() &&
            $this->getClientId() &&
            $this->getClientSecret();
    }

    /**
     * @return string
     */
    public function getClientId()
    {
        return $this->scopeConfig->getValue(self::XML_BACKEND_GOOGLE_SSO_CLIENT_ID);
    }

    /**
     * @return string
     */
    public function getClientSecret()
    {
        return $this->encryptor->decrypt($this->scopeConfig->getValue(self::XML_BACKEND_GOOGLE_SSO_CLIENT_SECRET));
    }

    /**
     * @return bool
     */
    public function isAutoRegisterActive()
    {
        return $this->scopeConfig->isSetFlag(self::XML_BACKEND_GOOGLE_SSO_AUTO_REGISTER_STATUS);
    }

    /**
     * @return bool
     */
    public function canAutoRegisteredUsersUsePasswordLogin()
    {
        return $this->scopeConfig->isSetFlag(self::XML_BACKEND_GOOGLE_SSO_AUTO_REGISTER_ALLOW_PASSWORD_LOGIN);
    }

    /**
     * @return string
     */
    public function getDefaultLocale()
    {
        return $this->scopeConfig->getValue(self::XML_BACKEND_GOOGLE_SSO_AUTO_REGISTER_DEFAULT_LOCALE) ?: "en_US";
    }

    /**
     * @return mixed
     */
    public function getDefaultRole()
    {
        return $this->scopeConfig->getValue(self::XML_BACKEND_GOOGLE_SSO_AUTO_REGISTER_DEFAULT_ROLE);
    }

    /**
     * @return string
     */
    public function getEmailMatchingSystem()
    {
        return $this->scopeConfig->getValue(self::XML_BACKEND_GOOGLE_SSO_AUTO_REGISTER_EMAIL_MATCHING_SYSTEM);
    }

    /**
     * @return array
     */
    public function getAllowedDomains()
    {
        $emails = $this->unserialize(
            $this->scopeConfig->getValue(self::XML_BACKEND_GOOGLE_SSO_AUTO_REGISTER_ALLOWED_DOMAINS)
        );

        return array_map(function($item) {
            return $item['value'];
        }, $emails);
    }

    /**
     * @return array
     */
    public function getAllowedEmails()
    {
        $emails = $this->unserialize(
            $this->scopeConfig->getValue(self::XML_BACKEND_GOOGLE_SSO_AUTO_REGISTER_ALLOWED_EMAILS)
        );

        return array_map(function($item) {
            return $item['value'];
        }, $emails);
    }

    /**
     * @return string
     */
    public function getAllowedRegex()
    {
        return $this->scopeConfig->getValue(self::XML_BACKEND_GOOGLE_SSO_AUTO_REGISTER_ALLOWED_REGEX);
    }
}