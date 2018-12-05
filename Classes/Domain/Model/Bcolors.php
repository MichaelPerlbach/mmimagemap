<?php
namespace MikelMade\Mmimagemap\Domain\Model;

/***************************************************************
 *  Copyright notice
 *
 *  (c) 2018 MikelMade (http://www.mikelmade.de)
 *  All rights reserved
 *
 *  This script is part of the TYPO3 project. The TYPO3 project is
 *  free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 3 of the License, or
 *  (at your option) any later version.
 *
 *  The GNU General Public License can be found at
 *  http://www.gnu.org/copyleft/gpl.html.
 *
 *  This script is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  This copyright notice MUST APPEAR in all copies of the script!
 ***************************************************************/

/**
 *
 *
 * @package mmimagemap
 *
 */
 
class Bcolors extends \TYPO3\CMS\Extbase\DomainObject\AbstractEntity
{
    
    /**
        * uid
        *
        * @lazy
        * @var \integer
        */
    protected $uid;

    /**
        * mapid
        *
        * @lazy
        * @var \integer
        */
    protected $mapid;

    /**
        * colorname
        *
        * @lazy
        * @var \string
        */
    protected $colorname;
    
    /**
        * color
        *
        * @lazy
        * @var \string
        */
    protected $color;
    
    /**
        * Returns the uid
        *
        * @return \integer $uid
        */
    public function getId()
    {
        return $this->uid;
    }
    
    /**
        * Returns the mapid
        *
        * @return \integer $mapid
        */
    public function getMapid()
    {
        return $this->mapid;
    }
    
    /**
        * Sets the mapid
        *
        * @param \integer $mapid
        * @return void
        */
    public function setMapid($mapid)
    {
        $this->mapid = $mapid;
    }
    
    /**
        * Returns the colorname
        *
        * @return \string $colorname
        */
    public function getColorname()
    {
        return $this->colorname;
    }
    
    /**
        * Sets the colorname
        *
        * @param \integer $colorname
        * @return void
        */
    public function setColorname($colorname)
    {
        $this->colorname = $colorname;
    }
    
    /**
        * Returns the color
        *
        * @return \string $color
        */
    public function getColor()
    {
        return $this->color;
    }
    
    /**
        * Sets the color
        *
        * @param \string $color
        * @return void
        */
    public function setColor($color)
    {
        $this->color = $color;
    }
}
