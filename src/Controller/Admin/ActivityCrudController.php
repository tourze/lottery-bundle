<?php

namespace LotteryBundle\Controller\Admin;

use EasyCorp\Bundle\EasyAdminBundle\Attribute\AdminCrud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use EasyCorp\Bundle\EasyAdminBundle\Contracts\Field\FieldInterface;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\FormField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Filter\BooleanFilter;
use EasyCorp\Bundle\EasyAdminBundle\Filter\DateTimeFilter;
use EasyCorp\Bundle\EasyAdminBundle\Filter\TextFilter;
use LotteryBundle\Entity\Activity;

/**
 * 抽奖活动管理控制器
 *
 * @template TEntity of Activity
 * @extends AbstractCrudController<TEntity>
 */
#[AdminCrud(
    routePath: '/lottery/activity',
    routeName: 'lottery_activity',
)]
final class ActivityCrudController extends AbstractCrudController
{
    /**
     * @phpstan-return class-string<TEntity>
     */
    public static function getEntityFqcn(): string
    {
        /** @phpstan-var class-string<TEntity> */
        return Activity::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('抽奖活动')
            ->setEntityLabelInPlural('抽奖活动列表')
            ->setPageTitle('index', '抽奖活动管理')
            ->setPageTitle('new', '创建抽奖活动')
            ->setPageTitle('edit', fn (Activity $activity) => sprintf('编辑活动: %s', $activity->getTitle()))
            ->setPageTitle('detail', fn (Activity $activity) => sprintf('活动详情: %s', $activity->getTitle()))
            ->setHelp('index', '这里列出了所有的抽奖活动')
            ->setDefaultSort(['id' => 'DESC'])
            ->setSearchFields(['id', 'title', 'textRule'])
        ;
    }

    public function configureFields(string $pageName): iterable
    {
        // ID字段始终显示在所有标签页上
        yield IdField::new('id', 'ID')
            ->hideOnForm()
            ->setMaxLength(9999)
        ;

        yield from $this->getBasicInfoFields($pageName);
        yield from $this->getDisplayFields($pageName);
        yield from $this->getShareFields($pageName);
        yield from $this->getAssociationFields($pageName);
        yield from $this->getStatusAndAuditFields($pageName);
    }

    /**
     * @return iterable<FieldInterface>
     */
    private function getBasicInfoFields(string $pageName): iterable
    {
        if (Crud::PAGE_INDEX !== $pageName && Crud::PAGE_DETAIL !== $pageName) {
            yield FormField::addTab('基本信息')->setIcon('fas fa-info-circle');
        }

        yield TextField::new('title', '活动标题')
            ->setRequired(true)
            ->setHelp('活动的显示名称')
        ;

        yield TextareaField::new('textRule', '活动规则')
            ->setRequired(false)
            ->hideOnIndex()
            ->setHelp('详细说明活动规则')
        ;

        yield DateTimeField::new('startTime', '开始时间')
            ->setRequired(true)
            ->setFormat('yyyy-MM-dd HH:mm:ss')
        ;

        yield DateTimeField::new('endTime', '结束时间')
            ->setRequired(true)
            ->setFormat('yyyy-MM-dd HH:mm:ss')
        ;

        yield DateTimeField::new('lastRedeemTime', '最后兑奖时间')
            ->setRequired(false)
            ->hideOnIndex()
            ->setFormat('yyyy-MM-dd HH:mm:ss')
        ;
    }

    /**
     * @return iterable<FieldInterface>
     */
    private function getDisplayFields(string $pageName): iterable
    {
        if (Crud::PAGE_INDEX !== $pageName && Crud::PAGE_DETAIL !== $pageName) {
            yield FormField::addTab('展示设置')->setIcon('fas fa-eye');
        }

        yield ImageField::new('headPhoto', '头图')
            ->setBasePath('/uploads/images')
            ->setUploadDir('public/uploads/images')
            ->setUploadedFileNamePattern('[randomhash].[extension]')
            ->setRequired(false)
        ;

        yield TextField::new('noChanceText', '无机会提示文本')
            ->hideOnIndex()
            ->setRequired(false)
            ->setHelp('当用户没有抽奖机会时显示的文本')
        ;
    }

    /**
     * @return iterable<FieldInterface>
     */
    private function getShareFields(string $pageName): iterable
    {
        if (Crud::PAGE_INDEX !== $pageName && Crud::PAGE_DETAIL !== $pageName) {
            yield FormField::addTab('分享设置')->setIcon('fas fa-share-alt');
        }

        yield TextField::new('sharePath', '分享路径')
            ->hideOnIndex()
            ->setRequired(false)
        ;

        yield TextField::new('shareTitle', '分享标题')
            ->hideOnIndex()
            ->setRequired(false)
        ;

        yield ImageField::new('sharePicture', '分享图片')
            ->hideOnIndex()
            ->setBasePath('/uploads/images')
            ->setUploadDir('public/uploads/images')
            ->setUploadedFileNamePattern('[randomhash].[extension]')
            ->setRequired(false)
        ;
    }

    /**
     * @return iterable<FieldInterface>
     */
    private function getAssociationFields(string $pageName): iterable
    {
        if (Crud::PAGE_INDEX !== $pageName && Crud::PAGE_DETAIL !== $pageName) {
            yield FormField::addTab('关联设置')->setIcon('fas fa-link');
        }

        yield AssociationField::new('pools', '关联奖池')
            ->hideOnIndex()
            ->setRequired(true)
        ;
    }

    /**
     * @return iterable<FieldInterface>
     */
    private function getStatusAndAuditFields(string $pageName): iterable
    {
        if (Crud::PAGE_INDEX !== $pageName && Crud::PAGE_DETAIL !== $pageName) {
            yield FormField::addTab('状态与审计')->setIcon('fas fa-toggle-on');
        }

        yield BooleanField::new('valid', '是否有效')
            ->renderAsSwitch(true)
        ;

        yield TextField::new('createdBy', '创建人')
            ->hideOnForm()
            ->hideOnIndex()
        ;

        yield TextField::new('updatedBy', '更新人')
            ->hideOnForm()
            ->hideOnIndex()
        ;

        yield DateTimeField::new('createTime', '创建时间')
            ->hideOnForm()
            ->setFormat('yyyy-MM-dd HH:mm:ss')
        ;

        yield DateTimeField::new('updateTime', '更新时间')
            ->hideOnForm()
            ->hideOnIndex()
            ->setFormat('yyyy-MM-dd HH:mm:ss')
        ;
    }

    public function configureActions(Actions $actions): Actions
    {
        return $actions
            ->add(Crud::PAGE_INDEX, Action::DETAIL)
            ->add(Crud::PAGE_EDIT, Action::DETAIL)
        ;
    }

    public function configureFilters(Filters $filters): Filters
    {
        return $filters
            ->add(TextFilter::new('title', '活动标题'))
            ->add(DateTimeFilter::new('startTime', '开始时间'))
            ->add(DateTimeFilter::new('endTime', '结束时间'))
            ->add(BooleanFilter::new('valid', '是否有效'))
        ;
    }
}
