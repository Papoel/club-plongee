<?php

namespace App\Controller\Admin;

use App\Entity\User;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\EmailField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TelephoneField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class UserCrudController extends AbstractCrudController
{
    use Traits\ReadOnlyTrait;

    public static function getEntityFqcn(): string
    {
        return User::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular(label: 'Adhérent')
            ->setEntityLabelInPlural(label: 'Adhérents')
            ->setPageTitle(pageName: 'index', title: 'Liste des adhérents du club')
            ->setPaginatorPageSize(maxResultsPerPage: 20)
            ->setPageTitle(
                pageName: 'detail',
                title: fn (User $user) => '👁️ Fiche Adhérent - '.$user->getFullName()
            )
            ->setDateTimeFormat(
                dateFormatOrPattern: dateTimeField::FORMAT_LONG,
                timeFormat: dateTimeField::FORMAT_SHORT
            )
        ;
    }

    public function configureFields(string $pageName): iterable
    {
        yield TextField::new(propertyName: 'fullname', label: 'Nom')
            ->setCssClass(cssClass: 'text-capitalize')
        ;

        yield EmailField::new(propertyName: 'email', label: 'E-mail')
            ->setCssClass(cssClass: 'text-lowercase')
        ;

        yield ChoiceField::new(propertyName: 'roles', label: 'Rôle')
            ->setChoices([
                'ADMINISTRATEUR' => 'ROLE_ADMIN',
                'ADHERENT' => 'ROLE_ADHERENT',
            ])
            ->allowMultipleChoices()
            ->renderAsBadges([
                'ROLE_ADMIN' => 'danger',
                'ROLE_ADHERENT' => 'light',
            ])
            ->setColumns(cols: 'col-12 col-sm-4')
        ;

        yield TextField::new(propertyName: 'fullAddress', label: 'Adresse')
            ->setCssClass(cssClass: 'text-capitalize')
            ->setColumns(cols: 'col-12 col-sm-4')
        ;

        yield TelephoneField::new(propertyName: 'phone', label: 'Téléphone');
    }
}
