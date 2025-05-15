<?php

namespace LotteryBundle\Controller\Admin;

use EasyCorp\Bundle\EasyAdminBundle\Attribute\AdminAction;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use EasyCorp\Bundle\EasyAdminBundle\Context\AdminContext;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\FormField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\MoneyField;
use EasyCorp\Bundle\EasyAdminBundle\Field\NumberField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Filter\BooleanFilter;
use EasyCorp\Bundle\EasyAdminBundle\Filter\EntityFilter;
use EasyCorp\Bundle\EasyAdminBundle\Filter\NumericFilter;
use EasyCorp\Bundle\EasyAdminBundle\Filter\TextFilter;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use LotteryBundle\Entity\Pool;
use LotteryBundle\Entity\Prize;
use Symfony\Component\HttpFoundation\Response;

/**
 * 奖品管理控制器
 */
class PrizeCrudController extends AbstractCrudController
{
    public function __construct(
        private readonly AdminUrlGenerator $adminUrlGenerator,
    )
    {
    }

    public static function getEntityFqcn(): string
    {
        return Prize::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('奖品')
            ->setEntityLabelInPlural('奖品列表')
            ->setPageTitle('index', '奖品管理')
            ->setPageTitle('new', '创建奖品')
            ->setPageTitle('edit', fn(Prize $prize) => sprintf('编辑奖品: %s', $prize->getName()))
            ->setPageTitle('detail', fn(Prize $prize) => sprintf('奖品详情: %s', $prize->getName()))
            ->setHelp('index', '这里列出了所有的奖品，奖品归属于特定奖池')
            ->setDefaultSort(['id' => 'DESC'])
            ->setSearchFields(['id', 'name', 'content', 'type']);
    }

    public function configureFields(string $pageName): iterable
    {
        $referrerPoolId = $this->getContext()->getRequest()->query->get('referrerPoolId');

        // ID字段
        yield IdField::new('id')
            ->hideOnForm()
            ->setMaxLength(9999);

        // 表单分组为标签页，但仅适用于表单页面
        if (Crud::PAGE_INDEX !== $pageName && Crud::PAGE_DETAIL !== $pageName) {
            // 基本信息标签页
            yield FormField::addTab('基本信息')
                ->setIcon('fas fa-info-circle');
        }

        // 基本信息
        yield TextField::new('name', '奖品名称')
            ->setRequired(true);

        yield TextareaField::new('content', '奖品描述')
            ->hideOnIndex()
            ->setRequired(false);

        yield TextField::new('type', '类型')
            ->setRequired(true);

        yield TextField::new('typeId', '类型值ID')
            ->hideOnIndex()
            ->setRequired(false);

        if (Crud::PAGE_INDEX !== $pageName && Crud::PAGE_DETAIL !== $pageName) {
            // 数量与概率标签页
            yield FormField::addTab('数量与概率')
                ->setIcon('fas fa-chart-bar');
        }

        // 奖品数量与概率
        yield IntegerField::new('amount', '单次派发数量')
            ->hideOnIndex()
            ->setRequired(false)
            ->setHelp('单次抽中派发的数量，默认为1');

        yield IntegerField::new('quantity', '总数量')
            ->setRequired(true);

        yield IntegerField::new('dayLimit', '每日数量')
            ->hideOnIndex()
            ->setRequired(false);

        yield IntegerField::new('probability', '概率数')
            ->setRequired(true)
            ->setHelp('概率数值，数值越大中奖概率越高');

        yield TextareaField::new('probabilityExpression', '概率表达式')
            ->hideOnIndex()
            ->setRequired(false);

        yield MoneyField::new('value', '奖品价值')
            ->setCurrency('CNY')
            ->setStoredAsCents(false)
            ->hideOnIndex()
            ->setRequired(false);

        yield NumberField::new('expireDay', '派发后有效天数')
            ->hideOnIndex()
            ->setRequired(false);

        yield DateTimeField::new('expireTime', '派发后到期时间')
            ->hideOnIndex()
            ->setRequired(false)
            ->setFormat('yyyy-MM-dd HH:mm:ss');

        if (Crud::PAGE_INDEX !== $pageName && Crud::PAGE_DETAIL !== $pageName) {
            // 图片标签页
            yield FormField::addTab('图片设置')
                ->setIcon('fas fa-images');
        }

        // 图片相关
        yield ImageField::new('picture', '主图')
            ->setBasePath('/uploads/images')
            ->setUploadDir('public/uploads/images')
            ->setUploadedFileNamePattern('[randomhash].[extension]')
            ->setRequired(false);

        yield ImageField::new('secondPicture', '选中图片')
            ->hideOnIndex()
            ->setBasePath('/uploads/images')
            ->setUploadDir('public/uploads/images')
            ->setUploadedFileNamePattern('[randomhash].[extension]')
            ->setRequired(false);

        yield ImageField::new('pickPicture', '中奖图片')
            ->hideOnIndex()
            ->setBasePath('/uploads/images')
            ->setUploadDir('public/uploads/images')
            ->setUploadedFileNamePattern('[randomhash].[extension]')
            ->setRequired(false);

        yield ImageField::new('consigneePicture', '地址图片')
            ->hideOnIndex()
            ->setBasePath('/uploads/images')
            ->setUploadDir('public/uploads/images')
            ->setUploadedFileNamePattern('[randomhash].[extension]')
            ->setRequired(false);

        if (Crud::PAGE_INDEX !== $pageName && Crud::PAGE_DETAIL !== $pageName) {
            // 配置标签页
            yield FormField::addTab('配置设置')
                ->setIcon('fas fa-cog');
        }

        // 展示控制
        yield BooleanField::new('canShow', '是否参与轮播')
            ->hideOnIndex()
            ->renderAsSwitch(true);

        yield BooleanField::new('canShowPrize', '是否在奖品列表展示')
            ->hideOnIndex()
            ->renderAsSwitch(true);

        yield BooleanField::new('isDefault', '兜底奖项')
            ->hideOnIndex()
            ->renderAsSwitch(true)
            ->setHelp('如果用户没中任何奖项，将会中此兜底奖项');

        yield BooleanField::new('needConsignee', '需要收货地址')
            ->hideOnIndex()
            ->renderAsSwitch(true);

        yield BooleanField::new('needReview', '需要审核')
            ->hideOnIndex()
            ->renderAsSwitch(true);

        // 关联实体
        $poolField = AssociationField::new('pool', '所属奖池')
            ->setRequired(true);

        // 如果从奖池页面跳转过来，预设奖池值
        if ($referrerPoolId && Crud::PAGE_NEW === $pageName) {
            $poolField->setFormTypeOption('data', $this->getPoolById($referrerPoolId));
        }

        yield $poolField;

        yield AssociationField::new('stocks', '库存信息')
            ->hideOnIndex()
            ->setRequired(false);

        // 排序
        yield IntegerField::new('sortNumber', '排序值')
            ->hideOnIndex()
            ->setHelp('值越大排序越靠前')
            ->setRequired(false);

        // 状态信息
        yield BooleanField::new('valid', '是否有效')
            ->renderAsSwitch(true);

        // 审计信息
        yield TextField::new('createdBy', '创建人')
            ->hideOnForm()
            ->hideOnIndex();

        yield TextField::new('updatedBy', '更新人')
            ->hideOnForm()
            ->hideOnIndex();

        yield DateTimeField::new('createTime', '创建时间')
            ->hideOnForm()
            ->hideOnIndex()
            ->setFormat('yyyy-MM-dd HH:mm:ss');

        yield DateTimeField::new('updateTime', '更新时间')
            ->hideOnForm()
            ->setFormat('yyyy-MM-dd HH:mm:ss');
    }

