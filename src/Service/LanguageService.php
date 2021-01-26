<?php

namespace App\Service;

use App\Entity\Language;

class LanguageService
{

    public static function getLang($em, $_locale)
    {
        $lang = $em->getRepository(Language::class)->findOneBy(array('code' => $_locale));
        return $lang;
    }

    public static function getLanguages($em)
    {
        $languages = $em->getRepository(Language::class)->findAll();
        return $languages;
    }
}
