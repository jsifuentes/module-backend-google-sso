<?php

namespace Sifuen\BackendGoogleSso\Helper;

use Sifuen\BackendGoogleSso\Model\Config\Source\EmailMatchingSystem;

/**
 * Class EmailMatching
 * @package Sifuen\BackendGoogleSso\Helper
 */
class EmailMatching
{
    /**
     * @var Config
     */
    protected $configHelper;

    /**
     * EmailMatching constructor.
     * @param Config $configHelper
     */
    public function __construct(
        Config $configHelper
    )
    {
        $this->configHelper = $configHelper;
    }

    /**
     * @param $email
     * @return bool
     */
    public function isEmailAllowed($email)
    {
        $system = $this->configHelper->getEmailMatchingSystem();

        switch ($system) {
            case EmailMatchingSystem::IN_DOMAIN:
                return $this->isEmailInAllowedDomains($email);
            case EmailMatchingSystem::IN_LIST:
                return $this->isEmailInAllowedList($email);
            case EmailMatchingSystem::REGEX:
                return $this->doesEmailMatchRegex($email);
            default:
                return false;
        }
    }

    /**
     * @param $email
     * @return bool
     */
    protected function isEmailInAllowedDomains($email)
    {
        $domain = substr(strrchr($email, "@"), 1);
        return in_array($domain, $this->configHelper->getAllowedDomains());
    }

    /**
     * @param $email
     * @return bool
     */
    protected function isEmailInAllowedList($email)
    {
        return in_array($email, $this->configHelper->getAllowedEmails());
    }

    /**
     * @param $email
     * @return bool
     */
    protected function doesEmailMatchRegex($email)
    {
        $expression = $this->configHelper->getAllowedRegex();

        /**
         * If the expression is false-y, we definitely don't want to execute it
         * and inadvertently allow someone into the backend.
         */
        if (!$expression) {
            return false;
        }

        return !!preg_match($expression, $email);
    }
}