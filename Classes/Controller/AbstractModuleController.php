<?php
/***************************************************************
 *  Copyright notice
 *
 *  (c) 2013 Steffen Ritter, rs websystems <steffen.ritter@typo3.org>
 *  All rights reserved
 *
 *  This script is part of the TYPO3 project. The TYPO3 project is
 *  free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 2 of the License, or
 *  (at your option) any later version.
 *
 *  The GNU General Public License can be found at
 *  http://www.gnu.org/copyleft/gpl.html.
 *  A copy is found in the textfile GPL.txt and important notices to the license
 *  from the author is found in LICENSE.txt distributed with these scripts.
 *
 *
 *  This script is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  This copyright notice MUST APPEAR in all copies of the script!
 ***************************************************************/

namespace Reelworx\RxSmoothmigration7\Controller;

use TYPO3\CMS\Core\FormProtection\FormProtectionFactory;
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Mvc\RequestInterface;
use TYPO3\CMS\Extbase\Mvc\ResponseInterface;
use TYPO3\CMS\Extensionmanager\Controller\ActionController;
use TYPO3\CMS\Lang\LanguageService;

/**
 * Abstract action controller.
 *
 * @author Steffen Ritter
 */
class AbstractModuleController extends ActionController {

	/**
	 * @var string Key of the extension this controller belongs to
	 */
	protected $extensionName = 'RxSmoothmigration7';

	/**
	 * @var string The module security token
	 */
	protected $moduleToken = '';

	/**
	 * @var \TYPO3\CMS\Core\Page\PageRenderer
	 */
	protected $pageRenderer;

	/**
	 * @var \TYPO3\CMS\Backend\Template\DocumentTemplate
	 */
	protected $template;

	/**
	 * Initializes the controller before invoking an action method.
	 *
	 * @return void
	 */
	protected function initializeAction() {
		$extRelPath = ExtensionManagementUtility::extRelPath('rx_smoothmigration7');
		$this->pageRenderer->addCssFile($extRelPath . 'Resources/Public/StyleSheet/module.css');
		$this->pageRenderer->addInlineLanguageLabelFile('EXT:rx_smoothmigration7/Resources/Private/Language/locallang.xml');
		$this->pageRenderer->addJsLibrary('jquery', $extRelPath . 'Resources/Public/JavaScript/jquery-1.10.1.min.js');
		$this->pageRenderer->addJsLibrary('sprintf', $extRelPath . 'Resources/Public/JavaScript/sprintf.min.js');
		$this->pageRenderer->addJsFile($extRelPath . 'Resources/Public/JavaScript/General.js');
		$this->moduleToken = FormProtectionFactory::get()->generateToken('moduleCall', 'tools_RxSmoothmigration7Smoothmigration');
	}

	/**
	 * Processes a general request. The result can be returned by altering the given response.
	 *
	 * @param RequestInterface $request The request object
	 * @param ResponseInterface $response The response, modified by this handler
	 * @return void
	 */
	public function processRequest(RequestInterface $request, ResponseInterface $response) {
		$this->template = GeneralUtility::makeInstance('TYPO3\\CMS\\Backend\\Template\\DocumentTemplate');
		$this->pageRenderer = $this->template->getPageRenderer();

		$GLOBALS['SOBE'] = new \stdClass();
		$GLOBALS['SOBE']->doc = $this->template;

		parent::processRequest($request, $response);

		/** @var LanguageService $lang */
		$lang = $GLOBALS['LANG'];
		$pageHeader = $this->template->startpage(
			$lang->sL('LLL:EXT:rx_smoothmigration7/Resources/Private/Language/locallang.xml:module.title')
		);
		$pageEnd = $this->template->endPage();

		$response->setContent($pageHeader . $response->getContent() . $pageEnd);
	}
}

