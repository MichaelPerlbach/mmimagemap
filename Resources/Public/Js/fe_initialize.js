TYPO3.settings.FormEngine = {"formName":"editform"};

define(['jquery', 'TYPO3/CMS/Backend/FormEngine'], function($){
    'use strict';

    $(function(){
        TYPO3.FormEngine.initialize();
    });
});

TYPO3.settings.Popup = [];
TYPO3.settings.Popup.PopupWindow = [];
TYPO3.settings.Popup.PopupWindow.width = 1000;
TYPO3.settings.Popup.PopupWindow.height = 800;