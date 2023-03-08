<?php

use Symfony\Component\Form\DataTransformerInterface;
//use Symfony\Component\Validator\Constraints\DateTime;
use DateTimeImmutable;
use DateTime;


class ConvertirFormatDate implements DataTransformerInterface{

    public function transform($value)
    {
        // Transforme la DateTime en DateTimeImmutable pour l'injection dans l'entité
        $dateTimeImmutable = DateTimeImmutable::createFromMutable($value);
        //return (DateTimeImmutable)$value;
        return $dateTimeImmutable;

    }

    public function reverseTransform($value)
    {
        // Transforme la DateTimeImmutable en DateTime pour l'affichage dans le formulaire
        $dateTime = DateTime::createFromImmutable($value);
        
        //return (DateTime)$value;
        return $dateTime;
    }
}


