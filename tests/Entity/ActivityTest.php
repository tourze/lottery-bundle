<?php

namespace LotteryBundle\Tests\Entity;

use Carbon\Carbon;
use Doctrine\Common\Collections\ArrayCollection;
use LotteryBundle\Entity\Activity;
use LotteryBundle\Entity\ActivityAttribute;
use LotteryBundle\Entity\Chance;
use LotteryBundle\Entity\Pool;
use PHPUnit\Framework\TestCase;

class ActivityTest extends TestCase
{
    private Activity $activity;

    protected function setUp(): void
    {
        $this->activity = new Activity();
    }

    /**
     * 测试基本属性设置与获取
     */
    public function test_basicProperties(): void
    {
        $title = '测试活动';
        $textRule = '活动规则内容';
        $startTime = Carbon::now();
        $endTime = Carbon::now()->addDays(7);
        $lastRedeemTime = Carbon::now()->addDays(10);
        $headPhoto = 'http://example.com/image.jpg';
        $noChanceText = '没有抽奖机会了';
        $sharePath = '/share';
        $shareTitle = '分享标题';
        $sharePicture = 'http://example.com/share.jpg';
        $valid = true;
        $createdBy = 'admin';
        $updatedBy = 'admin';
        $createdFromIp = '127.0.0.1';
        $updatedFromIp = '127.0.0.1';
        $createTime = Carbon::now();
        $updateTime = Carbon::now();

        $this->activity->setTitle($title);
        $this->activity->setTextRule($textRule);
        $this->activity->setStartTime($startTime);
        $this->activity->setEndTime($endTime);
        $this->activity->setLastRedeemTime($lastRedeemTime);
        $this->activity->setHeadPhoto($headPhoto);
        $this->activity->setNoChanceText($noChanceText);
        $this->activity->setSharePath($sharePath);
        $this->activity->setShareTitle($shareTitle);
        $this->activity->setSharePicture($sharePicture);
        $this->activity->setValid($valid);
        $this->activity->setCreatedBy($createdBy);
        $this->activity->setUpdatedBy($updatedBy);
        $this->activity->setCreatedFromIp($createdFromIp);
        $this->activity->setUpdatedFromIp($updatedFromIp);
        $this->activity->setCreateTime($createTime);
        $this->activity->setUpdateTime($updateTime);

        $this->assertEquals($title, $this->activity->getTitle());
        $this->assertEquals($textRule, $this->activity->getTextRule());
        $this->assertEquals($startTime, $this->activity->getStartTime());
        $this->assertEquals($endTime, $this->activity->getEndTime());
        $this->assertEquals($lastRedeemTime, $this->activity->getLastRedeemTime());
        $this->assertEquals($headPhoto, $this->activity->getHeadPhoto());
        $this->assertEquals($noChanceText, $this->activity->getNoChanceText());
        $this->assertEquals($sharePath, $this->activity->getSharePath());
        $this->assertEquals($shareTitle, $this->activity->getShareTitle());
        $this->assertEquals($sharePicture, $this->activity->getSharePicture());
        $this->assertEquals($valid, $this->activity->isValid());
        $this->assertEquals($createdBy, $this->activity->getCreatedBy());
        $this->assertEquals($updatedBy, $this->activity->getUpdatedBy());
        $this->assertEquals($createdFromIp, $this->activity->getCreatedFromIp());
        $this->assertEquals($updatedFromIp, $this->activity->getUpdatedFromIp());
        $this->assertEquals($createTime, $this->activity->getCreateTime());
        $this->assertEquals($updateTime, $this->activity->getUpdateTime());
    }

    /**
     * 测试关联集合初始化
     */
    public function test_collectionsInitialized(): void
    {
        $this->assertInstanceOf(ArrayCollection::class, $this->activity->getChances());
        $this->assertInstanceOf(ArrayCollection::class, $this->activity->getPools());
        $this->assertInstanceOf(ArrayCollection::class, $this->activity->getAttributes());
        
        $this->assertCount(0, $this->activity->getChances());
        $this->assertCount(0, $this->activity->getPools());
        $this->assertCount(0, $this->activity->getAttributes());
    }

    /**
     * 测试添加和移除 Chance
     */
    public function test_addAndRemoveChance(): void
    {
        $chance = $this->createMock(Chance::class);
        
        $chance->expects($this->once())
            ->method('setActivity')
            ->with($this->activity);
        
        $this->activity->addChance($chance);
        $this->assertCount(1, $this->activity->getChances());
        $this->assertTrue($this->activity->getChances()->contains($chance));
        
        $this->activity->removeChance($chance);
        $this->assertCount(0, $this->activity->getChances());
        $this->assertFalse($this->activity->getChances()->contains($chance));
    }

