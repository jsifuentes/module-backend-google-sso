<?php
/**
 * Copyright © 2013-2017 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Sifuen\BackendGoogleSso\Observer\Backend;

use Magento\Backend\Model\Auth\Session;
use Magento\Backend\Model\UrlInterface;
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\ActionFlag;
use Magento\Framework\App\Request\Http;
use Magento\Framework\AuthorizationInterface;
use Magento\Framework\Event\Observer as EventObserver;
use Magento\Framework\Event\ObserverInterface;

/**
 * Class ForceSetPasswordRedirectObserver
 * @package Sifuen\BackendGoogleSso\Observer\Backend
 */
class ForceSetPasswordRedirectObserver implements ObserverInterface
{
    /**
     * @var AuthorizationInterface
     */
    protected $authorization;

    /**
     * @var UrlInterface
     */
    protected $url;

    /**
     * @var Session
     */
    protected $authSession;

    /**
     * @var ActionFlag
     */
    protected $actionFlag;

    /**
     * ForceSetPasswordRedirectObserver constructor.
     * @param AuthorizationInterface $authorization
     * @param UrlInterface $url
     * @param Session $authSession
     * @param ActionFlag $actionFlag
     */
    public function __construct(
        AuthorizationInterface $authorization,
        UrlInterface $url,
        Session $authSession,
        ActionFlag $actionFlag
    ) {
        $this->authorization = $authorization;
        $this->url = $url;
        $this->authSession = $authSession;
        $this->actionFlag = $actionFlag;
    }

    /**
     * @param EventObserver $observer
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function execute(EventObserver $observer)
    {
        if (!$this->authSession->isLoggedIn() ||
            !$this->authSession->getUser()->getData('needs_to_set_password')) {
            return;
        }

        /** @var Http $request */
        $request = $observer->getEvent()->getRequest();

        if ($request->isAjax()) {
            return;
        }

        $actionList = [
            'google_sso_setpassword_save',
            'google_sso_setpassword_index',
            'adminhtml_auth_logout',
        ];

        if (in_array($request->getFullActionName(), $actionList)) {
            return;
        }

        /** @var Action $controller */
        $controller = $observer->getEvent()->getControllerAction();
        $controller->getResponse()->setRedirect($this->url->getUrl('google_sso/setpassword/index'));

        $this->actionFlag->set('', Action::FLAG_NO_DISPATCH, true);
        $this->actionFlag->set('', Action::FLAG_NO_POST_DISPATCH, true);
    }
}
