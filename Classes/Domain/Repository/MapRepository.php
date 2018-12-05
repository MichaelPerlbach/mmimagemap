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
class MapRepository extends \TYPO3\CMS\Extbase\Persistence\Repository
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
        * creates a map name
        *
        * return string
        */
    public function GenerateMapName()
    {
        $query = $this->createQuery();
        $query->statement('SELECT count(*) from tx_mmimagemap_domain_model_map');
        $res = $query->execute(true);
        $name = 'map_'.((int)$res[0]['count(*)']+1);
        
        return $name;
    }
    
    /**
        * checks if an uploaded image already exists in a given directory
        * @param string $image
        *	@param string $folder
        *
        */
    public function CheckForDoubleImages($origimage, $image, $folder, $suffix=0)
    {
        $abspath = PATH_site.'fileadmin/'.$folder;
        $dh = opendir($abspath);
        $double = false;
        while (($file = readdir($dh)) !== false) {
            if ($file != '.' && $file != '..') {
                if ($image == $file) {
                    $double = true;
                    break;
                }
            }
        }
        if ($double == true) {
            $suffix++;
            $fileparts = pathinfo($origimage);
            $newfile = $fileparts['filename'].'_'.$suffix.'.'.$fileparts['extension'];
            return $this->CheckForDoubleImages($origimage, $newfile, $folder, $suffix);
        } else {
            return $image;
        }
    }
    
    /**
        * gets all maps for a given directory
        * @param string $folder
        *
        * return array
        */
    public function GetMapsFromFolder($folder)
    {
        $query = $this->createQuery();
        $query->statement('SELECT * from tx_mmimagemap_domain_model_map where folder=\''.$folder.'\'');
        $res = $query->execute(true);
        
        foreach ($res as &$item) {
            $item['thumb'] = \MikelMade\Mmimagemap\Utility\Tx_Mmimagemap_Utility_Div::CreateThumb($item['imgfile'], $item['folder']);
        }
        return $res;
    }
    
    /**
        * gets all map data for a given map id
        *
        * @param integer $mapid
        * @param string $fields
        *
        * return string or array
        */
    public function GetMapData($mapid, $fields='*')
    {
        $query = $this->createQuery();
        $query->statement('SELECT '.$fields.' from tx_mmimagemap_domain_model_map where uid='.(int)$mapid);
        $res = $query->execute(true);
        if ($fields != '*' && !preg_match('/\,/', $fields)) {
            return $res[0][$fields];
        }
        return $res[0];
    }
    
    /**
        * checks if a given image in a given directory is used by other imagemaps
        *
        * @param string $path
        * @param string $image
        *
        * return boolean
        */
    public function CheckForUnusedPic($path, $image, $mapid)
    {
        $query = $this->createQuery();
        $query->statement('SELECT uid from tx_mmimagemap_domain_model_map where folder=\''.$path.'\' and imgfile=\''.$image.'\' and uid!='.(int)$mapid);
        $res = $query->execute(true);
        if (empty($res)) {
            return true;
        }
        return false;
    }
    
    /**
        * gets colors created by users
        *
        * @param array $becolors
        * @param integer $mapid
        * @param integer $oc
        *
        * return array
        */
    public function GetAdditionalColors($becolors, $mapid, $oc)
    {
        $addquery = ($oc == 1) ? ' where mapid='.$mapid : '';
        $query = $this->createQuery();
        $query->statement('SELECT * from tx_mmimagemap_domain_model_bcolors'.$addquery);
        $res = $query->execute(true);
        
        foreach ($res as $color) {
            $becolors[] = array(
                'color' => $color['color'],
                'colorname' => $color['colorname'],
                'fixed' => 0,
                'id' => $color['uid']
            );
        }
        
        return $becolors;
    }
    
    
    /**
        * creates the map overlays
        *
        * @param integer $mapid
        * @param integer $areaid
        *
        * return boolean
        */
    public function MakeFePics($mapid, $arearepo)
    {
        $impath = $GLOBALS['TYPO3_CONF_VARS']['GFX']['processor_path'];
        if ($GLOBALS['TYPO3_CONF_VARS']['GFX']['processor'] == 'GraphicsMagick') {
            $impath .= 'gm ';
        }
        
        $query = $this->createQuery();
        $query->statement('SELECT * from tx_mmimagemap_domain_model_map where uid='.(int)$mapid);
        $res = $query->execute(true);
        
        $apic = time().'.png';
        $abs_apic = PATH_site.'uploads/tx_mmimagemap/'.$apic;
            
        $img = PATH_site.'fileadmin/'.$res[0]['folder'].$res[0]['imgfile'];
        $oldimg = PATH_site.'uploads/tx_mmimagemap/'.$res[0]['altfile'];
        $imgsize = getimagesize($img);
        
        // first, create the overlay with the borders that should always be visible on the frontend
        $query = $this->createQuery();
        $query->statement('SELECT * from tx_mmimagemap_domain_model_area where mapid='.(int)$mapid.' and fevisible=1');
        $res = $query->execute(true);
        
        if (count($res) > 0) {
            foreach ($res as $area) {
                $query2 = $this->createQuery();
                $query2->statement('SELECT * from tx_mmimagemap_domain_model_point where areaid='.(int)$area['uid'].' order by num asc');
                $allpoints = $query2->execute(true);
                
                $bc = \MikelMade\Mmimagemap\Utility\Tx_Mmimagemap_Utility_Div::ConvertToRGB($area['febordercolor']);
                
                $simg = $abs_apic;
                if (!is_file($abs_apic)) {
                    $simg = PATH_site.'typo3conf/ext/mmimagemap/Resources/Public/Images/canvas.png -resize '.$imgsize[0].'x'.$imgsize[1].'!';
                    if (preg_match("/WIN/", PHP_OS)) {
                        $simg = ' "'.PATH_site.'typo3conf/ext/mmimagemap/Resources/Public/Images/canvas.png" -resize '.$imgsize[0].'x'.$imgsize[1].'!';
                    }
                }
                switch (intval($area['areatype'])) {
                    
                    case 0: //rectangle
                    
                        if (preg_match("/WIN/", PHP_OS) && !preg_match("/\"/", $simg)) {
                            $simg = '"'.$simg.'"';
                        }
                        $points = array();
                        $allpoints = array_reverse($allpoints);
                        foreach ($allpoints as $point) {
                            $points[] = $point['x'];
                            $points[] = $point['y'];
                        }
                        
                        if (preg_match("/WIN/", PHP_OS)) {
                            exec('"'.rtrim($impath).'" convert -quality 100 '.$simg.' -stroke "rgb('.$bc.')" -strokewidth '.$area['feborderthickness'].' -fill none -draw " rectangle '.$points[2].','.$points[3].','.($points[2]+$points[0]).','.($points[3]+$points[1]).'" "'.$abs_apic.'"');
                        } else {
                            exec($impath.'convert -quality 100 '.$simg.' -stroke "rgb('.$bc.')" -strokewidth '.$area['feborderthickness'].' -fill none -draw " rectangle '.$points[2].','.$points[3].','.($points[2]+$points[0]).','.($points[3]+$points[1]).'" '.$abs_apic);
                        }
                        
                    break;
                
                    case 1: // circle
                        if (preg_match("/WIN/", PHP_OS) && !preg_match("/\"/", $simg)) {
                            $simg = '"'.$simg.'"';
                        }
                        $points = array();
                        foreach ($allpoints as $point) {
                            $points[] = $point['x'];
                            $points[] = $point['y'];
                        }
                    
                        if (preg_match("/WIN/", PHP_OS)) {
                            exec('"'.rtrim($impath).'" convert -quality 100 '.$simg.' -stroke "rgb('.$bc.')" -strokewidth '.$area['feborderthickness'].' -fill none -draw " circle '.$points[2].','.$points[3].','.($points[2]+$points[0]).','.($points[3]+$points[1]).'" "'.$abs_apic.'"');
                        } else {
                            exec($impath.'convert -quality 100 '.$simg.' -stroke "rgb('.$bc.')" -strokewidth '.$area['feborderthickness'].' -fill none -draw " circle '.$points[2].','.$points[3].','.($points[2]+$points[0]).','.($points[3]+$points[1]).'" '.$abs_apic);
                        }
                
                    break;
                
                    case 2: // polygon
                        if (preg_match("/WIN/", PHP_OS) && !preg_match("/\"/", $simg)) {
                            $simg = '"'.$simg.'"';
                        }
                    
                        $points = '';
                        foreach ($allpoints as $point) {
                            $points .= (strlen($points) == 0) ? $point['x'].','.$point['y'] : ','.$point['x'].','.$point['y'];
                        }
                
                        if (preg_match("/WIN/", PHP_OS)) {
                            exec('"'.rtrim($impath).'" convert -quality 100 '.$simg.' -stroke "rgb('.$bc.')" -strokewidth '.$area['feborderthickness'].' -fill none -draw " polygon '.$points.'" "'.$abs_apic.'"');
                        } else {
                            exec($impath.'convert -quality 100 '.$simg.' -stroke "rgb('.$bc.')" -strokewidth '.$area['feborderthickness'].' -fill none -draw " polygon '.$points.'" '.$abs_apic);
                        }
                
                    break;
                }
            
                if (file_exists($oldimg)) {
                    unlink($oldimg);
                }
                $map = $this->findByUid($mapid);
            
                if (file_exists($abs_apic)) {
                    $map->setAltfile($apic);
                } else {
                    $map->setAltfile('');
                }

                $this->update($map);
                $this->persistenceManager->persistAll();
            }
        
            // then, create all overlays which should only be visible on mouseover
            $query = $this->createQuery();
            $query->statement('SELECT * from tx_mmimagemap_domain_model_area where mapid='.(int)$mapid.' and fevisible=2');
            $res = $query->execute(true);
        
            if (count($res) > 0) {
                foreach ($res as $area) {
                    $ypic = PATH_site.'uploads/tx_mmimagemap/'.$area['uid'].'_'.$apic;
                    $timg = $ypic;
                    if (!is_file($ypic)) {
                        $timg = PATH_site.'typo3conf/ext/mmimagemap/Resources/Public/Images/canvas.png -resize '.$imgsize[0].'x'.$imgsize[1].'!';
                        if (preg_match("/WIN/", PHP_OS)) {
                            $timg = ' "'.PATH_site.'typo3conf/ext/mmimagemap/Resources/Public/Images/canvas.png" -resize '.$imgsize[0].'x'.$imgsize[1].'!';
                        }
                    }
                    $oldimg = PATH_site.'uploads/tx_mmimagemap/'.$area['fealtfile'];
                
                    $query2 = $this->createQuery();
                    $query2->statement('SELECT * from tx_mmimagemap_domain_model_point where areaid='.(int)$area['uid'].' order by num asc');
                    $allpoints = $query2->execute(true);
                
                    $bc = \MikelMade\Mmimagemap\Utility\Tx_Mmimagemap_Utility_Div::ConvertToRGB($area['febordercolor']);
                    if (preg_match("/WIN/", PHP_OS) && !preg_match("/\"/", $timg)) {
                        $timg = '"'.$timg.'"';
                    }
                
                    switch (intval($area['areatype'])) {
                        case 0: // rectangle
                            $points = array();
                            $allpoints = array_reverse($allpoints);
                            foreach ($allpoints as $point) {
                                $points[] = $point['x'];
                                $points[] = $point['y'];
                            }
                        
                            if (preg_match("/WIN/", PHP_OS)) {
                                exec('"'.rtrim($impath).'" convert -quality 100 '.$timg.' -stroke "rgb('.$bc.')" -strokewidth '.$area['feborderthickness'].' -fill none -draw " rectangle '.$points[2].','.$points[3].','.($points[2]+$points[0]).','.($points[3]+$points[1]).'" "'.$ypic.'"');
                            } else {
                                exec($impath.'convert -quality 100 '.$timg.' -stroke "rgb('.$bc.')" -strokewidth '.$area['feborderthickness'].' -fill none -draw " rectangle '.$points[2].','.$points[3].','.($points[2]+$points[0]).','.($points[3]+$points[1]).'" '.$ypic);
                            }
                
                        break;
                    
                        case 1: // circle
                            $points = array();
                            foreach ($allpoints as $point) {
                                $points[] = $point['x'];
                                $points[] = $point['y'];
                            }
                    
                            if (preg_match("/WIN/", PHP_OS)) {
                                exec('"'.rtrim($impath).'" convert -quality 100 '.$timg.' -stroke "rgb('.$bc.')" -strokewidth '.$area['feborderthickness'].' -fill none -draw " circle '.$points[2].','.$points[3].','.($points[2]+$points[0]).','.($points[3]+$points[1]).'" "'.$ypic.'"');
                            } else {
                                exec($impath.'convert -quality 100 '.$timg.' -stroke "rgb('.$bc.')" -strokewidth '.$area['feborderthickness'].' -fill none -draw " circle '.$points[2].','.$points[3].','.($points[2]+$points[0]).','.($points[3]+$points[1]).'" '.$ypic);
                            }
                        
                        break;
                    
                        case 2: // polygon
                            $points = '';
                            foreach ($allpoints as $point) {
                                $points .= (strlen($points) == 0) ? $point['x'].','.$point['y'] : ','.$point['x'].','.$point['y'];
                            }
                        
                            if (preg_match("/WIN/", PHP_OS)) {
                                exec('"'.rtrim($impath).'" convert -quality 100 '.$timg.' -stroke "rgb('.$bc.')" -strokewidth '.$area['feborderthickness'].' -fill none -draw " polygon '.$points.'" "'.$ypic.'"');
                            } else {
                                exec($impath.'convert -quality 100 '.$timg.' -stroke "rgb('.$bc.')" -strokewidth '.$area['feborderthickness'].' -fill none -draw " polygon '.$points.'" '.$ypic);
                            }

                        break;
                    }
                
                    $updatearea = $arearepo->findByUid($area['uid']);
                    if (file_exists($oldimg)) {
                        unlink($oldimg);
                    }
                    if (file_exists($ypic)) {
                        $updatearea->setFealtfile($area['uid'].'_'.$apic);
                    } else {
                        $updatearea->setFealtfile('');
                    }
                    $arearepo->update($updatearea);
                    $this->persistenceManager->persistAll();
                }
            }
        }
    }
}
