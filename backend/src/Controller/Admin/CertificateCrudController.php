<?php

namespace App\Controller\Admin;

use App\Entity\Certificate;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class CertificateCrudController extends AbstractCrudController
{
    use Traits\ReadOnlyTrait;

    public static function getEntityFqcn(): string
    {
        return Certificate::class;
    }

    public function configureFields(string $pageName): iterable
    {
        yield AssociationField::new(propertyName: 'user', label: 'Adhérent')
            ->setFormTypeOption(optionName: 'disabled', optionValue: true);

        yield TextField::new(propertyName: 'originalFileName', label: 'Nom du fichier')
            ->setFormTypeOption(optionName: 'disabled', optionValue: true);

        yield DateField::new(propertyName: 'expireAt', label: 'Date d\'expiration')
            ->setFormat(dateFormatOrPattern: 'full');
    }

    public function configureActions(Actions $actions): Actions
    {
        $actions
            ->disable(Action::NEW, Action::DELETE)
            ->add(pageName: Crud::PAGE_DETAIL, actionNameOrObject: Action::DETAIL)
        ;

        return $actions;
    }
}
