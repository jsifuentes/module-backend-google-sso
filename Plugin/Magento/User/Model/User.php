<?php

namespace Sifuen\BackendGoogleSso\Plugin\Magento\User\Model;

use Magento\Framework\Event\Manager;
use Magento\Framework\Exception\AuthenticationException;
use Sifuen\BackendGoogleSso\Model\AuthenticationStrategy;

/**
 * Class User
 * @package Sifuen\BackendGoogleSso\Plugin\Magento\User\Model
 */
class User
{
    /**
     * @var AuthenticationStrategy
     */
    protected $authenticationStrategy;
    /**
     * @var Manager
     */
    protected $eventManager;

    /**
     * User constructor.
     * @param AuthenticationStrategy $authenticationStrategy
     * @param Manager $eventManager
     */
    public function __construct(
        AuthenticationStrategy $authenticationStrategy,
        Manager $eventManager
    )
    {
        $this->authenticationStrategy = $authenticationStrategy;
        $this->eventManager = $eventManager;
    }

    /**
     * @param \Magento\User\Model\User $subject
     * @param callable $proceed
     * @param $username
     * @param $password
     * @return bool
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function aroundAuthenticate(\Magento\User\Model\User $subject, callable $proceed, $username, $password)
    {
        if ($this->authenticationStrategy->getStrategy() !== AuthenticationStrategy::GOOGLE_SSO) {
            return $proceed($username, $password);
        }

        try {
            /**
             * Since we can't guarantee that the `username` here will point to a valid user, we just won't
             * fire it.

                $this->eventManager->dispatch(
                    'admin_user_authenticate_before',
                    ['username' => $username, 'user' => $subject]
                );
             */

            $subject->load($username, 'email');

            $this->eventManager->dispatch(
                'admin_user_authenticate_after',
                [
                    'username' => $subject->getUserName(),
                    'password' => $password,
                    'user' => $subject,
                    'result' => !!$subject->getId()
                ]
            );
        } catch (\Magento\Framework\Exception\LocalizedException $e) {
            $subject->unsetData();
            throw $e;
        }

        return !!$subject->getId();
    }
}