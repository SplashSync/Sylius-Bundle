<?php

/*
 *  This file is part of SplashSync Project.
 *
 *  Copyright (C) 2015-2019 Splash Sync  <www.splashsync.com>
 *
 *  This program is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 *
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 */

namespace Splash\Sylius\Objects\ThirdParty;

use DateTime;
use Splash\Client\Splash;
use Sylius\Component\Core\Model\CustomerInterface;

/**
 * Sylius Customers Main Fields
 */
trait MainTrait
{
    /**
     * Build Fields using FieldFactory
     */
    public function buildMainFields()
    {
        //====================================================================//
        // Phone
        $this->fieldsFactory()->create(SPL_T_PHONE)
            ->Identifier("phoneNumber")
            ->Name("Phone")
            ->isLogged()
            ->MicroData("http://schema.org/Person", "telephone")
            ->isListed();

        //====================================================================//
        // Gender Name
        $this->fieldsFactory()->create(SPL_T_VARCHAR)
            ->Identifier("gender")
            ->Name("Social Title")
            ->MicroData("http://schema.org/Person", "honorificPrefix")
            ->isReadOnly();

        //====================================================================//
        // Gender Type
        $this->fieldsFactory()->create(SPL_T_INT)
            ->Identifier("gender_type")
            ->Name("Social Title (ID)")
            ->MicroData("http://schema.org/Person", "gender")
            ->Description("Social Title : 0 => Male // 1 => Female // 2 => Neutral")
            ->addChoices(array("0" => "Male", "1" => "Female", "2" => "Unknown"))
            ->isNotTested();

        //====================================================================//
        // Birth Date
        $this->fieldsFactory()->create(SPL_T_DATE)
            ->Identifier("birthday")
            ->Name("Birthday")
            ->MicroData("http://schema.org/Person", "birthDate");

        //====================================================================//
        // Newsletter
        $this->fieldsFactory()->create(SPL_T_BOOL)
            ->Identifier("SubscribedToNewsletter")
            ->Name("Newsletter")
            ->MicroData("http://schema.org/Organization", "newsletter");
    }

    /**
     * Read requested Field
     *
     * @param string $key       Input List Key
     * @param string $fieldName Field Identifier / Name
     */
    public function getMainFields($key, $fieldName)
    {
        switch ($fieldName) {
            //====================================================================//
            // Direct Readings
            case 'phoneNumber':
                $this->getGeneric($fieldName);

                break;
            case 'gender':
                $this->out[$fieldName] = $this->getGender();

                break;
            case 'gender_type':
                $this->out[$fieldName] = $this->getGenderType();

                break;
            case 'birthday':
                $birthday = $this->object->getBirthday();
                $this->out[$fieldName] = $birthday ? $birthday->format(SPL_T_DATECAST) : "";

                break;
            case 'SubscribedToNewsletter':
                $this->out[$fieldName] = $this->object->isSubscribedToNewsletter();

                break;
            default:
                return;
        }
        unset($this->in[$key]);
    }

    /**
     * Write Given Fields
     *
     * @param string $fieldName Field Identifier / Name
     * @param mixed  $fieldData Field Data
     */
    public function setMainFields($fieldName, $fieldData)
    {
        switch ($fieldName) {
            case 'phoneNumber':
                $this->setGeneric($fieldName, $fieldData);

                break;
            case 'gender_type':
                $this->setGenderType($fieldData);

                break;
            case 'birthday':
                $birthday = $this->object->getBirthday();
                if (empty($birthday) || ($birthday->format(SPL_T_DATECAST) != $fieldData)) {
                    $this->object->setBirthday($fieldData ? new DateTime($fieldData) : null);
                    $this->needUpdate();
                }

                break;
            case 'SubscribedToNewsletter':
                if ($fieldData != $this->object->isSubscribedToNewsletter()) {
                    $this->object->setSubscribedToNewsletter($fieldData);
                    $this->needUpdate();
                }

                break;
            default:
                return;
        }
        unset($this->in[$fieldName]);
    }

    /**
     * Override Sylius Getter for Translations
     */
    private function getGender()
    {
        switch ($this->object->getGender()) {
            case CustomerInterface::MALE_GENDER:
                return $this->translator->Trans("sylius.gender.male", array(), "messages");
            case CustomerInterface::FEMALE_GENDER:
                return $this->translator->Trans("sylius.gender.female", array(), "messages");
            case CustomerInterface::UNKNOWN_GENDER:
                return $this->translator->Trans("sylius.gender.unknown", array(), "messages");
            default:
                return $this->translator->Trans("sylius.gender.unknown", array(), "messages");
        }
    }

    /**
     * Convert Sylius Customer Gender Type to Splash Standard Gender Type
     */
    private function getGenderType()
    {
        switch ($this->object->getGender()) {
            case CustomerInterface::MALE_GENDER:
                return 0;
            case CustomerInterface::FEMALE_GENDER:
                return 1;
            default:
            case CustomerInterface::UNKNOWN_GENDER:
                return 2;
        }
    }

    /**
     * Convert Splash Standard Gender Type to Sylius Customer Gender Type
     *
     * @param mixed $gender
     */
    private function setGenderType($gender)
    {
        if ($gender == $this->getGenderType()) {
            return;
        }

        switch ($gender) {
            case 0:
                $this->object->setGender(CustomerInterface::MALE_GENDER);

                break;
            case 1:
                $this->object->setGender(CustomerInterface::FEMALE_GENDER);

                break;
            default:
            case 2:
                $this->object->setGender(CustomerInterface::UNKNOWN_GENDER);

                break;
        }

        $this->needUpdate();
    }
}
