 <?php
 /**
        * TCA for the database fields.
        *
        * @author Michael Perlbach <info@mikelmade.de>
    */

if (!defined('TYPO3_MODE')) {
    die('Access denied.');
}

$tabledef = [
    'columns' => [
        'uid' => [],
        'pid' => [],
        'mapid' => [],
        'colorname' => [],
        'color' => []
    ]
];
return $tabledef;
