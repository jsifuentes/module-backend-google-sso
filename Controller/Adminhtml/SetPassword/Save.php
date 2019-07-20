<?php

namespace Sifuen\BackendGoogleSso\Controller\Adminhtml\SetPassword;

use Magento\Backend\App\Action;
use Magento\Backend\Model\Auth\Session;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Validator\Exception as ValidatorException;

/**
 * Class Save
 * @package Sifuen\BackendGoogleSso\Controller\Adminhtml\SetPassword
 */
class Save extends Action
{
    /**
     * @var Session
     */
    protected $authSession;

    /**
     * Save constructor.
     * @param Action\Context $context
     * @param Session $authSession
     */
    public function __construct(
        Action\Context $context,
        Session $authSession
    )
    {
        parent::__construct($context);

        $this->authSession = $authSession;
    }

    /**
     * @return ResponseInterface|ResultInterface
     */
    public function execute()
    {
        $request = $this->getRequest();
        $user = $this->authSession->getUser();

        if (!$user->getData('needs_to_set_password')) {
            return $this->_redirect($this->_backendUrl->getStartupPageUrl());
        }

        if (!$this->_formKeyValidator->validate($request) || !$user) {
            return $this->_redirect('*/*/index');
        }

        $password = $request->getParam('password');
        $confirmPassword = $request->getParam('confirm_password');

        if (!is_string($password) || strlen($password) == 0 || $password !== $confirmPassword) {
            $this->messageManager->addErrorMessage(__("The passwords you entered do not match."));
            return $this->redirectToIndex();
        }

        try {
            $user->addData([
                'password' => $password,
                'force_new_password' => false,
                'needs_to_set_password' => false
            ])->save();
        } catch (ValidatorException $e) {
            $messages = $e->getMessages();
            $this->messageManager->addMessages($messages);

            return $this->redirectToIndex();
        } catch (LocalizedException $e) {
            if ($e->getMessage()) {
                $this->messageManager->addError($e->getMessage());
            }

            return $this->redirectToIndex();
        } catch (\Exception $e) {
            $this->messageManager->addErrorMessage(__('A critical error occurred while setting your password.'));

            return $this->redirectToIndex();
        }

        $this->messageManager->addSuccessMessage(__('Your password has been successfully saved.'));
        return $this->_redirect($this->_backendUrl->getStartupPageUrl());
    }

    /**
     * @return ResponseInterface
     */
    public function redirectToIndex()
    {
        return $this->_redirect('*/*/index');
    }
}