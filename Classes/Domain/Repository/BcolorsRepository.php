<?php
namespace MikelMade\Mmimagemap\Domain\Repository;

/***************************************************************
 *	Copyright notice
 *
 *	(c) 2018 MikelMade (http://www.mikelmade.de)
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
 * @package mmimagemap
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 *
 */
class BcolorsRepository extends \TYPO3\CMS\Extbase\Persistence\Repository
{
    
    /**
     * Life cycle method.
     *
     * @return void
     */
    public function initializeObject()
    {
    }
    
    /**
        * gets all bcolors data for a given bcolors id
        *
        * @param integer $bcolorsid
        * @param string $fields
        *
        * return string or array
        */
    public function GetBcolorsData($bcolorsid, $fields='*')
    {
        $query = $this->createQuery();
        $query->statement('SELECT '.$fields.' from tx_mmimagemap_domain_model_bcolors where uid='.(int)$bcolorsid);
        $res = $query->execute(true);
        if ($fields != '*' && !preg_match('/\,/', $fields)) {
            return $res[0][$fields];
        }
        return $res[0];
    }
    
    /**
        * gets all bcolors for a given map id
        *
        * @param integer $mapid
        *
        * return string or array
        */
    public function GetBcolors($mapid)
    {
        $query = $this->createQuery();
        $query->statement('SELECT * from tx_mmimagemap_domain_model_bcolors where mapid='.(int)$mapid);
        $res = $query->execute(true);
        return $res;
    }
}
