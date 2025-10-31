# Lottery Bundle

[English](README.md) | [中文](README.zh-CN.md)

[![Latest Version](https://img.shields.io/packagist/v/tourze/lottery-bundle.svg?style=flat-square)](https://packagist.org/packages/tourze/lottery-bundle)  
[![Total Downloads](https://img.shields.io/packagist/dt/tourze/lottery-bundle.svg?style=flat-square)](https://packagist.org/packages/tourze/lottery-bundle)  
[![PHP Version](https://img.shields.io/packagist/php-v/tourze/lottery-bundle.svg?style=flat-square)](https://packagist.org/packages/tourze/lottery-bundle)  
[![License](https://img.shields.io/packagist/l/tourze/lottery-bundle.svg?style=flat-square)](https://packagist.org/packages/tourze/lottery-bundle)  
[![Build Status](https://img.shields.io/github/actions/workflow/status/tourze/php-monorepo/test.yml?style=flat-square)](https://github.com/tourze/php-monorepo/actions)  
[![Coverage](https://img.shields.io/codecov/c/github/tourze/php-monorepo?style=flat-square)](https://codecov.io/gh/tourze/php-monorepo)

A comprehensive lottery system bundle for Symfony applications featuring multiple lottery types, advanced prize pool management, chance control, and automated prize distribution.

## Table of Contents

- [Features](#features)
- [Installation](#installation)
  - [Requirements](#requirements)
  - [Install via Composer](#install-via-composer)
- [Quick Start](#quick-start)
  - [Bundle Registration](#bundle-registration)
  - [Database Setup](#database-setup)
  - [Configuration](#configuration)
- [Console Commands](#console-commands)
- [JSON-RPC API](#json-rpc-api)
- [System Architecture](#system-architecture)
- [Event System](#event-system)
- [Advanced Usage](#advanced-usage)
  - [Custom Event Subscribers](#custom-event-subscribers)
  - [Expression Language](#expression-language)
  - [External Integration](#external-integration)
- [Contributing](#contributing)
- [License](#license)

## Features

- **Multiple Lottery Types** - Support for wheel, grid, slot machine, and on-site lotteries
- **Advanced Prize Pool Management** - Multi-pool system with configurable probabilities and constraints
- **Intelligent Chance Management** - User chances with expiration tracking and status control
- **Automated Prize Distribution** - Automatic prize sending with consignee management
- **Admin Interface** - Complete EasyAdmin integration for lottery management
- **Responsive H5 Frontend** - Mobile-optimized lottery interface with multiple themes
- **Event-Driven Architecture** - Customizable event system for business logic extension
- **JSON-RPC API** - RESTful API endpoints for external system integration
- **Cron Job Integration** - Automated background tasks for maintenance and processing
- **Expression Language** - Flexible configuration with dynamic rule evaluation
- **Concurrent Safety** - Database-level locking for high-concurrency scenarios

## Installation

### Requirements

- PHP 8.1 or higher
- Symfony 7.3 or higher
- Doctrine ORM 3.0 or higher
- MySQL 5.7+ or PostgreSQL 10+
- Redis (recommended for caching)

### Install via Composer

```bash
composer require tourze/lottery-bundle
```

## Quick Start

```php
<?php

use LotteryBundle\Service\LotteryService;
use LotteryBundle\Service\PrizeService;
use LotteryBundle\Entity\Chance;

// Execute lottery draw
$lotteryService = $container->get(LotteryService::class);
$result = $lotteryService->doLottery($chance);

// Count valid chances for user
$validChances = $lotteryService->countValidChance($user, $activity);

// Award chances to user
$lotteryService->giveChance($user, $chance);

// Prize management
$prizeService = $container->get(PrizeService::class);
$prizeService->sendPrize($chance);

// Check if activity is currently active
$isActive = $activity->isActive();

// Get available lottery templates
$templates = $lotteryService->getAvailableTemplates();
```

### Bundle Registration

Add to `config/bundles.php`:

```php
LotteryBundle\LotteryBundle::class => ['all' => true],
```

### Database Setup

```bash
# Create database tables
php bin/console doctrine:migrations:migrate

# Load sample data (optional)
php bin/console doctrine:fixtures:load --group=lottery
```

### Configuration

The bundle provides flexible configuration through:

- **Activity Settings** - Configure lottery activities with time constraints and participation rules
- **Prize Pool Management** - Set up multiple pools with different probability algorithms
- **Prize Configuration** - Define virtual rewards, physical prizes, and distribution methods
- **Chance Control** - Monitor user participation limits and success tracking

## Console Commands

### lottery:check-expire-chance

Checks and processes expired lottery chances. This command runs every minute via
cron job.

```bash
php bin/console lottery:check-expire-chance
```

**Purpose**: Automatically marks expired chances as invalid and triggers expire
events for cleanup.

### lottery:check-review-chance-send-prize

Processes reviewed chances and sends prizes to winners. This command runs every hour
at 43 minutes.

```bash
php bin/console lottery:check-review-chance-send-prize
```

**Purpose**: Automatically sends prizes to users whose chances have been reviewed
and approved.

## JSON-RPC API

The bundle exposes JSON-RPC procedures for external integrations:

### Lottery Operations
- `JoinLottery` - Execute lottery participation with concurrent safety
- `GetAllLotteryChance` - Retrieve comprehensive lottery chance data
- `GetLotteryDetail` - Get detailed activity information and rules
- `ServerSendLotteryChance` - Administrative chance distribution

### User Management
- `GetUserLotteryChanceList` - User's participation history with pagination
- `GetUserValidLotteryChanceCounts` - Real-time valid chance counts
- `GetLotteryConsignee` - User delivery address information
- `SaveOrUpdateLotteryConsignee` - Manage shipping addresses

### Prize Information
- `GetLotteryPrizeList` - Available prizes with stock information

## System Architecture

The lottery system implements a robust workflow with concurrent safety:

1. **Activity Validation** - Verify lottery timing, status, and participation eligibility
2. **Chance Authentication** - Validate user's available chances with database locking
3. **Chance Pre-deduction** - Atomically reserve chances to prevent overselling
4. **Pool Selection** - Dynamic pool determination based on configurable rules
5. **Prize Algorithm** - Probability-based prize selection with stock management
6. **Result Confirmation** - Transactional commit with event dispatching
7. **Prize Distribution** - Automated or manual prize fulfillment process

## Event System

Extensive event system for business logic customization:

### Core Events
- `UserJoinSuccessEvent` - Dispatched after successful lottery participation
- `AfterChanceExpireEvent` - Triggered when user chances expire
- `ChanceEvent` - General chance lifecycle event

### Customization Events
- `DecidePoolEvent` - Override default pool selection algorithm
- `DecidePrizeProbabilityEvent` - Implement custom probability calculations
- `AllLotteryChanceEvent` - Global event for all chance operations

## Advanced Usage

### Custom Event Subscribers

Extend lottery functionality with event subscribers:

```php
use LotteryBundle\Event\UserJoinSuccessEvent;
use LotteryBundle\Event\DecidePoolEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class CustomLotterySubscriber implements EventSubscriberInterface
{
    public static function getSubscribedEvents(): array
    {
        return [
            UserJoinSuccessEvent::class => 'onUserJoinSuccess',
            DecidePoolEvent::class => 'onDecidePool',
        ];
    }

    public function onUserJoinSuccess(UserJoinSuccessEvent $event): void
    {
        // Send notification, award points, etc.
        $chance = $event->getChance();
        $this->notificationService->sendLotteryResult($chance);
    }

    public function onDecidePool(DecidePoolEvent $event): void
    {
        // Custom pool selection based on user VIP level
        $user = $event->getUser();
        if ($user->isVip()) {
            $event->setPool($this->getVipPool());
        }
    }
}
```

### Expression Language

Implement complex business rules with expression language:

```yaml
# config/packages/lottery.yaml
lottery:
    pools:
        vip_pool:
            # Only VIP users with 5+ chances
            expression: 'user.isVip() and chance.count() > 5'
        weekend_pool:
            # Special weekend prizes
            expression: 'date("N") >= 6'
        high_value_pool:
            # High-value prizes for frequent players
            expression: 'user.getLotteryCount() > 100'
```

### External Integration

Integrate with external systems via JSON-RPC:

```php
// Join lottery from external application
$result = $client->call('JoinLottery', [
    'activityId' => 1,
    'count' => 3, // Draw 3 times
]);

// Get user's lottery history
$history = $client->call('GetUserLotteryChanceList', [
    'page' => 1,
    'limit' => 20,
]);

// Check available chances
$chances = $client->call('GetUserValidLotteryChanceCounts', [
    'activityId' => 1,
]);
```

## Contributing

Please see [CONTRIBUTING.md](CONTRIBUTING.md) for details.

## License

The MIT License (MIT). Please see [License File](LICENSE) for more information.
