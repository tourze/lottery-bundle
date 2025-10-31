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
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Filter\EntityFilter;
use EasyCorp\Bundle\EasyAdminBundle\Filter\TextFilter;
use LotteryBundle\Entity\PoolAttribute;

/**
 * 奖池属性管理控制器
 *
 * @template TEntity of PoolAttribute
 * @extends AbstractCrudController<TEntity>
 */
#[AdminCrud(
    routePath: '/lottery/pool-attribute',
    routeName: 'lottery_pool_attribute',
)]
final class PoolAttributeCrudController extends AbstractCrudController
{
    /**
     * @phpstan-return class-string<TEntity>
     */
    public static function getEntityFqcn(): string
    {
        /** @phpstan-var class-string<TEntity> */
        return PoolAttribute::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('奖池属性')
            ->setEntityLabelInPlural('奖池属性列表')
            ->setPageTitle('index', '奖池属性管理')
            ->setPageTitle('new', '创建奖池属性')
            ->setPageTitle('edit', fn (PoolAttribute $poolAttribute) => sprintf('编辑奖池属性: %s', $poolAttribute->getName() ?? 'Unknown'))
            ->setPageTitle('detail', fn (PoolAttribute $poolAttribute) => sprintf('奖池属性详情: %s', $poolAttribute->getName() ?? 'Unknown'))
            ->setHelp('index', '这里列出了所有的奖池属性，每个奖池属性关联到特定奖池')
            ->setDefaultSort(['id' => 'DESC'])
            ->setSearchFields(['id', 'name', 'value', 'remark', 'pool.name'])
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

        yield TextField::new('name', '属性名称')
            ->setRequired(true)
            ->setHelp('属性的名称，在同一奖池中必须唯一')
        ;

        yield TextField::new('value', '属性值')
            ->setRequired(true)
            ->setHelp('属性的值')
        ;

        yield TextareaField::new('remark', '备注')
            ->hideOnIndex()
            ->setRequired(false)
            ->setHelp('可选的备注信息')
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

        yield AssociationField::new('pool', '所属奖池')
            ->setRequired(true)
            ->setHelp('属性所属的奖池')
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
        ;
    }

    public function configureFilters(Filters $filters): Filters
    {
        return $filters
            ->add(TextFilter::new('name', '属性名称'))
            ->add(TextFilter::new('value', '属性值'))
            ->add(TextFilter::new('remark', '备注'))
            ->add(EntityFilter::new('pool', '所属奖池'))
        ;
    }
}
