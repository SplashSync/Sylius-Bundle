<?php

namespace Splash\SyliusSplashPlugin\Objects\Common;

trait TimestampTrait
{
    /**
     * Build Fields using FieldFactory
     */
    protected function buildTimestampFields(): void
    {
        //====================================================================//
        // Creation Date
        $this->fieldsFactory()->create(SPL_T_DATETIME)
            ->identifier("createdAt")
            ->name("Created")
            ->group("Meta")
            ->microData("http://schema.org/DataFeedItem", "dateCreated")
            ->isReadOnly()
        ;
        //====================================================================//
        // Last Change Date
        $this->fieldsFactory()->create(SPL_T_DATETIME)
            ->identifier("updatedAt")
            ->name("Updated")
            ->group("Meta")
            ->microData("http://schema.org/DataFeedItem", "dateModified")
            ->isReadOnly()
        ;
    }

    /**
     * Read requested Field
     *
     * @param string $key       Input List Key
     * @param string $fieldName Field Identifier / Name
     */
    protected function getTimestampFields(string $key, string $fieldName): void
    {
        switch ($fieldName) {
            //====================================================================//
            // Static Id Readings
            case 'createdAt':
            case 'updatedAt':
                $this->getGenericDateTime($fieldName);

                break;
           default:
                return;
        }
        unset($this->in[$key]);
    }
}