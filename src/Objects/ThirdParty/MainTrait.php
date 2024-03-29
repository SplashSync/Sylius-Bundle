<?php

/*
 *  This file is part of SplashSync Project.
 *
 *  Copyright (C) Splash Sync  <www.splashsync.com>
 *
 *  This program is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 *
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 */

namespace Splash\SyliusSplashPlugin\Objects\ThirdParty;

use Splash\Client\Splash;
use Sylius\Component\Customer\Model\CustomerInterface;

/**
 * Sylius Customers Main Fields
 */
trait MainTrait
{
    /**
     * Build Fields using FieldFactory
     */
    public function buildMainFields(): void
    {
        //====================================================================//
        // Phone
        $this->fieldsFactory()->create(SPL_T_PHONE)
            ->identifier("phoneNumber")
            ->name("Phone")
            ->microData("http://schema.org/Person", "telephone")
            ->isIndexed()
            ->isListed()
            ->isLogged()
        ;
        //====================================================================//
        // Gender Name
        $this->fieldsFactory()->create(SPL_T_VARCHAR)
            ->identifier("gender")
            ->name("Social Title")
            ->microData("http://schema.org/Person", "honorificPrefix")
            ->isReadOnly()
        ;
        //====================================================================//
        // Gender Type
        $this->fieldsFactory()->create(SPL_T_INT)
            ->identifier("gender_type")
            ->name("Social Title (ID)")
            ->microData("http://schema.org/Person", "gender")
            ->description("Social Title : 0 => Male // 1 => Female // 2 => Neutral")
            ->addChoices(array("0" => "Male", "1" => "Female", "2" => "Unknown"))
            ->isNotTested()
        ;
        //====================================================================//
        // Birth Date
        $this->fieldsFactory()->create(SPL_T_DATE)
            ->identifier("birthday")
            ->name("Birthday")
            ->microData("http://schema.org/Person", "birthDate")
        ;
        //====================================================================//
        // Newsletter
        $this->fieldsFactory()->create(SPL_T_BOOL)
            ->identifier("SubscribedToNewsletter")
            ->name("Newsletter")
            ->microData("http://schema.org/Organization", "newsletter")
        ;
    }

    /**
     * Read requested Field
     *
     * @param string $key       Input List Key
     * @param string $fieldName Field Identifier / Name
     */
    public function getMainFields(string $key, string $fieldName): void
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
                $this->getGenericDate($fieldName);

                break;
            case 'SubscribedToNewsletter':
                $this->getGenericBool($fieldName);

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
    public function setMainFields(string $fieldName, $fieldData): void
    {
        switch ($fieldName) {
            case 'phoneNumber':
                $this->setGeneric($fieldName, $fieldData);

                break;
            case 'gender_type':
                $this->setGenderType($fieldData);

                break;
            case 'birthday':
                $this->setGenericDate($fieldName, $fieldData);

                break;
            case 'SubscribedToNewsletter':
                $this->setGenericBool($fieldName, $fieldData);

                break;
            default:
                return;
        }
        unset($this->in[$fieldName]);
    }

    /**
     * Override Sylius Getter for Translations
     *
     * @return string
     */
    private function getGender(): string
    {
        switch ($this->object->getGender()) {
            case CustomerInterface::MALE_GENDER:
                return $this->translator->trans("sylius.gender.male", array(), "messages");
            case CustomerInterface::FEMALE_GENDER:
                return $this->translator->trans("sylius.gender.female", array(), "messages");
            default:
            case CustomerInterface::UNKNOWN_GENDER:
                return $this->translator->trans("sylius.gender.unknown", array(), "messages");
        }
    }

    /**
     * Convert Sylius Customer Gender Type to Splash Standard Gender Type
     *
     * @return int
     */
    private function getGenderType(): int
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
    private function setGenderType($gender): void
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
