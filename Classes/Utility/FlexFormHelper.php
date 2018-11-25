<?php

/*
 *	(c) 2018 MikelMade (http://www.mikelmade.de) 
 *	All rights reserved
 */

namespace MikelMade\Mmimagemap\Utility;

use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Database\Query\QueryBuilder;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Class FlexFormHelper
 * @package mmimagemap
 */
class FlexFormHelper {

	/**
		* @param array $fConfig
		* @param \TYPO3\CMS\backend\form\FormEngine $fObj
		*
		* @return void
		*/
	public function getOptions($config) {
		$queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)->getQueryBuilderForTable('tx_mmimagemap_domain_model_map');
		
		$maps = $queryBuilder
		->select('uid','name')
		->from('tx_mmimagemap_domain_model_map')
		->orderBy('name', 'ASC')
		->execute()
		->fetchAll();
	
		$optionList = array();
		foreach($maps as $map){
			$optionList[] = array(0 => $map['name'], 1 => $map['uid']);
		}
    $config['items'] = array_merge($config['items'],$optionList);
    return $config;
	}
} 
























