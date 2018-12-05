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
class Map extends \TYPO3\CMS\Extbase\DomainObject\AbstractEntity
{
    
    /**
        * uid
        *
        * @lazy
        * @var \integer
        */
    protected $uid;

    /**
        * name
        *
        * @lazy
        * @var \string
        */
    protected $name;

    /**
        * imgfile
        *
        * @lazy
        * @var \string
        */
    protected $imgfile;
    
    /**
        * altfile
        *
        * @lazy
        * @var \string
        */
    protected $altfile;
    
    /**
        * folder
        *
        * @lazy
        * @var \string
        */
    protected $folder;
    
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
        * Returns the name
        *
        * @return \string $name
        */
    public function getName()
    {
        return $this->name;
    }
    
    /**
        * Sets the name
        *
        * @param \string $name
        */
    public function setName($name)
    {
        $this->name = $name;
    }
    
    /**
        * Returns the imgfile
        *
        * @return \string $imgfile
        */
    public function getImgfile()
    {
        return $this->imgfile;
    }
    
    /**
        * Sets the imgfile
        *
        * @param \string $imgfile
        */
    public function setImgfile($imgfile)
    {
        $this->imgfile = $imgfile;
    }
    
    /**
        * Returns the altfile
        *
        * @return \string $altfile
        */
    public function getAltfile()
    {
        return $this->altfile;
    }
    
    /**
        * Sets the altfile
        *
        * @param \string $altfile
        */
    public function setAltfile($altfile)
    {
        $this->altfile = $altfile;
    }
    
    /**
        * Returns the folder
        *
        * @return \string $folder
        */
    public function getFolder()
    {
        return $this->folder;
    }
    
    /**
        * Sets the folder
        *
        * @param \string $folder
        */
    public function setFolder($folder)
    {
        $this->folder = $folder;
    }
}
