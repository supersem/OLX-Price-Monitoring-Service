# OLX Price Monitoring Service

## Overview

This service monitors prices of ads on OLX and sends email notifications when there are price changes. Users can subscribe to specific ads by providing their email addresses and confirming their subscription. The service checks the prices periodically and informs the subscribers when there is a change.

## Features

- Monitor prices for specific ads on OLX.
- Email notifications sent when price changes occur.
- User subscription confirmation via email.
- Supports multiple subscribers for the same ad.
- Runs periodic checks using Laravel's job queue and scheduling.
- Implements Docker for environment consistency.
- Unit and feature tests with PHPUnit and Laravel testing framework.

## Requirements

- PHP 8.2 or higher
- Composer
- Docker & Docker Compose (with Laravel Sail for local development)
- MySQL or MariaDB database

## Installation

1. Clone the repository:
    ```bash
    git clone https://github.com/supersem/OLX-Price-Monitoring-Service.git
    cd OLX-Price-Monitoring-Service
    ```

2. Install dependencies using Composer:
    ```bash
    composer install
    ```

3. Set up your environment file:
    ```bash
    cp .env.example .env
    ```

4. Update the `.env` file with your database, mail, and other environment settings.

5. Build and start the Docker containers using Laravel Sail:
    ```bash
    ./vendor/bin/sail up -d
    ```

6. Run migrations to create the required database tables:
    ```bash
    ./vendor/bin/sail artisan migrate
    ```

## Usage

### Subscribe to an Ad

1. A user can subscribe to an OLX ad by submitting the ad URL and email.
2. The user will receive an email to verify the subscription. Clicking the verification link will confirm the subscription.
3. After confirming, the user will receive notifications when the price changes.

### Monitoring Prices

The price monitoring process runs automatically using the Laravel Scheduler.
Add the following cron job to your server:
```bash
* * * * * cd /path-to-your-project && ./vendor/bin/sail artisan schedule:run >> /dev/null 2>&1
```
You can trigger the price monitor manually with the following command:
```bash
./vendor/bin/sail artisan monitor:prices
```

### Running the Queue Worker

To ensure the scheduled job runs and emails are sent, you need to run the queue worker:
```bash
./vendor/bin/sail artisan queue:work
```

### API Endpoints
```bash
POST /api/subscribe - Subscribe to an ad
Body: { "email": "user@example.com", "ad_url": "https://olx.com/ad-url" }
GET /api/confirm/{token} - Confirm subscription via token
```

### Testing

Unit and feature tests are implemented for the main components of the service. To run the tests:

```bash
./vendor/bin/sail test
```




