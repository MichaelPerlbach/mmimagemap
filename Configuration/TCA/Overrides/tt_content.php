<?php

$GLOBALS['TCA']['tt_content']['types']['list']['subtypes_excludelist']['mmimagemap_pi1'] = 'recursive,select_key,pages';

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addPlugin(
   array(
      'LLL:EXT:mmimagemap/Resources/Private/Language/locallang_be.xlf:tx_mmimagemap.wizard_pi1_title',
      'mmimagemap',
      'EXT:mmimagemap/Resources/Public/Icons/module-mmimagemap.svg'
   ),
   'CType',
   'mmimagemap_pi1'
);
