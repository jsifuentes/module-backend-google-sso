<?php

namespace Sifuen\BackendGoogleSso\App\Action\Plugin;

use Magento\Backend\App\AbstractAction;
use Magento\Backend\App\BackendAppList;
use Magento\Backend\Model\Auth;
use Magento\Backend\Model\UrlInterface;
use Magento\Framework\App\ActionFlag;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\Result\RedirectFactory;
use Magento\Framework\Data\Form\FormKey\Validator;
use Magento\Framework\Message\ManagerInterface;
use Magento\User\Model\UserFactory;
use Sifuen\BackendGoogleSso\Helper\ActionLogger;
use Sifuen\BackendGoogleSso\Helper\Config;

/**
 * Class Authentication
 * @package Sifuen\BackendGoogleSso\App\Action\Plugin
 */
class Authentication extends \Magento\Backend\App\Action\Plugin\Authentication
{
    /**
     * @var Config
     */
    protected $configHelper;
    /**
     * @var UserFactory
     */
    private $userFactory;
    /**
     * @var ActionLogger
     */
    private $actionLogger;

    /**
     * Authentication constructor.
     * @param Auth $auth
     * @param UrlInterface $url
     * @param ResponseInterface $response
     * @param ActionFlag $actionFlag
     * @param ManagerInterface $messageManager
     * @param UrlInterface $backendUrl
     * @param RedirectFactory $resultRedirectFactory
     * @param BackendAppList $backendAppList
     * @param Validator $formKeyValidator
     * @param Config $configHelper
     * @param UserFactory $userFactory
     * @param ActionLogger $actionLogger
     */
    public function __construct(
        Auth $auth,
        UrlInterface $url,
        ResponseInterface $response,
        ActionFlag $actionFlag,
        ManagerInterface $messageManager,
        UrlInterface $backendUrl,
        RedirectFactory $resultRedirectFactory,
        BackendAppList $backendAppList,
        Validator $formKeyValidator,
        Config $configHelper,
        UserFactory $userFactory,
        ActionLogger $actionLogger
    )
    {
        parent::__construct(
            $auth, $url, $response, $actionFlag, $messageManager, $backendUrl, $resultRedirectFactory,
            $backendAppList, $formKeyValidator
        );

        $this->configHelper = $configHelper;
        $this->userFactory = $userFactory;
        $this->actionLogger = $actionLogger;
    }

    /**
     * @param AbstractAction $subject
     * @param \Closure $proceed
     * @param RequestInterface $request
     * @return mixed
     */
    public function aroundDispatch(
        AbstractAction $subject,
        \Closure $proceed,
        RequestInterface $request
    )
    {
        if ($this->configHelper->isActive()) {
            $this->_openActions = array_merge($this->_openActions, ['callback']);
        }

        return parent::aroundDispatch($subject, $proceed, $request);
    }

    /**
     * @param RequestInterface $request
     * @return bool
     */
    protected function _performLogin(RequestInterface $request)
    {
        $postLogin = $request->getPost('login');
        $username = isset($postLogin['username']) ? $postLogin['username'] : '';

        $user = $this->userFactory->create();
        $user->loadByUsername($username);

        if ($user->getId() && !$user->getData('can_use_password_authentication')) {
            $this->actionLogger->create(__(
                "User '%1' tried to login with a password, but password authentication is disabled.",
                $user->getUserName()
            ), $user->getId());

            $this->messageManager->addErrorMessage(__(
                'You cannot login to this admin account using a password because password authentication is disabled.'
            ));
            $request->setParam('messageSent', true);
            return false;
        }

        return parent::_performLogin($request);
    }
}