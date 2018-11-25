<?php
namespace MikelMade\Mmimagemap\Controller;

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

use TYPO3\CMS\Backend\Form\Element\InputLinkElement;
use TYPO3\CMS\Backend\Form\NodeFactory;

/**
 *
	*
	*	@package	mmimagemap
	*	@license	http://www.gnu.org/licenses/gpl.html	GNU	General	Public	License,	version	3	or	later
	*
	*/
class	BackendController	extends	\TYPO3\CMS\Extbase\Mvc\Controller\ActionController	{
	
	/**
		*
		*	@var \MikelMade\Mmimagemap\Domain\Repository\MapRepository
		*	@inject
		*/
	protected $mapRepository;
	
	/**
		*
		*	@var	\MikelMade\Mmimagemap\Domain\Repository\AreaRepository
		*	@inject
		*/
	protected $areaRepository;
	
	/**
		*	pointRepository
		*
		*	@var	\MikelMade\Mmimagemap\Domain\Repository\PointRepository
		*	@inject
		*/
	protected	$pointRepository;
	
	/**
		*	bcolorsRepository
		*
		*	@var	\MikelMade\Mmimagemap\Domain\Repository\BcolorsRepository
		*	@inject
		*/
	protected	$bcolorsRepository;
	
	
	/**
		*	contentpopupRepository
		*
		*	@var	\MikelMade\Mmimagemap\Domain\Repository\ContentpopupRepository
		*	@inject
		*/
	protected $contentpopupRepository;

	public function initializeSettings() {
		$this->pManager = $this->objectManager->get('TYPO3\\CMS\\Extbase\\Persistence\\Generic\\PersistenceManager');
		/*
		foreach ($this->settings['flexform'] as $key => $value) {
				if (isset($this->settings[$key]) && $value != '') {
					$this->settings[$key] = $value;
				}
 			}
			*/
 		}
		
	public function initializeAction() {
		$this->initializeSettings();
	}


	/**
		*	action	list
		*
		*	@return	 void
	*/
	public function listAction() {
		if(!isset($_GET['id'])){
			$this->view->assign('nodir',1);
		}
		else{
			$path = '';
			if(strlen($_GET['id']) > 0){
				$patharr = explode(':/',$_GET['id']);
				$path = $patharr[1];
			}
			if(isset($_GET['tx__file_mmimagemapmod1']['path'])){ $path = $_GET['tx__file_mmimagemapmod1']['path']; }
			
			$this->view->assign('dir',1);
			$this->view->assign('pathid',$path);
			$abspath = PATH_site.'fileadmin/'.$path;
			$files = array();
			$dh = opendir($abspath);
			while (($file = readdir($dh)) !== false) {
				if($file != '.' && $file != '..'){
					if ( strpos(strtolower($file), '.png') !== strlen($file) - 4
					&& strpos(strtolower($file), '.jpg') !== strlen($file) - 4
					&& strpos(strtolower($file), '.jpeg') !== strlen($file) - 5
					&& strpos(strtolower($file), '.gif') !== strlen($file) - 4 ) { continue; }
					if ( strpos($file, 't_') === 0 ) { $tmp = substr($file, 2); }
					else { $tmp = $file; }
					if ( in_array( $tmp, $files ) ) { continue; }
					$files[] = $file;
				}
			}
			$this->view->assign('files',$files);
			
			$maps = $this->mapRepository->GetMapsFromFolder($path);
			$this->view->assign('mappics',$maps);
			$this->view->assign('maps',$maps);
		}
		
		if(isset($_GET['tx__file_mmimagemapmod1']['nofile'])){
			$this->view->assign('mapname',$_GET['tx__file_mmimagemapmod1']['mapname']);
		}
	}
	
