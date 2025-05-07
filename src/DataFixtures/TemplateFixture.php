<?php

namespace LotteryBundle\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Yiisoft\Json\Json;

/**
 * 角色数据
 */
class TemplateFixture extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $json = null;

        return;

        // TODO 新增模板数据，下面要改造
        $json = file_get_contents(__DIR__ . '/../../data/default_lottery_template.json');
        $data = Json::decode($json);

        foreach ($data as $datum) {
            $model = LotteryTemplate::findOne([
                'marking' => $datum['marking'],
            ]);
            if ($model) {
                $model->update_time = Carbon::now()->toDateTimeString();
                $model->updated_by = 1;
            } else {
                $model = new LotteryTemplate();
                $model->create_time = Carbon::now()->toDateTimeString();
                $model->created_by = 1;
            }

            $model->thumb_url = $datum['thumb'];
            $model->active_thumb = $datum['active_thumb'];
            $model->title = $datum['title'];
            $model->is_hot = intval($datum['is_hot']);
            $model->marking = $datum['marking'] ?? '';
            $model->status = $datum['status'];
            $model->type = $datum['type'];
            $model->save();
        }
    }
}
