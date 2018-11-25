 <?php
 /**
		* TCA for the database fields.
		*
		* @author Michael Perlbach <info@mikelmade.de>
	*/

if (!defined ('TYPO3_MODE')) 	die ('Access denied.');

$tabledef = [
	'columns' => [
		'uid' => [],
		'pid' => [],
		'mapid' => [],
		'areatype' => [],
		'arealink' => [],
		'description' => [],
		'color' => [],
		'param' => [],
		'febordercolor' => [],
		'fevisible' => [],
		'feborderthickness' => [],
		'fealtfile' => []
	]
];
return $tabledef;