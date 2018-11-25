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

/**
 *
	*
	*	@package	mmimagemap
	*	@license	http://www.gnu.org/licenses/gpl.html	GNU	General	Public	License,	version	3	or	later
	*
	*/
class	FrontendDisplayController	extends	\TYPO3\CMS\Extbase\Mvc\Controller\ActionController	{

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
		if(isset($this->settings['flexform']) && is_array($this->settings['flexform'])){
			foreach ($this->settings['flexform'] as $key => $value) {
				if (isset($this->settings[$key]) && $value != '') {
					$this->settings[$key] = $value;
				}
 			}
 		}
	}
	public function initializeAction() { $this->initializeSettings(); }
	
	/**
		*	action	showajaxreturn
		* 
		*	@return	 void
	*/
	public	function	showajaxreturnAction(){}
	
	/**
		*	action	list
		* 
		*	@return	 void
	*/
	public	function	listAction()	{
		$areashapes = [ 0 => 'rect', 1 => 'circle', 2 => 'poly', 3 => 'def' ];

		$mapdata = $this->mapRepository->GetMapData($this->settings['map']);
		$mapdata['overlay'] = (strlen($mapdata['altfile']) != 0) ? 'uploads/tx_mmimagemap/'.$mapdata['altfile'] : '';
		
		$isize = getimagesize(PATH_site.'fileadmin/'.$mapdata['folder'].$mapdata['imgfile']);
		$mapdata['width'] = $isize[0];
		$mapdata['height'] = $isize[1];
		
		$areas = array();
		$allareas = $this->areaRepository->findByMapid((int)$this->settings['map']);
		$cboxes = array();
		
		foreach($allareas as $area){
			$areadata = $this->areaRepository->GetCompleteArea($area->getUid());
		
			$thisparams = '';
			$thisarea = array();
			$thisarea['uid'] = $area->getUid();
			$thisarea['shape'] = $areashapes[$area->getAreatype()];
			$thisarea['coords'] = '';
			switch($areadata['area']['areatype']){
				case 0:
					$thisarea['coords'] .= $areadata['points']['x'].','.$areadata['points']['y'].','.((int)$areadata['points']['x']+(int)$areadata['points']['w']).','.((int)$areadata['points']['y']+(int)$areadata['points']['h']);
				break;
				
				case 1: print 'circle';
					$thisarea['coords'] .= $areadata['points']['x'].','.$areadata['points']['y'].','.$areadata['points']['r'];
				break;
				
				case 2:
					foreach($areadata['points']['points'] as $point){
						$comma = (strlen($thisarea['coords']) == 0) ? '' :',';
						$thisarea['coords'] .= $comma.$point['x'].','.$point['y'];
					}
				break;
				
				case 3:
					$thisarea['coords'] = '0,0,'.$mapdata['width'].','.$mapdata['height'];
				break;
			}
			
			$thisarea['target'] = '';
			if(strlen($areadata['area']['arealink']) == 0){ $thisarea['link'] = '#'; }
			else{
				if(preg_match("/ /",$areadata['area']['arealink'])){
					$linkarr = explode(' ',$areadata['area']['arealink']);
					$thisarea['target'] = ' target="'.$linkarr[1].'"';
					$thisarea['link'] = $linkarr[0];
				}
				else{ $thisarea['link'] = $areadata['area']['arealink']; }
			}

			// overlay options are needed if an overlay image is found
			if(strlen($area->getFealtfile()) > 0){
				$thisparams['mouseover'] = 'Javascript:mmimagemap_changearea(\'tx_mmimagemap_img_'.$this->settings['map'].'\',\'tx_mmimagemap_altfefile_'.$area->getUid().'\');';
				$thisparams['mouseout'] = 'Javascript:mmimagemap_resetarea(\'tx_mmimagemap_img_'.$this->settings['map'].'\',\'typo3conf/ext/mmimagemap/Resources/Public/Images/canvas.png\');';
				$thisarea['fealtfile'] = $area->getFealtfile();
			}
			
			if(!empty($areadata['contentpopup']) && (int)$areadata['contentpopup']['active'] != 0 ){
				if(strlen($areadata['contentpopup']['popupbordercolor']) != 0 && strlen($areadata['contentpopup']['popupborderwidth']) != 0){
					$areadata['contentpopup']['border'] = 'border: '.(int)$areadata['contentpopup']['popupborderwidth'].'px solid #'.$areadata['contentpopup']['popupbordercolor'].';';
				}
				else{ $areadata['contentpopup']['border'] = ''; }
				
				if(strlen($areadata['contentpopup']['popupbackgroundcolor']) != 0){
					$areadata['contentpopup']['background'] = 'background-color:#'.$areadata['contentpopup']['popupbackgroundcolor'].';';
				}
				else{ $areadata['contentpopup']['background'] = ''; }
				
				if(strlen($areadata['contentpopup']['popupbordercolor']) != 0){
					$areadata['contentpopup']['bcolor'] = '#'.$areadata['contentpopup']['popupbordercolor'];
				}
				
				$cboxes[] = $areadata['contentpopup'];
				$thisparams['mouseover'] = $thisparams['mouseover'].'Javascript:mmimagemap_showCBox(\'txmmimagemap_cbox_'.$areadata['contentpopup']['uid'].'\');';
				$thisparams['mouseout'] = $thisparams['mouseout'].'Javascript:mmimagemap_hideCBox(\'txmmimagemap_cbox_'.$areadata['contentpopup']['uid'].'\');';
			}
			
			$thisarea['params'] = '';
			
			if(strlen($areadata['area']['param']) != 0){
				$events = ['onmouseover','onmousedown','onclick','onmouseout','onmouseup'];
				$params = $areadata['area']['param'];
				$parray = explode(' ',$params);
				foreach($parray as $item){
					$iarray = explode('=',$item);
					if(in_array(strtolower($iarray[0]),$events)){
						$item = \MikelMade\Mmimagemap\Utility\Tx_Mmimagemap_Utility_Div::CorrectParams($iarray[0], $item);
						if(strtolower($iarray[0]) == 'onmouseover'){ $thisparams['mouseover'] .= 'Javascript:'.$item.';'; }
						else if(strtolower($iarray[0]) == 'onmouseout'){ $thisparams['mouseout'] .= 'Javascript:'.$item.';'; }
						else{ $thisarea['params'] .= strtolower($iarray[0]).'="Javascript:'.$item.';"'; }
					}
				}
				
				
			}
			
			if(strlen($thisparams['mouseover']) > 0){ $thisarea['params'] .= 'onmouseover="'.$thisparams['mouseover'].'" '; }
			if(strlen($thisparams['mouseover']) > 0){ $thisarea['params'] .= ' onmouseout="'.$thisparams['mouseout'].'"'; }
			
			$areas[] = $thisarea;
		}
		
		$this->view->assign('cboxes',$cboxes);
		$this->view->assign('map',$mapdata);
		$this->view->assign('areas',$areas);
	}
}










