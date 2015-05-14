<?php
/***************************************************************
 *  Copyright notice
 *
 *  (c) 2013 Steffen Ritter, rs websystems (steffen.ritter@typo3.org)
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

use Reelworx\RxSmoothmigration7\Service\Check\Registry;
use Reelworx\RxSmoothmigration7\Domain\Repository\IssueRepository;
use TYPO3\CMS\Extbase\Mvc\Controller\ActionController;
use TYPO3\CMS\Extbase\Mvc\Web\Response;

/**
 * Class AjaxController
 */
class AjaxController extends ActionController {

	/**
	 * @var \Reelworx\RxSmoothmigration7\Domain\Repository\IssueRepository
	 */
	protected $issueRepository;

	/**
	 * @param \Reelworx\RxSmoothmigration7\Domain\Repository\IssueRepository $issueRepository
	 */
	public function injectIssueRepository(IssueRepository $issueRepository) {
		$this->issueRepository = $issueRepository;
	}

	/**
	 * Initializes the controller before invoking an action method.
	 *
	 * @return void
	 */
	protected function initializeAction() {
		parent::initializeAction();
		if ($this->response instanceof Response) {
			$this->response->setHeader('Content-type', 'application/json; charset=utf-8');
		}
	}

	/**
	 * @param string $checkIdentifier
	 *
	 * @return string
	 */
	public function runTestAction($checkIdentifier) {
		$registry = Registry::getInstance();
		$check = $registry->getActiveCheckByIdentifier($checkIdentifier);

		if ($check !== NULL) {
			$processor = $check->getProcessor();
			$processor->execute();

			foreach ($processor->getIssues() as $issue) {
				$this->issueRepository->add($issue);
			}
			return json_encode(array(
				'result' => 'OK',
				'issueCount' => count($processor->getIssues()),
			));
		} else {
			if ($this->response instanceof Response) {
				$this->response->setStatus(404, 'Check not found');
			}
			return json_encode(array('result' => 'ERROR'));
		}
	}


	/**
	 * @param string $checkIdentifier
	 *
	 * @return string
	 */
	public function clearTestResultsAction($checkIdentifier) {
		$deletedIssueCount = $this->issueRepository->deleteAllByInspection($checkIdentifier);

		if ($deletedIssueCount !== -1) {
			return json_encode(array(
				'result' => 'OK',
				'issueCount' => $deletedIssueCount,
			));
		} else {
			if ($this->response instanceof Response) {
				$this->response->setStatus(404, 'Check not found');
			}
			return json_encode(array('result' => 'ERROR'));
		}
	}
}