	/**
		*	action	addmap
		*
		*	@return	 void
	*/
	public function addmapAction() {
		/* [MAX_FILE_SIZE] => 1000000 */
		
		if(isset($_POST)){
			$file = $_FILES['usr_file'];
			$image = $file['name'];
			$name = $_POST['name'];
			if(strlen($name) == 0){ $name = $this->mapRepository->GenerateMapName(); }
			
			if(strlen($image) == 0){
				if(strlen($_POST['use_pic']) == 0 && strlen($_POST['use_map']) == 0	&& strlen($_POST['use_file']) == 0){
					$this->redirect('list', 'BackendController', '', array('nofile'=>'nofile','mapname'=>$_POST['name']));
				}
				
				if(strlen($_POST['use_pic']) != 0){
					$image = $this->mapRepository->GetMapData($_POST['use_pic'],'imgfile');
				}
				
				if(strlen($_POST['use_file']) != 0){ $image = $_POST['use_file']; }
			}
			else{
				$image = $this->mapRepository->CheckForDoubleImages($_FILES['usr_file']['name'],$_FILES['usr_file']['name'],$_POST['path']);
				move_uploaded_file($_FILES['usr_file']['tmp_name'],PATH_site.'fileadmin/'.$_POST['path'].$image);
			}
			
			$map = new \MikelMade\Mmimagemap\Domain\Model\Map();
			$map->setName($name);
			$map->setFolder($_POST['path']);
			$map->setImgfile($image);
			
			$this->mapRepository->add($map);
			$this->pManager->persistAll();

			if(strlen($_POST['use_map']) != 0){
				$mapid = $map->getUid();
				
				$mapdata = $this->mapRepository->GetMapData($_POST['use_map']);
				$bcolors = $this->bcolorsRepository->GetBcolors($_POST['use_map']);	
				$areas = $this->areaRepository->GetAreas($_POST['use_map']);
				
				foreach($bcolors as $bcolor){
					$newbcolor = new \MikelMade\Mmimagemap\Domain\Model\Bcolor();
					$newbcolor->setMapid($mapid);
					$newbcolor->setColorname($bcolor['colorname']);
					$newbcolor->setColor($bcolor['color']);
					$this->bcolorsRepository->add($newbcolor);
					$this->pManager->persistAll();
				}
				
				foreach($areas as $area){
					$newarea = new \MikelMade\Mmimagemap\Domain\Model\Area();
					$newarea->setMapid($mapid);
					$newarea->setMaptype($area['maptype']);
					$newarea->setLink($area['link']);
					$newarea->setDescription($area['description']);
					$newarea->setColor($area['color']);
					$newarea->setParam($area['param']);
					$newarea->setFebordercolor($area['febordercolor']);
					$newarea->setFevisible($area['fevisible']);
					$newarea->setFeborderthickness($area['feborderthickness']);
					$newarea->setFealtfile($area['fealtfile']);
					
					$this->areaRepository->add($newarea);
					$this->pManager->persistAll();
					$newareaid = $newarea->getUid();
					
					$points = $this->pointRepository->GetPoints($area['uid']);	
					foreach($points as $point){
						$newpoint = new \MikelMade\Mmimagemap\Domain\Model\Point();
						$newpoint->setAreaid($newareaid);
						$newpoint->setNum($point->getNum());
						$newpoint->setX($point->getX());
						$newpoint->setY($point->getY());
						$this->pointRepository->add($newpoint);
						$this->pManager->persistAll();
					}
					
					$contentpopups = $this->contentpopupRepository->GetContentpopups($area['uid']);
					foreach($contentpopups as $contentpopup){
						$newcp = new \MikelMade\Mmimagemap\Domain\Model\Contentpopup();
						$newcp->setAreaid($newareaid);
						$newcp->setContentid($contentpopup['contentid']);
						$newcp->setPopupwidth($contentpopup['popupwidth']);
						$newcp->setPopupheight($contentpopup['popupheight']);
						$newcp->setPopupx($contentpopup['popupx']);
						$newcp->setPopupy($contentpopup['popupy']);
						$newcp->setPopupbordercolor($contentpopup['popupbordercolor']);
						$newcp->setPopupbackgroundcolor($contentpopup['popupbackgroundcolor']);
						$newcp->setPopupborderwidth($contentpopup['popupborderwidth']);
						$newcp->setActive($contentpopup['active']);
						$this->contentpopupRepository->add($newcp);
						$this->pManager->persistAll();
					}
				}
			}
		}
		$this->mapRepository->MakeFePics($mapid,$this->areaRepository);
		$this->redirect('list', 'BackendController', '', array());
	}
	
