<?php
namespace Reelworx\RxSmoothmigration7\Database;

use TYPO3\CMS\Core\Authentication\BackendUserAuthentication;
use TYPO3\CMS\Backend\Utility\BackendUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Labels for database tables
 */
class DatabaseLabels {

	/**
	 * Title for deprecation table
	 *
	 * @param array $parameters
	 * @return void
	 */
	public function deprecationTitle(&$parameters) {
		$record = BackendUtility::getRecord($parameters['table'], $parameters['row']['uid']);
		$parameters['title'] = $this->renderTitle($record['interface'] ?: $record['class'] . ': ' . $record['method']);
	}

	/**
	 * Title for issue table
	 *
	 * @param array $parameters
	 * @return void
	 */
	public function issueTitle(&$parameters) {
		$record = BackendUtility::getRecord($parameters['table'], $parameters['row']['uid']);
		$parameters['title'] = $this->renderTitle($record['extension'] . ': ' . $record['inspection']);
	}

	/**
	 * @param string $title
	 * @return string
	 */
	protected function renderTitle($title) {
		/** @var BackendUserAuthentication $beUser */
		$beUser = $GLOBALS['BE_USER'];
		$titleLength = $beUser->uc['titleLen'] ?: 30;
		return htmlspecialchars(GeneralUtility::fixed_lgd_cs($title, $titleLength));
	}

}
