<?php

namespace App\Controller\Admin;

use App\Controller\Admin\Traits\ReadOnlyTrait;
use App\Entity\Contact;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\EmailField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class ContactCrudController extends AbstractCrudController
{
    use readOnlyTrait;

    public static function getEntityFqcn(): string
    {
        return Contact::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular(label: 'Contact')
            ->setEntityLabelInPlural(label: 'Contacts')
            ->setPageTitle(pageName: 'index', title: 'Message reçu depuis le formulaire de contact')
            ->setPaginatorPageSize(maxResultsPerPage: 20)
            ->setPageTitle(
                pageName: 'detail',
                title: fn (Contact $contact) => '👁️ Message de '.$contact->getFullname().' - '.$contact->getEmail()
            )
            ->setDateTimeFormat(
                dateFormatOrPattern: dateTimeField::FORMAT_MEDIUM,
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

        yield TextField::new(propertyName: 'subject', label: 'Sujet')
            ->setCssClass(cssClass: 'text-capitalize')
        ;

        yield TextareaField::new(propertyName: 'message', label: 'Message')
            ->setCssClass(cssClass: 'text-capitalize')
        ;

        yield DateTimeField::new(propertyName: 'createdAt', label: 'Date de création')
        ;
    }
}
