<?php

namespace Metamorfer\MonologTeamsWorkflow;

use Monolog\Formatter\FormatterInterface;
use Monolog\Handler\AbstractProcessingHandler;
use Monolog\Level;
use Monolog\LogRecord;

class TeamsWorkflowLogHandler extends AbstractProcessingHandler
{
    public function __construct(
        private readonly string $url,
        string $source_name,
        string $source_url,
        int|string|Level $level = Level::Debug,
        bool $bubble = true
    ) {
        parent::__construct($level, $bubble);

        $this->setFormatter(new TeamsFormatter());

        if ($this->formatter instanceof TeamsFormatter) {
            $this->formatter->setSource($source_name, $source_url);
        }
    }

    protected function write(LogRecord $record): void
    {
        $json = json_encode($this->teamsMessage($record));

        $ch = curl_init($this->url);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
        curl_setopt($ch, CURLOPT_POSTFIELDS, $json);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 3);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'Content-Length: ' . strlen($json)
        ]);

        curl_exec($ch);
    }

    private function teamsMessage(LogRecord $record): array
    {
        return $this->formatter->format($record);
    }
}
