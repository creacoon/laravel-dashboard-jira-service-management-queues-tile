# laravel-dashboard-jira-service-management-queues-tile
# Installation
1. Require package via composer
2. Place the required values in the `.env` file.
3. Place the tile component in your dashboard.
4. Schedule the command in the `app/console/kernel.php`

## Composer
You can install the package via composer:

```bash
composer require creacoon/laravel-dashboard-jira-queue-tile
```

## Env file
Place this in the `.env` file.
```dotenv
JIRA_HOST=
JIRA_AUTHENTICATION="basic_token"
JIRA_USER=
JIRA_API_TOKEN=
JSM_VISIBLE_QUEUES=1,2,3
```
### Config file
In the `dashboard` config file, you must add this configuration in the `tiles` key.

```php
// in config/dashboard.php
return [
    // ...
'jira_service_queues' => [
    'jira_host' => env('JIRA_HOST'),
    'jira_user' => env('JIRA_USER'),
    'jira_api_token' => env('JIRA_API_TOKEN'),
    'visible_queues' => explode(',', env('JSM_VISIBLE_QUEUES')),
    'resolved_today_jql' => 'project = SV AND statusCategory IN (Done) AND updated > startOfDay() AND updated < endOfDay()',
],
```
## Tile component

In your dashboard view you use the `livewire:jira-service-queue-tile` component.

```html
<x-dashboard>
    <livewire:jira-service-queue-tile position="a2" refresh-interval="30"/>
</x-dashboard>
```
## Schedule command
In `app\Console\Kernel.php` you should schedule the following commands.
```php
protected function schedule(Schedule $schedule)
{
    // ...
           $schedule->command(FetchDataFromJiraQueueCommand::class)->everyFiveMinutes();
}
```
## Customizing the view
If you want to customize the view used to render this tile, run this command:
```bash
php artisan vendor:publish --tag="dashboard-jira-queue-tile-views"
```
## Testing

``` bash
composer test
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.
## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