    public function configureActions(Actions $actions): Actions
    {
        // 添加返回奖池按钮
        $backToPool = Action::new('backToPool', '返回奖池', 'fas fa-arrow-left')
            ->linkToCrudAction('backToPoolAction')
            ->setCssClass('btn btn-secondary');

        // 检查是否有过滤条件，如果有表示是从奖池页面跳转而来
        $request = $this->getContext()->getRequest();
        $poolFilter = $request->query->get('filters', [])['pool'] ?? null;

        if ($poolFilter) {
            $actions->add(Crud::PAGE_INDEX, $backToPool);
        }

        return $actions
            ->add(Crud::PAGE_INDEX, Action::DETAIL)
            ->add(Crud::PAGE_EDIT, Action::DETAIL)
            ->reorder(Crud::PAGE_INDEX, [Action::DETAIL, Action::EDIT, Action::DELETE]);
    }

    public function configureFilters(Filters $filters): Filters
    {
        return $filters
            ->add(TextFilter::new('name', '奖品名称'))
            ->add(TextFilter::new('type', '奖品类型'))
            ->add(EntityFilter::new('pool', '所属奖池'))
            ->add(NumericFilter::new('quantity', '总数量'))
            ->add(NumericFilter::new('probability', '概率数'))
            ->add(BooleanFilter::new('isDefault', '兜底奖项'))
            ->add(BooleanFilter::new('valid', '是否有效'));
    }

    /**
     * 返回奖池页面
     */
    #[AdminAction('{entityId}/back-to-pool', 'admin_prize_back_to_pool')]
    public function backToPoolAction(AdminContext $context): Response
    {
        // 获取池ID
        $poolId = $context->getRequest()->query->get('filters', [])['pool']['value'] ?? null;

        if (!$poolId) {
            // 如果没有poolId，返回到所有奖池列表
            $url = $this->adminUrlGenerator
                ->setController(PoolCrudController::class)
                ->setAction(Action::INDEX)
                ->generateUrl();
        } else {
            // 如果有poolId，返回到特定奖池详情
            $url = $this->adminUrlGenerator
                ->setController(PoolCrudController::class)
                ->setAction(Action::DETAIL)
                ->setEntityId($poolId)
                ->generateUrl();
        }

        return $this->redirect($url);
    }

    /**
     * 通过ID获取奖池实体
     */
    private function getPoolById(int $id): ?Pool
    {
        return $this->container->get('doctrine')
            ->getRepository(Pool::class)
            ->find($id);
    }
}
