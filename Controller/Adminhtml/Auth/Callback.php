<?php

namespace Sifuen\BackendGoogleSso\Controller\Adminhtml\Auth;

use League\OAuth2\Client\Provider\GoogleUser;
use League\OAuth2\Client\Provider\ResourceOwnerInterface;
use Magento\Backend\App\AbstractAction;
use Magento\Backend\App\Action;
use Magento\Backend\Model\Auth;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Data\Collection\ModelFactory;
use Magento\Framework\Exception\AuthenticationException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Math\Random;
use Magento\User\Model\User;
use Magento\User\Model\UserFactory;
use Sifuen\BackendGoogleSso\Helper\Config;
use Sifuen\BackendGoogleSso\Helper\EmailMatching;
use Sifuen\BackendGoogleSso\Helper\Google;
use Sifuen\BackendGoogleSso\Model\AuthenticationStrategy;
use Psr\Log\LoggerInterface;

/**
 * Class Callback
 * @package Sifuen\BackendGoogleSso\Controller\Adminhtml\Auth
 */
class Callback extends AbstractAction
{
    /**
     * @var array
     */
    protected $_publicActions = ['callback'];

    /**
     * @var Config
     */
    protected $configHelper;

    /**
     * @var Google
     */
    protected $googleHelper;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * @var Random
     */
    protected $random;

    /**
     * @var UserFactory
     */
    protected $userFactory;

    /**
     * @var Auth
     */
    protected $auth;

    /**
     * @var ModelFactory
     */
    protected $modelFactory;

    /**
     * @var AuthenticationStrategy
     */
    protected $authenticationStrategy;

    /**
     * @var EmailMatching
     */
    protected $emailMatchingHelper;

    /**
     * @var Auth\Session
     */
    protected $authSession;

    /**
     * Callback constructor.
     * @param Action\Context $context
     * @param Config $configHelper
     * @param Google $googleHelper
     * @param LoggerInterface $logger
     * @param Random $random
     * @param UserFactory $userFactory
     * @param Auth $auth
     * @param ModelFactory $modelFactory
     * @param AuthenticationStrategy $authenticationStrategy
     * @param EmailMatching $emailMatchingHelper
     * @param Auth\Session $authSession
     */
    public function __construct(
        Action\Context $context,
        Config $configHelper,
        Google $googleHelper,
        LoggerInterface $logger,
        Random $random,
        UserFactory $userFactory,
        Auth $auth,
        ModelFactory $modelFactory,
        AuthenticationStrategy $authenticationStrategy,
        EmailMatching $emailMatchingHelper,
        Auth\Session $authSession
    )
    {
        parent::__construct($context);

        $this->configHelper = $configHelper;
        $this->googleHelper = $googleHelper;
        $this->logger = $logger;
        $this->random = $random;
        $this->userFactory = $userFactory;
        $this->auth = $auth;
        $this->modelFactory = $modelFactory;
        $this->authenticationStrategy = $authenticationStrategy;
        $this->emailMatchingHelper = $emailMatchingHelper;
        $this->authSession = $authSession;
    }

    /**
     * @return bool
     */
    protected function _isAllowed()
    {
        return true;
    }

    /**
     * @return ResponseInterface|ResultInterface
     */
    public function execute()
    {
        $savedAuthState = $this->_getSession()->getOAuthState();
        $requestAuthState = $this->getRequest()->getParam('state');
        $code = $this->getRequest()->getParam('code');

        if (!$this->configHelper->isConfigured() || !$savedAuthState || $requestAuthState !== $savedAuthState || !$code) {
            // Redirect back to login page.
            return $this->redirectToLogin();
        }

        try {
            /** @var GoogleUser $owner */
            $owner = $this->getOwnerFromCode($code);

            try {
                $this->attemptSignIn($owner);
            } catch (NoSuchEntityException $e) {
                if ($this->configHelper->isAutoRegisterActive() && $this->emailMatchingHelper->isEmailAllowed($owner->getEmail())) {
                    $this->createNewAdminUser($owner);
                    $this->attemptSignIn($owner);
                }
            }

            if ($this->auth->isLoggedIn()) {
                return $this->redirectToDashboard();
            }

            $this->messageManager->addErrorMessage(__('You did not sign in correctly or your account is temporarily disabled.'));
            return $this->redirectToLogin();
        } catch (AuthenticationException $e) {
            $this->messageManager->addErrorMessage(__($e->getMessage()));
            return $this->redirectToLogin();
        } catch (\Exception $e) {
            $this->messageManager->addErrorMessage(__("An error occurred while signing you in. Please try again later."));
            $this->logger->critical("Error while attempting to sign a user in via Google SSO.", ['e' => $e]);

            // Redirect back to login page.
            return $this->redirectToLogin();
        }
    }

    /**
     * @return ResponseInterface
     */
    protected function redirectToDashboard()
    {
        return $this->_redirect($this->_backendUrl->getStartupPageUrl());
    }

    /**
     * @return ResponseInterface
     */
    protected function redirectToLogin()
    {
        return $this->_redirect('*/auth/login');
    }

    /**
     * @param $code
     * @return ResourceOwnerInterface
     */
    protected function getOwnerFromCode($code)
    {
        $provider = $this->googleHelper->getGoogleOAuthProvider([
            'options' => [
                'useOidcMode' => true
            ]
        ]);

        $token = $provider->getAccessToken('authorization_code', [
            'code' => $code
        ]);

        /** @var GoogleUser $ownerDetails */
        return $provider->getResourceOwner($token);
    }

    /**
     * @param $owner
     * @throws NoSuchEntityException
     * @throws \Exception
     * @throws \Magento\Framework\Exception\AuthenticationException
     */
    protected function attemptSignIn($owner)
    {
        /** @var GoogleUser $owner */
        /** @var User $user */
        $user = $this->userFactory->create()->load($owner->getEmail(), 'email');

        if (!$user->getId()) {
            throw new NoSuchEntityException();
        }

        $this->authenticationStrategy->setStrategy(AuthenticationStrategy::GOOGLE_SSO);
        $this->auth->login($owner->getEmail(), "password");

        /**
         * This is to take advantage of some native functionality Magento has
         * when a user needs to change their password.
         */
        if ($user->getData('needs_to_set_password')) {
            $user->setData('force_new_password', true)
                ->save();
        }
    }

    /**
     * @param ResourceOwnerInterface $owner
     * @throws \Exception
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function createNewAdminUser(ResourceOwnerInterface $owner)
    {
        /** @var GoogleUser $owner */

        /** @var User $user */
        $user = $this->userFactory->create();

        /**
         * The '1' at the end of the password is just to satisfy
         * the need for both alphabetic and numeric characters.
         */
        $password = $this->random->getRandomString(20) . '1';

        $user->setData([
            'username' => $owner->getEmail(),
            'email' => $owner->getEmail(),
            'firstname' => $owner->getFirstName(),
            'lastname' => $owner->getLastName(),
            'password' => $password,
            'interface_locale' => $this->configHelper->getDefaultLocale(),
            'is_active' => 1,
            'role_id' => $this->configHelper->getDefaultRole(),
            'needs_to_set_password' => 1
        ]);

        $user->save();
    }
}