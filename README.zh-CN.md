# 抽奖模块

[English](README.md) | [中文](README.zh-CN.md)

[![Latest Version](https://img.shields.io/packagist/v/tourze/lottery-bundle.svg?style=flat-square)](https://packagist.org/packages/tourze/lottery-bundle)  
[![Total Downloads](https://img.shields.io/packagist/dt/tourze/lottery-bundle.svg?style=flat-square)](https://packagist.org/packages/tourze/lottery-bundle)  
[![PHP Version](https://img.shields.io/packagist/php-v/tourze/lottery-bundle.svg?style=flat-square)](https://packagist.org/packages/tourze/lottery-bundle)  
[![License](https://img.shields.io/packagist/l/tourze/lottery-bundle.svg?style=flat-square)](https://packagist.org/packages/tourze/lottery-bundle)  
[![Build Status](https://img.shields.io/github/actions/workflow/status/tourze/php-monorepo/test.yml?style=flat-square)](https://github.com/tourze/php-monorepo/actions)  
[![Coverage](https://img.shields.io/codecov/c/github/tourze/php-monorepo?style=flat-square)](https://codecov.io/gh/tourze/php-monorepo)

为 Symfony 应用程序提供的综合抽奖系统包，支持多种抽奖类型、高级奖池管理、机会控制和自动奖品分发。

## 目录

- [功能特性](#功能特性)
- [安装](#安装)
  - [系统要求](#系统要求)
  - [通过 Composer 安装](#通过-composer-安装)
- [快速开始](#快速开始)
  - [Bundle 注册](#bundle-注册)
  - [数据库设置](#数据库设置)
  - [配置](#配置)
- [控制台命令](#控制台命令)
- [JSON-RPC API](#json-rpc-api)
- [系统架构](#系统架构)
- [事件系统](#事件系统)
- [高级用法](#高级用法)
  - [自定义事件订阅者](#自定义事件订阅者)
  - [表达式语言](#表达式语言)
  - [外部集成](#外部集成)
- [贡献指南](#贡献指南)
- [许可证](#许可证)

## 功能特性

- **多种抽奖类型** - 支持转盘、九宫格、老虎机和现场抽奖
- **高级奖池管理** - 多奖池系统，支持可配置概率和约束条件
- **智能机会管理** - 用户机会过期跟踪和状态控制
- **自动奖品分发** - 自动发奖和收货人管理
- **管理后台** - 完整的 EasyAdmin 抽奖管理集成
- **响应式 H5 前端** - 移动端优化的抽奖界面，支持多种主题
- **事件驱动架构** - 可定制的事件系统，用于业务逻辑扩展
- **JSON-RPC API** - RESTful API 接口，支持外部系统集成
- **定时任务集成** - 自动化后台任务处理和维护
- **表达式语言** - 灵活配置，支持动态规则评估
- **并发安全** - 数据库级锁定，适用于高并发场景

## 安装

### 系统要求

- PHP 8.1 或更高版本
- Symfony 7.3 或更高版本
- Doctrine ORM 3.0 或更高版本
- MySQL 5.7+ 或 PostgreSQL 10+
- Redis（推荐用于缓存）

### 通过 Composer 安装

```bash
composer require tourze/lottery-bundle
```

## 快速开始

```php
<?php

use LotteryBundle\Service\LotteryService;
use LotteryBundle\Service\PrizeService;
use LotteryBundle\Entity\Chance;

// 执行抽奖
$lotteryService = $container->get(LotteryService::class);
$result = $lotteryService->doLottery($chance);

// 统计用户有效机会数
$validChances = $lotteryService->countValidChance($user, $activity);

// 为用户发放机会
$lotteryService->giveChance($user, $chance);

// 奖品管理
$prizeService = $container->get(PrizeService::class);
$prizeService->sendPrize($chance);

// 检查活动是否正在进行
$isActive = $activity->isActive();

// 获取可用的抽奖模板
$templates = $lotteryService->getAvailableTemplates();
```

### Bundle 注册

在 `config/bundles.php` 中添加：

```php
LotteryBundle\LotteryBundle::class => ['all' => true],
```

### 数据库设置

```bash
# 创建数据库表
php bin/console doctrine:migrations:migrate

# 加载示例数据（可选）
php bin/console doctrine:fixtures:load --group=lottery
```

### 配置

该模块通过以下方式提供灵活的配置：

- **活动设置** - 配置抽奖活动的时间约束和参与规则
- **奖池管理** - 设置多个奖池和不同的概率算法
- **奖品配置** - 定义虚拟奖励、实物奖品和分发方法
- **机会控制** - 监控用户参与限制和成功跟踪

## 控制台命令

### lottery:check-expire-chance

检查并处理过期的抽奖机会。此命令通过定时任务每分钟运行一次。

```bash
php bin/console lottery:check-expire-chance
```

**用途**: 自动将过期的机会标记为无效，并触发过期事件进行清理。

### lottery:check-review-chance-send-prize

处理已审核的机会并向获奖者发送奖品。此命令每小时在 43 分钟时运行。

```bash
php bin/console lottery:check-review-chance-send-prize
```

**用途**: 自动向机会已审核并批准的用户发送奖品。

## JSON-RPC API

该模块为外部集成提供 JSON-RPC 过程：

### 抽奖操作
- `JoinLottery` - 执行抽奖参与，具备并发安全性
- `GetAllLotteryChance` - 获取抽奖机会综合数据
- `GetLotteryDetail` - 获取详细活动信息和规则
- `ServerSendLotteryChance` - 管理员机会分发

### 用户管理
- `GetUserLotteryChanceList` - 用户参与历史，支持分页
- `GetUserValidLotteryChanceCounts` - 实时有效机会数量
- `GetLotteryConsignee` - 用户收货地址信息
- `SaveOrUpdateLotteryConsignee` - 管理配送地址

### 奖品信息
- `GetLotteryPrizeList` - 可用奖品及库存信息

## 系统架构

抽奖系统实现具备并发安全的健壮工作流程：

1. **活动验证** - 验证抽奖时间、状态和参与资格
2. **机会身份验证** - 通过数据库锁定验证用户可用机会
3. **机会预扣除** - 原子性预留机会，防止超卖
4. **奖池选择** - 基于可配置规则的动态奖池确定
5. **奖品算法** - 基于概率的奖品选择和库存管理
6. **结果确认** - 事务性提交和事件分发
7. **奖品分发** - 自动化或手动奖品履约流程

## 事件系统

为业务逻辑定制提供广泛的事件系统：

### 核心事件
- `UserJoinSuccessEvent` - 成功参与抽奖后分发
- `AfterChanceExpireEvent` - 用户机会过期时触发
- `ChanceEvent` - 通用机会生命周期事件

### 定制事件
- `DecidePoolEvent` - 覆盖默认奖池选择算法
- `DecidePrizeProbabilityEvent` - 实现自定义概率计算
- `AllLotteryChanceEvent` - 所有机会操作的全局事件

## 高级用法

### 自定义事件订阅者

通过事件订阅者扩展抽奖功能：

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
        // 发送通知、奖励积分等
        $chance = $event->getChance();
        $this->notificationService->sendLotteryResult($chance);
    }

    public function onDecidePool(DecidePoolEvent $event): void
    {
        // 基于用户 VIP 等级的自定义奖池选择
        $user = $event->getUser();
        if ($user->isVip()) {
            $event->setPool($this->getVipPool());
        }
    }
}
```

### 表达式语言

使用表达式语言实现复杂的业务规则：

```yaml
# config/packages/lottery.yaml
lottery:
    pools:
        vip_pool:
            # 仅 VIP 用户且有 5+ 机会
            expression: 'user.isVip() and chance.count() > 5'
        weekend_pool:
            # 周末特殊奖品
            expression: 'date("N") >= 6'
        high_value_pool:
            # 高价值奖品给频繁玩家
            expression: 'user.getLotteryCount() > 100'
```

### 外部集成

通过 JSON-RPC 与外部系统集成：

```php
// 从外部应用参与抽奖
$result = $client->call('JoinLottery', [
    'activityId' => 1,
    'count' => 3, // 抽取 3 次
]);

// 获取用户抽奖历史
$history = $client->call('GetUserLotteryChanceList', [
    'page' => 1,
    'limit' => 20,
]);

// 检查可用机会
$chances = $client->call('GetUserValidLotteryChanceCounts', [
    'activityId' => 1,
]);
```

## 贡献指南

请查看 [CONTRIBUTING.md](CONTRIBUTING.md) 了解详细信息。

## 许可证

MIT 许可证。请查看 [License File](LICENSE) 了解更多信息。
