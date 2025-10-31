<?php

declare(strict_types=1);

namespace LotteryBundle\Controller\Admin;

use EasyCorp\Bundle\EasyAdminBundle\Attribute\AdminAction;
use EasyCorp\Bundle\EasyAdminBundle\Attribute\AdminCrud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use EasyCorp\Bundle\EasyAdminBundle\Context\AdminContext;
use EasyCorp\Bundle\EasyAdminBundle\Contracts\Field\FieldInterface;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\CollectionField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\FormField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Filter\BooleanFilter;
use EasyCorp\Bundle\EasyAdminBundle\Filter\TextFilter;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use LotteryBundle\Controller\Admin\PrizeCrudController;
use LotteryBundle\Entity\Pool;
use Symfony\Component\HttpFoundation\Response;

/**
 * 奖池管理控制器
 *
 * @template TEntity of Pool
 * @extends AbstractCrudController<TEntity>
 */
#[AdminCrud(
    routePath: '/lottery/pool',
    routeName: 'lottery_pool',
)]
final class PoolCrudController extends AbstractCrudController
{
    public function __construct(private readonly AdminUrlGenerator $adminUrlGenerator)
    {
    }

    /**
     * @phpstan-return class-string<TEntity>
     */
    public static function getEntityFqcn(): string
    {
        /** @phpstan-var class-string<TEntity> */
        return Pool::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('奖池')
            ->setEntityLabelInPlural('奖池列表')
            ->setPageTitle('index', '奖池管理')
            ->setPageTitle('new', '创建奖池')
            ->setPageTitle('edit', fn (Pool $pool) => sprintf('编辑奖池: %s', $pool->getTitle() ?? 'Unknown'))
            ->setPageTitle('detail', fn (Pool $pool) => sprintf('奖池详情: %s', $pool->getTitle() ?? 'Unknown'))
            ->setHelp('index', '这里列出了所有的奖池，每个奖池包含多个奖品')
            ->setDefaultSort(['id' => 'DESC'])
            ->setSearchFields(['id', 'title'])
            ->setFormThemes(['@EasyAdmin/crud/form_theme.html.twig', '@EasyAdmin/crud/field/collection.html.twig'])
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

        yield TextField::new('title', '奖池名称')
            ->setRequired(true)
            ->setHelp('奖池的显示名称')
        ;

        // 在列表页显示更多信息
        if (Crud::PAGE_INDEX === $pageName) {
            yield IntegerField::new('prizes.count', '奖品数量')
                ->setLabel('奖品数量')
                ->formatValue(function (mixed $value, Pool $entity): int {
                    return $entity->getPrizes()->count();
                })
            ;

            yield IntegerField::new('activities.count', '关联活动数')
                ->setLabel('关联活动数')
                ->formatValue(function (mixed $value, Pool $entity): int {
                    return $entity->getActivities()->count();
                })
            ;
        }

        yield BooleanField::new('valid', '是否有效')
            ->renderAsSwitch(true)
        ;
    }

    /**
     * @return iterable<FieldInterface>
     */
    private function getAssociationFields(string $pageName): iterable
    {
        if (Crud::PAGE_INDEX === $pageName) {
            return;
        }

        if (Crud::PAGE_INDEX !== $pageName && Crud::PAGE_DETAIL !== $pageName) {
            yield FormField::addTab('关联信息')
                ->setIcon('fas fa-link')
            ;
        }

        yield AssociationField::new('activities', '关联活动')
            ->hideOnIndex()
            ->setRequired(false)
        ;

        yield CollectionField::new('poolAttributes', '奖池属性')
            ->hideOnIndex()
            ->setEntryIsComplex(true)
            ->setFormTypeOption('by_reference', false)
            ->useEntryCrudForm() // 使用默认表单编辑
        ;

        // 仅在详情页展示奖品列表
        if (Crud::PAGE_DETAIL === $pageName) {
            yield AssociationField::new('prizes', '奖品列表')
                ->setTemplatePath('admin/field/prizes_list.html.twig')
            ;
        }
    }

    /**
     * @return iterable<FieldInterface>
     */
    private function getAuditFields(string $pageName): iterable
    {
        if (Crud::PAGE_INDEX !== $pageName && Crud::PAGE_DETAIL !== $pageName) {
            yield FormField::addTab('审计信息')
                ->setIcon('fas fa-history')
            ;
        }

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
        // 创建一个新的操作按钮用于管理奖品
        $managePrizes = Action::new('managePrizes', '管理奖品', 'fas fa-award')
            ->linkToCrudAction('managePrizesAction')
            ->setCssClass('btn btn-primary')
        ;

        // 返回配置好的操作按钮
        return $actions
            ->add(Crud::PAGE_INDEX, $managePrizes)
            ->add(Crud::PAGE_DETAIL, $managePrizes)
            ->add(Crud::PAGE_EDIT, Action::DETAIL)
        ;
    }

    public function configureFilters(Filters $filters): Filters
    {
        return $filters
            ->add(TextFilter::new('title', '奖池名称'))
            ->add(BooleanFilter::new('valid', '是否有效'))
        ;
    }

    /**
     * 管理奖品操作
     */
    #[AdminAction(routeName: 'admin_pool_prizes', routePath: '{entityId}/prizes')]
    public function managePrizesAction(AdminContext $context): Response
    {
        $pool = $context->getEntity()->getInstance();
        assert($pool instanceof Pool);

        // 生成PrizeCrudController的列表页URL，带上奖池筛选条件
        $url = $this->adminUrlGenerator
            ->setController(PrizeCrudController::class)
            ->setAction(Action::INDEX)
            ->setEntityId(null)
            ->set('filters[pool][comparison]', '=')
            ->set('filters[pool][value]', $pool->getId())
            ->generateUrl()
        ;

        // 重定向到奖品列表页
        return $this->redirect($url);
    }
}
