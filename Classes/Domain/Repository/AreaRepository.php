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
class AreaRepository extends \TYPO3\CMS\Extbase\Persistence\Repository
{
    
    /**
     * Life cycle method.
     *
     * @return void
     */
    public function initializeObject()
    {
        $this->areatypelabels = $this->GetAreaTypeLabels();
    }
    
    /**
        * creates an area name
        *
        * return array
        */
    public function GetAreaTypeLabels()
    {
        $areatypelabels = array(
            \TYPO3\CMS\Extbase\Utility\LocalizationUtility::translate('LLL:EXT:mmimagemap/Resources/Private/Language/locallang_be.xlf:tx_mmimagemap.rectangle', ''),
            \TYPO3\CMS\Extbase\Utility\LocalizationUtility::translate('LLL:EXT:mmimagemap/Resources/Private/Language/locallang_be.xlf:tx_mmimagemap.circle', ''),
            \TYPO3\CMS\Extbase\Utility\LocalizationUtility::translate('LLL:EXT:mmimagemap/Resources/Private/Language/locallang_be.xlf:tx_mmimagemap.polygon', ''),
            \TYPO3\CMS\Extbase\Utility\LocalizationUtility::translate('LLL:EXT:mmimagemap/Resources/Private/Language/locallang_be.xlf:tx_mmimagemap.default', '')
        );
        return $areatypelabels;
    }
    
    /**
        * creates an area name
        *
        * return string
        */
    public function GenerateAreaName()
    {
        $query = $this->createQuery();
        $query->statement('SELECT count(*) from tx_mmimagemap_domain_model_area');
        $res = $query->execute(true);
        $name = 'area_'.((int)$res[0]['count(*)']+1);
        
        return $name;
    }
    
    /**
        * gets all (or some) area data for a given area id
        *
        * @param integer $areaid
        * @param string $fields
        *
        * return string or array
        */
    public function GetAreaData($areaid, $fields='*')
    {
        $query = $this->createQuery();
        $query->statement('SELECT '.$fields.' from tx_mmimagemap_domain_model_area where uid='.(int)$areaid);
        $res = $query->execute(true);
        if ($fields != '*' && !preg_match('/\,/', $fields)) {
            return $res[0][$fields];
        }
        return $res[0];
    }
    
    /**
        * gets all points and contentpopups for a given area id
        *
        * @param integer $areaid
        * @param string $fields
        *
        * return string or array
        */
    public function GetCompleteArea($areaid)
    {
        $ret = array();
        $query = $this->createQuery();
        $query->statement('SELECT * from tx_mmimagemap_domain_model_area where uid='.(int)$areaid);
        $res = $query->execute(true);
        
        $ret['area'] = $res[0];
        $ret['area']['areatypelabel'] = $this->areatypelabels[$ret['area']['areatype']];
        $ret['points'] = $this->GetPoints($areaid, $ret['area']['areatype']);
        
        $query = $this->createQuery();
        $query->statement('SELECT * from tx_mmimagemap_domain_model_contentpopup where areaid='.(int)$areaid);
        $res = $query->execute(true);
        $ret['contentpopup'] = $res[0];
        
        return $ret;
    }
    
    /**
        * gets all points for a given area id and prepares them for the backend display
        *
        * @param integer $mapid
        *
        * return string or array
        */
    public function GetPoints($areaid, $areatype)
    {
        $query = $this->createQuery();
        $query->statement('SELECT * from tx_mmimagemap_domain_model_point where areaid='.(int)$areaid.' order by num asc');
        $res = $query->execute(true);
        $points = array();

        switch ($areatype) {
        case 0: //rectangle
            if (!empty($res)) {
                foreach ($res as $item) {
                    if ($item['num'] == 1) {
                        $points['x'] = $item['x'];
                        $points['y'] = $item['y'];
                    }
                    if ($item['num'] == 2) {
                        $points['w'] = $item['x'];
                        $points['h'] = $item['y'];
                    }
                }
            } else {
                $points['x'] = 0;
                $points['y'] = 0;
                $points['w'] = 0;
                $points['h'] = 0;
            }
        break;
            
        case 1: //circle
            if (!empty($res)) {
                foreach ($res as $item) {
                    if ($item['num'] == 1) {
                        $points['r'] = $item['x'];
                    }
                    if ($item['num'] == 2) {
                        $points['x'] = $item['x'];
                        $points['y'] = $item['y'];
                    }
                }
            } else {
                $points['r'] = 0;
                $points['x'] = 0;
                $points['y'] = 0;
            }
        break;
            
        case 2: // polygon
            $x = array();
            $y = array();
            $num = 0;
            foreach ($res as $item) {
                $x[] = $item['x'];
                $y[] = $item['y'];
                $num++;
            }
            $points['num'] = $num;
            $points['x'] = implode(',', $x);
            $points['y'] = implode(',', $y);
            $points['points'] = $res;
        break;
        
        case 3:
        
        break;
    }
    
        return $points;
    }
    
    
    /**
        * gets all areas for a given map id
        *
        * @param integer $mapid
        *
        * return string or array
        */
    public function GetAreas($mapid)
    {
        $areatypelabels = $this->GetAreaTypeLabels();
        $query = $this->createQuery();
        $query->statement('SELECT * from tx_mmimagemap_domain_model_area where mapid='.(int)$mapid);
        $res = $query->execute(true);
        foreach ($res as &$item) {
            $item['areatypelabel'] = $this->areatypelabels[$item['areatype']];
            $item['points'] = $this->GetPoints($item['uid'], $item['areatype']);
        }
        return $res;
    }
}
