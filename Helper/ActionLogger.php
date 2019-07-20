<?php

namespace Sifuen\BackendGoogleSso\Helper;

use Psr\Log\LoggerInterface;
use Sifuen\BackendGoogleSso\Model\ActionLogFactory;

class ActionLogger
{
    /**
     * @var ActionLogFactory
     */
    private $actionLogFactory;
    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * ActionLog constructor.
     * @param ActionLogFactory $actionLogFactory
     * @param LoggerInterface $logger
     */
    public function __construct(
        ActionLogFactory $actionLogFactory,
        LoggerInterface $logger
    )
    {
        $this->actionLogFactory = $actionLogFactory;
        $this->logger = $logger;
    }

    /**
     * @param $message
     * @param null $userId
     * @return \Sifuen\BackendGoogleSso\Model\ActionLog
     */
    public function create($message, $userId = null)
    {
        /** @var \Sifuen\BackendGoogleSso\Model\ActionLog $actionLog */
        $actionLog = $this->actionLogFactory->create();
        $actionLog->setUserId($userId);
        $actionLog->setMessage($message);

        try {
            $actionLog->save();
        } catch (\Exception $e) {
            $this->logger->critical('[Google SSO] Failed to create action log entry.', ['e' => $e]);
        }

        return $actionLog;
    }
}