<?php

namespace App\Controller\Admin;

use App\Entity\Licence;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class LicenceCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Licence::class;
    }

    public function configureFields(string $pageName): iterable
    {
        yield TextField::new(propertyName: 'number', label: 'Numéro de licence');

        yield DateField::new(propertyName: 'expireAt', label: 'Date d\'expiration')
            ->setFormat(dateFormatOrPattern: 'full');

        yield IntegerField::new(propertyName: 'divingLevel', label: 'Niveau de plongée');

        yield AssociationField::new(propertyName: 'user_licence', label: 'Adhérent');
    }
}
