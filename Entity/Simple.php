<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Splash\Bundle\Annotation as SPL;
//use Splash\Bundle\Annotation\SplashField as Field;

/**
 * @ORM\Entity()
 * @ORM\Table(name="app__simple")
 * @ORM\HasLifecycleCallbacks
 * 
 * @SPL\Object(  type="simple",
 *          disabled=true,
 *          name="Simple Object",
 *          description="A Simple object for testing Splash Sync"
 * )
 */
class Simple 
{
//==============================================================================
//      Definition           
//==============================================================================
        
    
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */    
    protected $id;
    
    /**
     * @ORM\Column(name="boolean", type="boolean", nullable=TRUE)
     * @SPL\Field(  
     *          id      =   "boolean",
     *          type    =   "bool",
     *          name    =   "Simple Boolean",
     *          inlist  =   true,
     * )
     */
    protected $boolean;
    
    /**
     * @ORM\Column(name="_integer", type="integer", nullable=TRUE)
     * @SPL\Field(  
     *          id      =   "integer",
     *          type    =   "int",
     *          name    =   "Simple Integer",
     *          itemtype= "http://schema.org/PostalAddress", itemprop="postalCode",
     *          inlist  =   true,
     * )
     */
    protected $integer;
    
    /**
     * @ORM\Column(name="string", type="string", length=250, nullable=TRUE)
     * @SPL\Field(  
     *          id      =   "varchar",
     *          type    =   "varchar",
     *          name    =   "Simple Varchar",
     *          itemtype= "http://schema.org/Person", itemprop="familyName",
     *          inlist  =   true,
     *          required=   true,
     * )
     */
    protected $varchar;
    
    /**
     * @ORM\Column(name="choice", type="string", length=250, nullable=TRUE)
     * @SPL\Field(  
     *          id      =   "choice",
     *          type    =   "varchar",
     *          name    =   "Simple Choice",
     *          choices =   {"USD" : "US Dollars","EUR" : "Euro"},
     *          itemtype= "http://schema.org/PostalAddress", itemprop="postalCode",
     *          group   =   "Structured"
     * )
     */
    protected $choice;
    
    /**
     * @ORM\Column(name="_date", type="date", nullable=TRUE)
     * @SPL\Field(  
     *          id      =   "date",
     *          type    =   "date",
     *          name    =   "Simple Date",
     * )
     */
    protected $date;
    
    
//==============================================================================
//      Lifecycles Events         
//==============================================================================

    
//==============================================================================
//      Getters & Setters          
//==============================================================================
    
    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set boolean
     *
     * @param boolean $boolean
     *
     * @return Simple
     */
    public function setBoolean($boolean)
    {
        $this->boolean = $boolean;

        return $this;
    }

    /**
     * Get boolean
     *
     * @return boolean
     */
    public function getBoolean()
    {
        return $this->boolean;
    }

    /**
     * Set integer
     *
     * @param integer $integer
     *
     * @return Simple
     */
    public function setInteger($integer)
    {
        $this->integer = $integer;

        return $this;
    }

    /**
     * Get integer
     *
     * @return integer
     */
    public function getInteger()
    {
        return $this->integer;
    }

    /**
     * Set varchar
     *
     * @param string $varchar
     *
     * @return Simple
     */
    public function setVarchar($varchar)
    {
        $this->varchar = $varchar;

        return $this;
    }

    /**
     * Get varchar
     *
     * @return string
     */
    public function getVarchar()
    {
        return $this->varchar;
    }

    /**
     * Set choice
     *
     * @param integer $choice
     *
     * @return Simple
     */
    public function setChoice($choice)
    {
        $this->choice = $choice;

        return $this;
    }

    /**
     * Get choice
     *
     * @return integer
     */
    public function getChoice()
    {
        return $this->choice;
    }


    /**
     * Set date
     *
     * @param \DateTime $date
     *
     * @return Simple
     */
    public function setDate($date)
    {
        $this->date = $date;
        return $this;
    }

    /**
     * Get date
     *
     * @return \DateTime
     */
    public function getDate()
    {
        return $this->date;
    }
}
