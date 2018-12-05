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
        'name' => [],
        'imgfile' => [],
        'altfile' => [],
        'folder' => []
    ]
];
return $tabledef;
