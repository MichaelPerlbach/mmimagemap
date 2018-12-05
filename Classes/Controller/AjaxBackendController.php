<?php

/***************************************************************
 *	Copyright notice
 *
 *	(c) 2018 MikelMade (www.mikelmade.de)
 *	All rights reserved
 *
 *	This script is part of the TYPO3 project. The TYPO3 project is
 *	free software; you can redistribute it and/or modify
 *	it under the terms of the GNU General Public License as published by
 *	the Free Software Foundation; either version 3 of the License, or
 *	(at your option) any later version.
 *
 *	The GNU General Public License can be found at
 *	http://www.gnu.org/copyleft/gpl.html.
 *
 *	This script is distributed in the hope that it will be useful,
 *	but WITHOUT ANY WARRANTY; without even the implied warranty of
 *	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.	See the
 *	GNU General Public License for more details.
 *
 *	This copyright notice MUST APPEAR in all copies of the script!
 ***************************************************************/

/**
 *
    *
    *	@package	mmimagemap
    *	@license	http://www.gnu.org/licenses/gpl.html	GNU	General	Public	License,	version	3	or	later
    *
    */
class Tx_mmimagemap_Controller_AjaxBackendController extends \TYPO3\CMS\Extbase\Mvc\Controller\ActionController
{
    
    /**
        *
        *	@var \TYPO3\CMS\Extbase\Domain\Repository\MapRepository
        *	@inject
        */
    protected $mapRepository;
    
    /**
        *
        *	@var	\MikelMade\Argenproducts\Domain\Repository\AreaRepository
        *	@inject
        */
    protected $areaRepository;
    
    /**
        *	pointRepository
        *
        *	@var	\MikelMade\Argenproducts\Domain\Repository\PointRepository
        *	@inject
        */
    protected $pointRepository;
    
    /**
        *	bcolorsRepository
        *
        *	@var	\MikelMade\Argenproducts\Domain\Repository\BcolorsRepository
        *	@inject
        */
    protected $bcolorsRepository;
    
    
    /**
        *	contentpopupRepository
        *
        *	@var	\MikelMade\Argenproducts\Domain\Repository\ContentpopupRepository
        *	@inject
        */
    protected $contentpopupRepository;
    
    /**
        *	action	some
        *
        *	@return string
    */
    public function someAction()
    {
    }
}
