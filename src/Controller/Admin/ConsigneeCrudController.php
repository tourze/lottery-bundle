<?php

namespace LotteryBundle\Controller\Admin;

use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TelephoneField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Filter\EntityFilter;
use EasyCorp\Bundle\EasyAdminBundle\Filter\TextFilter;
use LotteryBundle\Entity\Consignee;

/**
 * 收货信息管理控制器
 */
class ConsigneeCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Consignee::class;
    }
    
    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('收货信息')
            ->setEntityLabelInPlural('收货信息列表')
            ->setPageTitle('index', '收货信息管理')
            ->setPageTitle('new', '创建收货信息')
            ->setPageTitle('edit', fn (Consignee $consignee) => sprintf('编辑收货信息: #%s', $consignee->getId()))
            ->setPageTitle('detail', fn (Consignee $consignee) => sprintf('收货详情: #%s', $consignee->getId()))
            ->setHelp('index', '这里列出了所有的收货信息')
            ->setDefaultSort(['id' => 'DESC'])
            ->setSearchFields(['id', 'realName', 'mobile', 'address']);
    }
    
    public function configureFields(string $pageName): iterable
    {
        // ID字段
        yield IdField::new('id')
            ->hideOnForm()
            ->setMaxLength(9999);
            
        // 关联抽奖机会
        yield AssociationField::new('chance', '关联抽奖机会')
            ->setRequired(true);
            
        // 收货人信息
        yield TextField::new('realName', '收货人姓名')
            ->setRequired(true);
            
        yield TelephoneField::new('mobile', '手机号')
            ->setRequired(true);
            
        // 地址信息
        yield TextField::new('address', '详细地址')
            ->setRequired(true);
            
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
            ->add(EntityFilter::new('chance', '关联抽奖机会'))
            ->add(TextFilter::new('realName', '收货人姓名'))
            ->add(TextFilter::new('mobile', '手机号'))
            ->add(TextFilter::new('address', '详细地址'));
    }
}
