<?php

namespace Paulgsepulveda\MonologTeamsWorkflow;

use Monolog\Level;
use Monolog\Logger;
use Psr\Log\LoggerInterface;

class TeamsLogChannel
{
    public function __invoke(array $config): LoggerInterface
    {

        $handlers = [
            new TeamsWorkflowLogHandler(
                $config['url'],
                $config['source_name'],
                $config['source_url'],
                $config['level'] ?? Level::Debug,
                true
            ),
        ];

        return new Logger('app', $handlers);
    }
}