	/**
		*	action	listedit
		*
		*	@return	 void
	*/
	public function listeditAction(){
		$mapid = (int)$_POST['map_id'];
		$map = $this->mapRepository->findByUid($mapid);
		$previmage = $map->getImgfile();
		$prevaltimage = $map->getAltfile();
		
		
		if($_POST['action'] == 'chg_name'){
			$file = $_FILES['usr_file'];
			$image = $file['name'];
			$name = $_POST['name'];
			if(strlen($name) == 0){ $name = $this->mapRepository->GenerateMapName(); }
			
			if(strlen($image) > 0){
				$image = $this->mapRepository->CheckForDoubleImages($_FILES['usr_file']['name'],$_FILES['usr_file']['name'],$_POST['path']);
				move_uploaded_file($_FILES['usr_file']['tmp_name'],PATH_site.'fileadmin/'.$_POST['path'].$image);
				$map->setImgfile($image);
				
				if(isset($_POST['del_unused'])){
					$unused = $this->mapRepository->CheckForUnusedPic($_POST['path'],$previmage,$mapid);
					if($unused == true){
						\MikelMade\Mmimagemap\Utility\Tx_Mmimagemap_Utility_Div::RemoveThumb($_POST['path'],$previmage);
						unlink(PATH_site.'fileadmin/'.$_POST['path'].$previmage);
					}
				}
			}
			$map->setName($name);
			$this->mapRepository->update($map);
			$this->pManager->persistAll();
			$this->mapRepository->MakeFePics($mapid,$this->areaRepository);
		}
		
		if($_POST['action'] == 'del_map'){
			if(isset($_POST['del_unused'])){
				$unused = $this->mapRepository->CheckForUnusedPic($_POST['path'],$previmage,$mapid);
				if($unused == true){
					\MikelMade\Mmimagemap\Utility\Tx_Mmimagemap_Utility_Div::RemoveThumb($_POST['path'],$previmage);
					unlink(PATH_site.'fileadmin/'.$_POST['path'].$previmage);
				}
				if(strlen($prevaltimage) > 0 && is_file(PATH_site.'uploads/tx_mmimagemap/'.$prevaltimage)){ unlink(PATH_site.'uploads/tx_mmimagemap/'.$prevaltimage); }
			}
			
			$areas = $this->areaRepository->GetAreas($mapid);
			foreach($areas as $area){
				$points = $this->pointrepository->getPoints($area['uid']);
				foreach($points as $point){
					$delpoint = $this->pointRepository->findByUid($point['uid']);
					$this->pointRepository->remove($delpoint);
					$this->pManager->persistAll();
				}
				if(strlen($area['fealtfile']) > 0 && is_file(PATH_site.'uploads/tx_mmimagemap/'.$area['fealtfile'])){ unlink(PATH_site.'uploads/tx_mmimagemap/'.$area['fealtfile']); }
				
				$delarea = $this->areaRepository->findByUid($area['uid']);
				$this->areaRepository->remove($delarea);
				$this->pManager->persistAll();
			}
			$contentpopups = $this->contentpopupRepository->GetContentpopups($mapid);
			foreach($contentpopups as $contentpopup){
				$delcontentpopup = $this->areaRepository->findByUid($contentpopup['uid']);
				$this->contentpopupRepository->remove($delcontentpopup);
				$this->pManager->persistAll();
			}
			$delmap = $this->mapRepository->findByUid($mapid);
			$this->mapRepository->remove($delmap);
			$this->pManager->persistAll();
		}
		
		$this->redirect('list', 'BackendController', '', array('path'=>$_POST['path']));
	}
	
	
	/**
		*	action	edit
		*
		*	@return	 void
	*/
	public function editAction(){
		
		$belang = $GLOBALS['BE_USER']->uc['lang'];
		if(strlen($belang) == 0){ $belang = 'en'; }
		$extconf = \MikelMade\Mmimagemap\Utility\Tx_Mmimagemap_Utility_Div::GetExtConf();
		$becolors = array();
		$link = '#';
		
		foreach($extconf['colors'] as $color){
			$becolors[] = array(
				'color' => str_replace('#','',$color['color']),
				'colorname' => $color[$belang],
				'fixed' => 1,
				'id' => 0
			);
		}
		if ( ! isset($_SESSION['mmim_blink']) ) { $_SESSION['mmim_blink'] = 1; }
		if(!isset($_SESSION['mmim_close'])) { $_SESSION['mmim_close'] = 0;}
		if(!isset($_SESSION['mmim_spoint'])) { $_SESSION['mmim_spoint'] = 0;}
		if(!isset($_SESSION['mmim_epoint'])) { $_SESSION['mmim_epoint'] = 0;}
		
		$params = $_GET['tx_mmimagemap_file_mmimagemapmod1'];
		$mapdata = $this->mapRepository->GetMapdata($params['mapid']);
		$picdata = \MikelMade\Mmimagemap\Utility\Tx_Mmimagemap_Utility_Div::GetPicSize($mapdata['imgfile'],$mapdata['folder']);
		$areatypelabels = $this->areaRepository->GetAreaTypeLabels();
		
		$areaid = 0;
		$area = array();
		
		$path = '';
		if(strlen($_GET['id']) > 0){
			$patharr = explode(':/',$_GET['id']);
			$path = $patharr[1];
		}
		
		if(isset($_POST['action']) && $_POST['action'] == 'addarea'){
			if(strlen($_POST['descript']) == 0){ $_POST['descript'] = $this->areaRepository->GenerateAreaName(); }
			if(!isset($_POST['blink'])){ $_SESSION['mmim_blink'] = 0; }
			if(!isset($_POST['close'])) { $_SESSION['mmim_close'] = 0;}
			else { $_SESSION['mmim_close'] = (int)$_POST['close'];}
			if(!isset($_POST['spoint'])) { $_SESSION['mmim_spoint'] = 0;}
			else { $_SESSION['mmim_spoint'] = (int)$_POST['spoint'];}
			if(!isset($_POST['epoint'])) { $_SESSION['mmim_epoint'] = 0;}
			else { $_SESSION['mmim_epoint'] = (int)$_POST['epoint'];}
			
			$link = (strlen($_POST['data']['mmimagemap']['editform']['link']) == 0) ? '#' : $_POST['data']['mmimagemap']['editform']['link'];
			
			$newarea = new \MikelMade\Mmimagemap\Domain\Model\Area();
			$newarea->setMapid($_POST['mapid']);
			$newarea->setDescription($_POST['descriptnew']);
			$newarea->setAreatype($_POST['newtype']);
			$newarea->setArealink($link);
			$newarea->setColor(str_replace('#','',$becolors[0]['color']));
			$this->areaRepository->add($newarea);
			$this->pManager->persistAll();
			$areaid = $newarea->getUid;
			$this->mapRepository->MakeFePics($_POST['mapid'],$this->areaRepository);
		}
		
		if(isset($_POST['action']) && $_POST['action'] == 'addcolor'){
			if(strlen($_POST['be_col']) > 0){
				if(preg_match('/^[a-f0-9]{6}$/i', $_POST['be_col'])){
					$_POST['be_col'] = '#'.$_POST['be_col'];
					if(strlen($_POST['be_colname']) == 0){ $_POST['be_colname'] = $_POST['be_col']; }
					
					$newcolor = new \MikelMade\Mmimagemap\Domain\Model\Bcolors;
					$newcolor->setMapid(($_POST['mapid']));
					$newcolor->setColor($_POST['be_col']);
					$newcolor->setColorname($_POST['be_colname']);
					$this->bcolorsRepository->add($newcolor);
					$this->pManager->persistAll();
				}
			}
		}
		
		if($_POST['action'] == 'delcolor'){
			$delcolor = $this->bcolorsRepository->findByUid((int)$_POST['actiondata']);
			if(count($delcolor) > 0){
				
				$color = str_replace('#',$_POST['color']);
				
				$careas = $this->areaRepository->findByColor($color);
				foreach($careas as $area){
					$area->setColor(str_replace('#','',$becolors[0]['color']));
					$this->areaRepository->update($area);
				}
				$this->bcolorsRepository->remove($delcolor);
				$this->pManager->persistAll();
			}
		}
		
		if(isset($_POST['action']) && $_POST['action'] == 'savearea'){
			if(!isset($_POST['blink'])){ $_SESSION['mmim_blink'] = 0; }
			$savearea = $this->areaRepository->findByUid((int)$_POST['area_id']);
			$mapid = $savearea->getMapid();
			$savearea->setColor(str_replace('#','',$_POST['color']));
			
			$link = (strlen($_POST['data']['mmimagemap']['editform']['editlink']) == 0) ? '#' : $_POST['data']['mmimagemap']['editform']['editlink'];
			
			$savearea->setArealink($link);
			$savearea->setParam($_POST['param']);
			$savearea->setFevisible((int)$_POST['fe_visible']);
			$savearea->setFebordercolor(str_replace('#','',$_POST['fe_bcol']));
			$savearea->setFeborderthickness((int)$_POST['fe_borderthickness']);
			$savearea->setDescription($_POST['descript']);
			$this->areaRepository->update($savearea);
			
			// save contentbox here
			if(strlen($_POST['cbid']) > 0){
				$new = false;
				
				$cboxid = $this->contentpopupRepository->GetCboxId($_POST['area_id']); //findByAreaid((int)$_POST['area_id']);
				
				if(count($cboxid) == 0){
					$new = true;
					$cbox = new \MikelMade\Mmimagemap\Domain\Model\Contentpopup();
					$cbox->setAreaid((int)$_POST['area_id']);
				}
				else{
					$cbox = $this->contentpopupRepository->findByUid($cboxid[0]['uid']);
				}
				$active = (isset($_POST['cb_active'])) ? 1 : 0;
				
				$cbox->setPopupwidth((int)$_POST['cb_width']);
				$cbox->setPopupheight((int)$_POST['cb_height']);
				$cbox->setPopupx((int)$_POST['cb_x']);
				$cbox->setPopupy((int)$_POST['cb_y']);
				$cbox->setPopupbordercolor(str_replace('#','',$_POST['cb_bcol']));
				$cbox->setPopupborderwidth(str_replace('#','',$_POST['cb_borderthickness']));
				$cbox->setContentid((int)$_POST['cbid']);
				$cbox->setActive($active);					
				
				if($new == true){ $this->contentpopupRepository->add($cbox); }
				else{ $this->contentpopupRepository->update($cbox); }
			}
			
			$points = $this->pointRepository->findByAreaid((int)$_POST['area_id']);
			switch($_POST['type']){
				case 0: //rectangle
					if(count($points) > 0){
						foreach($points as $point){
							if($point->getNum() == 1){
								$point->setX((int)$_POST['xpos']);
								$point->setY((int)$_POST['ypos']);
							}
							if($point->getNum() == 2){
								$point->setX((int)$_POST['xsize']);
								$point->setY((int)$_POST['ysize']);
							}
							$this->pointRepository->update($point);
						}
					}
					else{
						$pos = new \MikelMade\Mmimagemap\Domain\Model\Point();
						$pos->setAreaid((int)$_POST['area_id']);
						$pos->setX((int)$_POST['xpos']);	
						$pos->setY((int)$_POST['ypos']);
						$pos->setNum(1);
						$this->pointRepository->add($pos);
						
						$size = new \MikelMade\Mmimagemap\Domain\Model\Point();
						$size->setAreaid((int)$_POST['area_id']);
						$size->setX((int)$_POST['xsize']);	
						$size->setY((int)$_POST['ysize']);
						$size->setNum(2);
						$this->pointRepository->add($size);
					}
					
				break;
				
				case 1: //circle
					if(count($points) > 0){
						foreach($points as $point){
							if($point->getNum() == 1){
								$point->setX((int)$_POST['radius']);
							}
							if($point->getNum() == 2){
								$point->setX((int)$_POST['xpos']);
								$point->setY((int)$_POST['ypos']);
							}
							$this->pointRepository->update($point);
						}
					}
					else{
						$pos = new \MikelMade\Mmimagemap\Domain\Model\Point();
						$pos->setAreaid((int)$_POST['area_id']);
						$pos->setX((int)$_POST['radius']);	
						$pos->setNum(1);
						$this->pointRepository->add($pos);
						
						$size = new \MikelMade\Mmimagemap\Domain\Model\Point();
						$size->setAreaid((int)$_POST['area_id']);
						$size->setX((int)$_POST['xpos']);	
						$size->setY((int)$_POST['ypos']);
						$size->setNum(2);
						$this->pointRepository->add($size);
					}
				
				break;
				
				case 2: //polygon
					// first, remove all previous points
					if(count($points) > 0){
						foreach($points as $delpoint){ $this->pointRepository->remove($delpoint); }
						$this->pManager->persistAll();
					}
					for($i=0;$i<$_POST['polynum'];$i++){
						$newpoint = new \MikelMade\Mmimagemap\Domain\Model\Point();
						$newpoint->setAreaid((int)$_POST['area_id']);
						$newpoint->setX((int)$_POST['xpos'.($i+1)]);
						$newpoint->setY((int)$_POST['ypos'.($i+1)]);
						$newpoint->setNum(($i+1));
						$this->pointRepository->add($newpoint);
					}
					
				break;
				
				case 3:
				
				break;
			}
			$this->pManager->persistAll();
			
			$areaid = (int)$_POST['area_id'];
			$this->mapRepository->MakeFePics($mapid,$this->areaRepository);
		}
		
		
		if(isset($_POST['action']) && $_POST['action'] == 'delarea'){
			$areaid = ((int)$_POST['actiondata'] == (int)$_POST['area_id']) ? 0 : (int)$_POST['area_id'];
			
			$delarea = $this->areaRepository->findByUid((int)$_POST['actiondata']);
			$altfile = $delarea->getFealtfile();
			if(strlen($altfile) > 0){
				if(file_exists(PATH_site.'uploads/tx_mmimagemap/'.$altfile)){ unlink(PATH_site.'uploads/tx_mmimagemap/'.$altfile); }
			}
			$this->areaRepository->remove($delarea);
			$points = $this->pointRepository->findByAreaid((int)$_POST['actiondata']);
			foreach($points as $delpoint){ $this->pointRepository->remove($delpoint); }
			$this->pManager->persistAll();
			
			$this->mapRepository->MakeFePics((int)$_POST['mapid'],$this->areaRepository);
		}
		
		if(isset($_POST['action']) && $_POST['action'] == 'moveareas'){
			$areas = explode(',',$_POST['actiondata']);
			$xmov = (int)$_POST['xmov'];
			$ymov = (int)$_POST['ymov'];
			if(count($areas) > 0){
				foreach($areas as $area){
					$areatype = $this->areaRepository->GetAreaData($area,'areatype');
					$points = $this->pointRepository->findByAreaid((int)$area);
					
					switch((int)$areatype){
						
						case 0: // rectangle
							foreach($points as $point){
								if($point->getNum() == 1){
									$point->setX((int)$point->getX()+$xmov);
									$point->setY((int)$point->getY()+$ymov);
									$this->pointRepository->update($point);
									break;
								}
							}
						break;
						
						case 1: // circle
						 
							foreach($points as $point){
								if($point->getNum() == 2){
									$point->setX((int)$point->getX()+$xmov);
									$point->setY((int)$point->getY()+$ymov);
									$this->pointRepository->update($point);
									break;
								}
							}
						break;
						
						case 2: // polygon
							foreach($points as $point){
								$point->setX((int)$point->getX()+$xmov);
								$point->setY((int)$point->getY()+$ymov);
								$this->pointRepository->update($point);
							}
						break;
					}
				}
			}
			$this->pManager->persistAll();
		}
		
		
		// get area list after all inserts and updates are done
		$arealist = $this->areaRepository->GetAreas($params['mapid']);
		
		// get all fresh area data here
		if($areaid == 0 && !empty($arealist)){
			
			if(isset($_GET['tx_mmimagemap_file_mmimagemapmod1']['setarea'])){
				$areaid = $_GET['tx_mmimagemap_file_mmimagemapmod1']['areaid'];
			}
			else{ $areaid = $arealist[0]['uid']; }
		}
		
		$area = $this->areaRepository->GetCompleteArea($areaid);

		if(!empty($area)){
			$link = $area['area']['arealink'];
			$this->view->assign('area',$area);
		}
		
		if($extconf['ext']['add_colors'] == '1'){
			$becolors = $this->mapRepository->GetAdditionalColors($becolors,$params['mapid'],$extconf['ext']['overall_colors']);
			$this->view->assign('ocols',count($becolors));
			$this->view->assign('addcolors',1);
		}
		else{
			$this->view->assign('ocols',0);
			$this->view->assign('addcolors',0);			
		}
		
		
		
		$this->view->assign('close',$_SESSION['mmim_close']);
		$this->view->assign('spoint',$_SESSION['mmim_spoint']);
		$this->view->assign('epoint',$_SESSION['mmim_epoint']);
		
		$this->view->assign('elementbrowser',\TYPO3\CMS\Backend\Utility\BackendUtility::getModuleUrl('wizard_element_browser'));
		$this->view->assign('token',$_GET['moduleToken']);
		$this->view->assign('blink',$_SESSION['mmim_blink']);
		$this->view->assign('becolors',$becolors);
		$this->view->assign('arealist',$arealist);
		$this->view->assign('path',$path);
		$this->view->assign('mapid',$params['mapid']);
		$this->view->assign('picdata',$picdata);
		$this->view->assign('urlfield',$this->createlinkbrowser('link','#'));
		$this->view->assign('editurlfield',$this->createlinkbrowser('editlink',$link));
	}
	
	/**
		*	create a link wizard
		*
		* @param string $form
		* @param string $field
		*
		*	@return	 void
	*/
	private function createlinkbrowser($elm,$value=''){
		$options = [
				'renderType' => 'inputLink',
				'tableName' => '',
				'fieldName' => '',
				'databaseRow' => [
					'uid' => 0,
					'pid' => 0
				],
				'parameterArray' => [
					'fieldConf' => [
						'label' => '',
						'config' => [
							'eval' => 'trim',
							'size' => 2024,
						],
					],
					'itemFormElValue' => $value,
					'itemFormElName' => 'data[mmimagemap][editform]['.$elm.']',
					'itemFormElID' => 'data[mmimagemap][editform]['.$elm.']',
					'field' => '',
					'fieldChangeFunc' => [
						'TBE_EDITOR_fieldChanged' => "TBE_EDITOR.fieldChanged('','editform','','data[mmimagemap][editform][".$elm."]');"
					],
				]
		];
		$nodeFactory = new NodeFactory();
		$linkField = new InputLinkElement($nodeFactory, $options);
		$urlField = $linkField->render();
		return $urlField['html'];
	}
	

}

?>