    /**
     * 测试添加和移除 Pool
     */
    public function test_addAndRemovePool(): void
    {
        $pool = $this->createMock(Pool::class);
        
        $pool->expects($this->once())
            ->method('addActivity')
            ->with($this->activity);
        
        $this->activity->addPool($pool);
        $this->assertCount(1, $this->activity->getPools());
        $this->assertTrue($this->activity->getPools()->contains($pool));
        
        $pool->expects($this->once())
            ->method('removeActivity')
            ->with($this->activity);
        
        $this->activity->removePool($pool);
        $this->assertCount(0, $this->activity->getPools());
        $this->assertFalse($this->activity->getPools()->contains($pool));
    }

    /**
     * 测试添加和移除 ActivityAttribute
     */
    public function test_addAndRemoveActivityAttribute(): void
    {
        $attribute = $this->createMock(ActivityAttribute::class);
        
        $attribute->expects($this->once())
            ->method('setActivity')
            ->with($this->activity);
        
        $this->activity->addActivityAttribute($attribute);
        $this->assertCount(1, $this->activity->getAttributes());
        $this->assertTrue($this->activity->getAttributes()->contains($attribute));
        
        $this->activity->removeActivityAttribute($attribute);
        $this->assertCount(0, $this->activity->getAttributes());
        $this->assertFalse($this->activity->getAttributes()->contains($attribute));
    }

    /**
     * 测试 __toString 方法
     */
    public function test_toString_withValidId(): void
    {
        $title = '测试活动';
        $this->activity->setTitle($title);
        
        // 使用反射设置 id 属性
        $reflectionClass = new \ReflectionClass(Activity::class);
        $reflectionProperty = $reflectionClass->getProperty('id');
        $reflectionProperty->setAccessible(true);
        $reflectionProperty->setValue($this->activity, 1);
        
        $this->assertEquals($title, (string) $this->activity);
    }

    /**
     * 测试 __toString 方法（无 ID 情况）
     */
    public function test_toString_withoutId(): void
    {
        $this->assertEquals('', (string) $this->activity);
    }

    /**
     * 测试 retrievePlainArray 方法
     */
    public function test_retrievePlainArray_returnsCorrectArray(): void
    {
        $title = '测试活动';
        $startTime = Carbon::now();
        $endTime = Carbon::now()->addDays(7);
        
        $this->activity->setTitle($title);
        $this->activity->setStartTime($startTime);
        $this->activity->setEndTime($endTime);
        
        // 使用反射设置 id 属性
        $reflectionClass = new \ReflectionClass(Activity::class);
        $reflectionProperty = $reflectionClass->getProperty('id');
        $reflectionProperty->setAccessible(true);
        $reflectionProperty->setValue($this->activity, 1);
        
        $array = $this->activity->retrievePlainArray();
        $this->assertArrayHasKey('id', $array);
        $this->assertArrayHasKey('title', $array);
        $this->assertArrayHasKey('startTime', $array);
        $this->assertArrayHasKey('endTime', $array);
        $this->assertEquals(1, $array['id']);
        $this->assertEquals($title, $array['title']);
    }

    /**
     * 测试 retrieveAdminArray 方法
     */
    public function test_retrieveAdminArray_returnsCorrectArray(): void
    {
        $title = '测试活动';
        
        $this->activity->setTitle($title);
        
        // 使用反射设置 id 属性
        $reflectionClass = new \ReflectionClass(Activity::class);
        $reflectionProperty = $reflectionClass->getProperty('id');
        $reflectionProperty->setAccessible(true);
        $reflectionProperty->setValue($this->activity, 1);
        
        $array = $this->activity->retrieveAdminArray();
        $this->assertArrayHasKey('id', $array);
        $this->assertArrayHasKey('title', $array);
        $this->assertEquals(1, $array['id']);
        $this->assertEquals($title, $array['title']);
    }

    /**
     * 测试 ResourceIdentity 接口方法
     */
    public function test_resourceIdentityMethods(): void
    {
        $title = '测试活动';
        $this->activity->setTitle($title);
        
        // 使用反射设置 id 属性
        $reflectionClass = new \ReflectionClass(Activity::class);
        $reflectionProperty = $reflectionClass->getProperty('id');
        $reflectionProperty->setAccessible(true);
        $reflectionProperty->setValue($this->activity, 1);
        
        $this->assertEquals('1', $this->activity->getResourceId());
        $this->assertEquals($title, $this->activity->getResourceLabel());
    }
} 