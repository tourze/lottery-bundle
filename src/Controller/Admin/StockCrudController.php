<?php

declare(strict_types=1);

namespace LotteryBundle\Controller\Admin;

use EasyCorp\Bundle\EasyAdminBundle\Attribute\AdminCrud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use EasyCorp\Bundle\EasyAdminBundle\Contracts\Field\FieldInterface;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\FormField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Filter\EntityFilter;
use EasyCorp\Bundle\EasyAdminBundle\Filter\NumericFilter;
use EasyCorp\Bundle\EasyAdminBundle\Filter\TextFilter;
use LotteryBundle\Entity\Stock;

/**
 * 库存管理控制器
 *
 * @template TEntity of Stock
 * @extends AbstractCrudController<TEntity>
 */
#[AdminCrud(
    routePath: '/lottery/stock',
    routeName: 'lottery_stock',
)]
final class StockCrudController extends AbstractCrudController
{
    /**
     * @phpstan-return class-string<TEntity>
     */
    public static function getEntityFqcn(): string
    {
        /** @phpstan-var class-string<TEntity> */
        return Stock::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('库存记录')
            ->setEntityLabelInPlural('库存记录列表')
            ->setPageTitle('index', '库存管理')
            ->setPageTitle('new', '创建库存记录')
            ->setPageTitle('edit', fn (Stock $stock) => sprintf('编辑库存记录: %s', $stock->getSn() ?? 'Unknown'))
            ->setPageTitle('detail', fn (Stock $stock) => sprintf('库存记录详情: %s', $stock->getSn() ?? 'Unknown'))
            ->setHelp('index', '这里列出了所有的库存记录，每个库存记录关联到特定奖品')
            ->setDefaultSort(['id' => 'DESC'])
            ->setSearchFields(['id', 'sn', 'prize.name'])
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
        yield from $this->getAssociationFields($pageName);
        yield from $this->getAuditFields($pageName);
    }

    /**
     * @return iterable<FieldInterface>
     */
    private function getBasicInfoFields(string $pageName): iterable
    {
        if (Crud::PAGE_INDEX !== $pageName && Crud::PAGE_DETAIL !== $pageName) {
            yield FormField::addTab('基本信息')
                ->setIcon('fas fa-info-circle')
            ;
        }

        yield TextField::new('sn', '序列号')
            ->setRequired(true)
            ->setHelp('库存记录的唯一序列号')
        ;

        yield IntegerField::new('lockVersion', '版本号')
            ->hideOnIndex()
            ->setRequired(false)
            ->setHelp('乐观锁版本号，用于并发控制')
        ;
    }

    /**
     * @return iterable<FieldInterface>
     */
    private function getAssociationFields(string $pageName): iterable
    {
        if (Crud::PAGE_INDEX !== $pageName && Crud::PAGE_DETAIL !== $pageName) {
            yield FormField::addTab('关联信息')
                ->setIcon('fas fa-link')
            ;
        }

        yield AssociationField::new('prize', '关联奖品')
            ->setRequired(true)
            ->setHelp('库存记录所关联的奖品')
        ;

        yield AssociationField::new('chance', '关联抽奖机会')
            ->hideOnIndex()
            ->setRequired(false)
            ->setHelp('如果已被抽中，显示对应的抽奖机会')
        ;
    }

    /**
     * @return iterable<FieldInterface>
     */
    private function getAuditFields(string $pageName): iterable
    {
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
            ->hideOnIndex()
            ->setFormat('yyyy-MM-dd HH:mm:ss')
        ;

        yield DateTimeField::new('updateTime', '更新时间')
            ->hideOnForm()
            ->setFormat('yyyy-MM-dd HH:mm:ss')
        ;

        yield TextField::new('createdByIp', '创建IP')
            ->hideOnForm()
            ->hideOnIndex()
        ;

        yield TextField::new('updatedByIp', '更新IP')
            ->hideOnForm()
            ->hideOnIndex()
        ;
    }

    public function configureActions(Actions $actions): Actions
    {
        return $actions
            ->add(Crud::PAGE_INDEX, Action::DETAIL)
            ->add(Crud::PAGE_EDIT, Action::DETAIL)
            ->disable(Action::NEW)  // 库存记录应该通过奖品生成，不允许手动创建
        ;
    }

    public function configureFilters(Filters $filters): Filters
    {
        return $filters
            ->add(TextFilter::new('sn', '序列号'))
            ->add(EntityFilter::new('prize', '关联奖品'))
            ->add(EntityFilter::new('chance', '关联抽奖机会'))
            ->add(NumericFilter::new('lockVersion', '版本号'))
        ;
    }
}
