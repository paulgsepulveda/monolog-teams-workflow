<?php

namespace Metamorfer\MonologTeamsWorkflow;

use Monolog\Formatter\FormatterInterface;
use Monolog\Level;
use Monolog\LogRecord;
use PHPUnit\Util\Color;

class TeamsFormatter implements FormatterInterface
{
    private ?string $source_name;
    private ?string $source_url;

    public function format(LogRecord $record): array
    {
        return [
            'type' => 'message',
            'attachments' => [
                [
                    'contentType' => 'application/vnd.microsoft.card.adaptive',
                    'content' => [
                        'type' => 'AdaptiveCard',
                        'body' => [
                            [
                                'type' => 'TextBlock',
                                'text' => $this->source_name . ' - ' . $this->source_url,
                                'isSubtle' => true,
                            ],
                            [
                                'type' => 'TextBlock',
                                'text' => $record->level->getName(),
                                'size' => 'Large',
                                'color' => $this->getColor($record->level),
                            ],
                            [
                                'type' => 'TextBlock',
                                'text' => $record->message,
                                'separator' => true,
                            ]
                        ],
                        '$schema' => 'http://adaptivecards.io/schemas/adaptive-card.json'
                    ]
                ]
            ]
        ];
    }

    public function formatBatch(array $records): array
    {
        foreach ($records as $key => $record) {
            $records[$key] = $this->format($record);
        }

        return $records;
    }

    public function setSource(string $name, string $url): void
    {
        $this->source_name = $name;
        $this->source_url = $url;
    }

    private function getColor(Level $level): string
    {
        return match($level) {
            Level::Debug, Level::Info => 'accent',
            Level::Notice, Level::Warning => 'warning',
            Level::Error, Level::Critical, Level::Alert, Level::Emergency => 'attention',
        };
    }
}
