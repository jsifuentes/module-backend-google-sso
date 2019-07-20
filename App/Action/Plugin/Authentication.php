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
        Config $configHelper
    )
    {
        parent::__construct(
            $auth, $url, $response, $actionFlag, $messageManager, $backendUrl, $resultRedirectFactory,
            $backendAppList, $formKeyValidator
        );

        $this->configHelper = $configHelper;
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
}