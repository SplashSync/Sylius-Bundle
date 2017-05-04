<?php

namespace Splash\Sylius\Objects\Traits;

use Splash\Bundle\Annotation as SPL;

trait ProductSlugTrait {
    
    /**
     * @SPL\Field(  
     *          id      =   "slug",
     *          type    =   "mvarchar",
     *          name    =   "Slug (Url)",
     *          itemtype=   "http://schema.org/Product", itemprop="urlRewrite",
     *          group   =   "SEO",
     *          asso    =   { "name" },
     * )
     */
    protected $slug;      
    
    public function getSlug($Variant)
    {
        $Response = array();
        foreach ($Variant->getProduct()->getTranslations() as $LanguageCode => $Translation) {
            $Value = $Translation->getSlug();
            //====================================================================//
            // Detect Encoded Value
            $DecodedValue = Null;
            sscanf($Value, $Translation->getId() . "-%s" , $DecodedValue);
            if ($DecodedValue) {
                $Value = $DecodedValue;
            }
            $Response[$LanguageCode] = $Value;
        }
        return $Response;
    } 
    
    public function setSlug($Variant, $Data)
    {
        var_dump($Data);
        $Translations   =   $Variant->getProduct()->getTranslations();
//        dump($Translations);
        foreach ($Data as $LanguageCode => $Value) {
            if (!isset($Translations[$LanguageCode])) {
                //====================================================================//
                // Add Translation
                $Translation = new \Sylius\Component\Core\Model\ProductTranslation();
                $Translation->setLocale($LanguageCode);
                $Translation->setTranslatable($Variant->getProduct());
                $Translations[$LanguageCode] = $Translation;
            }
            //====================================================================//
            // Encode Value if Modified
            if ( $Translations[$LanguageCode]->getId() && ($Translations[$LanguageCode]->getSlug() !== $Value) ) {
                $Value = $Translations[$LanguageCode]->getId() . "-" . $Value;
            }
            $Translations[$LanguageCode]->setSlug($Value);
        }
    }
    
}
