# monolog-teams-workflow

Monolog Handler for sending messages to Microsoft Teams channels using Workflow / Power Automate. I put this together based on [monolog-microsoft-teams](https://github.com/cmdisp/monolog-microsoft-teams) after Microsoft retired Office 365 connectors.

## Install

```bash
$ composer require paulgsepulveda/monolog-teams-workflow
```

## Configuring Workflow

* Install the app `Power Automate` on the Teams channel that is intended to receive the log messages
* Create a new Workflow using the template *Post to a channel when a webhook request is received*.
* The JSON formatting assumes that the *Select an output from previous steps* field will remain set to `attachments`
* The JSON formatting assumes that the *Adaptive Card* field under **Post your own adaptive card** will remain set to `content`
* After saving the workflow, the **When a Teams webhook request is received** card will prove the `HTTP POST URL` value to use for the incoming webhook.

## Usage

```php
$logger = new \Monolog\Logger('app');
$logger->pushHandler(new \Paulgsepulveda\MonologTeamsWorkflow\TeamsWorkflowLogHandler('INCOMING_WEBHOOK_URL', \Monolog\Level::Error));

$logger->error('Error message');
```

## Usage with Laravel/Lumen framework (5.6+)

Create a [custom channel](https://laravel.com/docs/master/logging#creating-custom-channels)

`config/logging.php`

```php
'teams' => [
    'driver' => 'custom',
    'via' => \Paulgsepulveda\MonologTeamsWorkflow\TeamsLogChannel::class,
    'level' => 'error',
    'url' => 'INCOMING_WEBHOOK_URL',
    'source_name' => env('APP_NAME'),
    'source_url' => env('APP_URL'),
],
```
`source_name` and `source_url` are included since the receiving channel may be receiving notifications from more than one site.

Send an error message to the teams channel:

```php
Log::channel('teams')->error('Error message');
```

You can also add `teams` to the default `stack` channel so all errors are automatically send to the `teams` channel.

```php
'channels' => [
    'stack' => [
        'driver' => 'stack',
        'channels' => ['single', 'teams'],
    ],
],
```

## Unit testing · PhpUnit

The tests require a valid incoming Workflow webhook url. Follow the steps above in *Configuring Workflow* to generate this. To provide this URL to PhpUnit, copy `phpunit.xml.dist` to `phpunit.xml`and set the URL in the `<php>` section. Make sure to not commit your local *phpunit.xml* into the repo!

```xml
<php>
    <env name="TEAMS_INCOMING_WEBHOOK_URL" value="..." />
</php>
```

Run the tests on the command line:

```bash
$ composer test
```

## License

monolog-teams-workflow is available under the MIT license. See the LICENSE file for more info.
