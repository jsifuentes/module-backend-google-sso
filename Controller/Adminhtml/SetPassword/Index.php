<?php

namespace Sifuen\BackendGoogleSso\Controller\Adminhtml\SetPassword;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Backend\Model\Auth\Session;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\View\Result\Page;
use Magento\Framework\View\Result\PageFactory;
use Sifuen\BackendGoogleSso\Helper\Config;

/**
 * Class Index
 * @package Sifuen\BackendGoogleSso\Controller\Adminhtml\SetPassword
 */
class Index extends Action
{
    /**
     * @var array
     */
    protected $_publicActions = ['index'];

    /**
     * @var PageFactory
     */
    protected $resultPageFactory;

    /**
     * @var Config
     */
    protected $configHelper;

    /**
     * @var Session
     */
    protected $authSession;

    /**
     * Index constructor.
     * @param Context $context
     * @param PageFactory $resultPageFactory
     * @param Config $configHelper
     * @param Session $authSession
     */
    public function __construct(
        Context $context,
        PageFactory $resultPageFactory,
        Config $configHelper,
        Session $authSession
    ) {
        parent::__construct($context);

        $this->resultPageFactory = $resultPageFactory;
        $this->configHelper = $configHelper;
        $this->authSession = $authSession;
    }

    /**
     * @return ResponseInterface|ResultInterface|Page
     */
    public function execute()
    {
        if (!$this->authSession->getUser()->getData('needs_to_set_password')) {
            return $this->_redirect($this->_backendUrl->getStartupPageUrl());
        }

        $resultPage = $this->resultPageFactory->create();
        $resultPage->getConfig()->getTitle()->prepend(__('Set Your Password'));

        return $resultPage;
    }
}