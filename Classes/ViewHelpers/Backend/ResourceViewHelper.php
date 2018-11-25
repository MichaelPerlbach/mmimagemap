<?php
namespace MikelMade\Mmimagemap\ViewHelpers\Backend;

use TYPO3\CMS\Core\Page\PageRenderer;

 /**
		* Loads css and javascript files in the backend.
		*
		* @author Michael Perlbach <info@mikelmade.de>
	*/
class ResourceViewHelper extends	\TYPO3\CMS\Fluid\ViewHelpers\Be\AbstractBackendViewHelper {
		public function render() {
			/*
				$doc = $this->getDocInstance();
				
				$pageRender = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(\TYPO3\CMS\Core\Page\PageRenderer::class);
				//$pageRender->addJsFooterFile($jsFile, 'text/javascript', true, false, '', true);
				
				$extRelPath = \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extRelPath("mmimagemap");					
				$pageRender->addCssFile($extRelPath . "Resources/Public/Css/mm_icons.css");
				$pageRender->addCssFile($extRelPath . "Resources/Public/Css/be.css");
				$pageRender->addJsFile($extRelPath . "Resources/Public/Js/mmimagemap.js");
				$pageRender->addJsFile($extRelPath . "Resources/Public/Js/wz_jsgraphics.js");
				$pageRender->addJsFile($extRelPath . "Resources/Public/Js/draw_objects.js");
				$pageRender->addJsFile($extRelPath . "Resources/Public/Js/poly.js");
				$pageRender->addJsFile($extRelPath . "Resources/Public/Js/jscolor.js");
				$pageRender->addJsFile($extRelPath . "Resources/Public/Js/functions.js");
				
				$output = $this->renderChildren();
				$output = $doc->startPage("MM Imagemap") . $output;
				//$output .= $doc->endPage();

				print $output;
				//return $output;
				*/
		}
}



?>