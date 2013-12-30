<?php

namespace TYPO3\Q8yRssmanager\Controller;

/***************************************************************
 *  Copyright notice
 *
 *  (c) 2013 
 *  All rights reserved
 *
 *  This script is part of the TYPO3 project. The TYPO3 project is
 *  free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 3 of the License, or
 *  (at your option) any later version.
 *
 *  The GNU General Public License can be found at
 *  http://www.gnu.org/copyleft/gpl.html.
 *
 *  This script is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  This copyright notice MUST APPEAR in all copies of the script!
 ***************************************************************/

/**
 *
 *
 * @package q8y_rssmanager
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 *
 */
 

include_once(PATH_tslib.'typo3conf/ext/q8y_rssmanager/autoloader.php');

 
class RssmanagerController extends \TYPO3\CMS\Extbase\Mvc\Controller\ActionController {

	/**
	 * action list
	 *
	 * @return void
	 */
	public function listAction() {
		//$rssmanagers = $this->rssmanagerRepository->findAll();
		$this->view->assign('rssmanagers', $rssmanagers);
	}

	/**
	 * action show
	 *
	 * @param \TYPO3\Q8yRssmanager\Domain\Model\Rssmanager $rssmanager
	 * @return void
	 */
	public function showAction(\TYPO3\Q8yRssmanager\Domain\Model\Rssmanager $rssmanager) {
		$this->view->assign('rssmanager', $rssmanager);
	}

	/**
	 * action new
	 *
	 * @param \TYPO3\Q8yRssmanager\Domain\Model\Rssmanager $newRssmanager
	 * @dontvalidate $newRssmanager
	 * @return void
	 */
	public function newAction(\TYPO3\Q8yRssmanager\Domain\Model\Rssmanager $newRssmanager = NULL) {
		
		$this->view->assign('newRssmanager', $newRssmanager);
	}

	/**
	 * action create
	 *
	 * @param \TYPO3\Q8yRssmanager\Domain\Model\Rssmanager $newRssmanager
	 * @return void
	 */
	public function createAction(\TYPO3\Q8yRssmanager\Domain\Model\Rssmanager $newRssmanager) {
		$feed_url = $newRssmanager->getFeedurl();
		$feed = new \TYPO3\Q8yRssmanager\Util\autoloader;
		//$feed->set_feed_url($url);
		//$feed->init();
		
		exit;
		$this->rssmanagerRepository->add($newRssmanager);
		$this->flashMessageContainer->add('Your new Rssmanager was created.');
		$this->redirect('list');
	}

	/**
	 * action delete
	 *
	 * @param \TYPO3\Q8yRssmanager\Domain\Model\Rssmanager $rssmanager
	 * @return void
	 */
	public function deleteAction(\TYPO3\Q8yRssmanager\Domain\Model\Rssmanager $rssmanager) {
		$this->rssmanagerRepository->remove($rssmanager);
		$this->flashMessageContainer->add('Your Rssmanager was removed.');
		$this->redirect('list');
	}

}
?>