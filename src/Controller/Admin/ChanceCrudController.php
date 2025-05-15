<?php

namespace LotteryBundle\Controller\Admin;

use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Filter\BooleanFilter;
use EasyCorp\Bundle\EasyAdminBundle\Filter\ChoiceFilter;
use EasyCorp\Bundle\EasyAdminBundle\Filter\DateTimeFilter;
use EasyCorp\Bundle\EasyAdminBundle\Filter\EntityFilter;
use EasyCorp\Bundle\EasyAdminBundle\Filter\TextFilter;
use LotteryBundle\Entity\Chance;
use LotteryBundle\Enum\ChanceStatusEnum;

/**
 * 抽奖机会管理控制器
 */
class ChanceCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Chance::class;
    }
    
    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('抽奖机会')
            ->setEntityLabelInPlural('抽奖机会列表')
            ->setPageTitle('index', '抽奖机会管理')
            ->setPageTitle('new', '创建抽奖机会')
            ->setPageTitle('edit', fn (Chance $chance) => sprintf('编辑抽奖机会: #%s', $chance->getId()))
            ->setPageTitle('detail', fn (Chance $chance) => sprintf('机会详情: #%s', $chance->getId()))
            ->setHelp('index', '这里列出了所有的抽奖机会')
            ->setDefaultSort(['id' => 'DESC'])
            ->setSearchFields(['id', 'title', 'remark'])
            ->setPaginatorPageSize(30); // 增加每页显示数量，便于查看
    }

    public function configureFields(string $pageName): iterable
    {
        // ID字段
        yield IdField::new('id')
            ->hideOnForm()
            ->setMaxLength(9999);
            
        // 基本信息
        yield TextField::new('title', '任务标题')
            ->setRequired(false);
            
        yield TextField::new('remark', '备注')
            ->hideOnIndex()
            ->setRequired(false);
            
        // 时间信息
        yield DateTimeField::new('startTime', '开始时间')
            ->setRequired(false)
            ->setFormat('yyyy-MM-dd HH:mm:ss');
            
        yield DateTimeField::new('expireTime', '失效时间')
            ->setRequired(false)
            ->setFormat('yyyy-MM-dd HH:mm:ss');
            
        yield DateTimeField::new('useTime', '使用时间')
            ->hideOnIndex()
            ->setRequired(false)
            ->setFormat('yyyy-MM-dd HH:mm:ss');
            
        yield DateTimeField::new('sendTime', '发送时间')
            ->hideOnIndex()
            ->setRequired(false)
            ->setFormat('yyyy-MM-dd HH:mm:ss');
            
        // 状态信息
        yield ChoiceField::new('status', '状态')
            ->setChoices([
                '未使用' => ChanceStatusEnum::INIT,
                '已中奖' => ChanceStatusEnum::WINNING,
                '已审核' => ChanceStatusEnum::REVIEWED,
                '已发奖' => ChanceStatusEnum::SENT,
                '已过期' => ChanceStatusEnum::EXPIRED,
            ])
            ->renderAsBadges()
            ->hideOnForm();
            
        yield BooleanField::new('valid', '是否有效')
            ->renderAsSwitch(true);
            
        // 关联实体
        yield AssociationField::new('activity', '关联活动')
            ->setRequired(true);
            
        yield AssociationField::new('user', '用户')
            ->setRequired(false);
            
        yield AssociationField::new('pool', '关联奖池')
            ->hideOnIndex()
            ->setRequired(false);
            
        yield AssociationField::new('prize', '获得奖品')
            ->hideOnIndex()
            ->setRequired(false);
            
        yield AssociationField::new('consignee', '收货信息')
            ->hideOnIndex()
            ->setRequired(false);
            
        yield AssociationField::new('stocks', '库存信息')
            ->hideOnIndex()
            ->setRequired(false);
            
        // 审计信息
        yield TextField::new('createdBy', '创建人')
            ->hideOnForm()
            ->hideOnIndex();
            
        yield TextField::new('updatedBy', '更新人')
            ->hideOnForm()
            ->hideOnIndex();
            
        yield DateTimeField::new('createTime', '创建时间')
            ->hideOnForm()
            ->setFormat('yyyy-MM-dd HH:mm:ss');
            
        yield DateTimeField::new('updateTime', '更新时间')
            ->hideOnForm()
            ->hideOnIndex()
            ->setFormat('yyyy-MM-dd HH:mm:ss');
    }
    
    public function configureActions(Actions $actions): Actions
    {
        return $actions
            ->add(Crud::PAGE_INDEX, Action::DETAIL)
            ->add(Crud::PAGE_EDIT, Action::DETAIL)
            ->reorder(Crud::PAGE_INDEX, [Action::DETAIL, Action::EDIT, Action::DELETE]);
    }
    
    public function configureFilters(Filters $filters): Filters
    {
        return $filters
            ->add(TextFilter::new('title', '任务标题'))
            ->add(EntityFilter::new('activity', '关联活动'))
            ->add(EntityFilter::new('prize', '获得奖品'))
            ->add(ChoiceFilter::new('status', '状态')->setChoices([
                '未使用' => ChanceStatusEnum::INIT->value,
                '已中奖' => ChanceStatusEnum::WINNING->value,
                '已审核' => ChanceStatusEnum::REVIEWED->value,
                '已发奖' => ChanceStatusEnum::SENT->value,
                '已过期' => ChanceStatusEnum::EXPIRED->value,
            ]))
            ->add(DateTimeFilter::new('startTime', '开始时间'))
            ->add(DateTimeFilter::new('expireTime', '失效时间'))
            ->add(DateTimeFilter::new('useTime', '使用时间'))
            ->add(DateTimeFilter::new('sendTime', '发送时间'))
            ->add(BooleanFilter::new('valid', '是否有效'));
    }
} 