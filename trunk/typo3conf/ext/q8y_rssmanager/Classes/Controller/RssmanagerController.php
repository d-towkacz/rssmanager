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
 

include_once(PATH_site.'typo3conf/ext/q8y_rssmanager/Classes/Util/autoloader.php');

 
class RssmanagerController extends \TYPO3\CMS\Extbase\Mvc\Controller\ActionController {

	/**
	 * action list
	 *
	 * @return void
	 */
	public function listAction() {
		//$rssmanagers = $this->rssmanagerRepository->findAll();
		
		//$feed = new \SimplePie;
		//$feed->set_feed_url($feed_url);
		//$feed->init();
		$feuser_uid = $GLOBALS['TSFE']->fe_user->user['uid'];
		$repoFeed = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance("TYPO3\Q8yRssmanager\Domain\Repository\RssmanagerRepository");
		$repoUser = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance("TYPO3\Q8yRssmanager\Domain\Repository\UserRepository");
		$userData = $repoUser->findUser($feuser_uid);
		$userData = $userData[0];	
		//print_r($userData);
		//exit;
		$feed_uids = explode(',',$userData['feed_uids']);
		
		$out_feed_list = $repoFeed->findListFeeds($feed_uids);
		
		$out_records_list = array();
		$num_chanel = 0;
		$num_record = 0;
		foreach ($out_feed_list as $item) {
		    $feed = new \SimplePie;
		    $feed->set_feed_url($item['feedurl']);
		    $feed->set_cache_location(PATH_site.'typo3temp');
		    $feed->enable_cache();
		    $feed->strip_htmltags(array('blink', 'marquee','img'));
		    $feed->init();
		    $feed->handle_content_type();
		    $out_records_list[$num_chanel]['title'] = $feed->get_title();
		    $out_records_list[$num_chanel]['uid'] = $item['uid'];
		    $out_records_list[$num_chanel]['records'] = array();
		    foreach ($feed->get_items() as $item)
		    {
				$out_records_list[$num_chanel]['records'][$num_record]['title'] = $item->get_title();
				$out_records_list[$num_chanel]['records'][$num_record]['link'] = $item->get_link();
				$out_records_list[$num_chanel]['records'][$num_record]['description'] = htmlspecialchars_decode(strip_tags($item->get_description()));
				$out_records_list[$num_chanel]['records'][$num_record]['date'] = $item->get_date();
				$num_record++;
		    }
		    $num_chanel++;
			//print_r($feed);
		}
		
		//exit;
		
		$this->view->assign('rssrecords', $out_records_list);
		$this->view->assign('rssmanagers', $out_feed_list);
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
		$feed = new \SimplePie;
		$feed->set_feed_url($feed_url);
		$feed->init();
		
		if ($feed->error == "")
		{
			$feed_link = $feed->get_link();
			$feed_title = $feed->get_title();
			//print_r($feed_link);
			$repoFeed = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance("TYPO3\Q8yRssmanager\Domain\Repository\RssmanagerRepository");
			$repoUser = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance("TYPO3\Q8yRssmanager\Domain\Repository\UserRepository");
			$feed_exist = $repoFeed->findFeed($feed_link);
			$feuser_uid = $GLOBALS['TSFE']->fe_user->user['uid'];
			if (count($feed_exist) > 0)
			{
				$feed_uid = $feed_exist[0]['uid'];
				$userData = $repoUser->findUser($feuser_uid);
				$userData = $userData[0];
				$feed_uids = explode(',',$userData['feed_uids']);
				$feed_uids[] = $feed_uid;
				$feed_uids = array_unique($feed_uids);
				$feed_uids_str = implode(',',$feed_uids);
				//print_r($userData['feed_uids']);
				//exit;
				$repoUser->updateUser($feuser_uid,$feed_uids_str);
			}
			else
			{
				//$newFeed = new \TYPO3\Q8yRssmanager\Domain\Model\Rssmanager;
				//$repoSave = $repoFeed->get('TYPO3\Q8yRssmanager\Domain\Repository\RssmanagerRepository');
				$feed_uid = $repoFeed->createFeed($feed_link, $feed_title, 0);
				$userData = $repoUser->findUser($feuser_uid);
				$userData = $userData[0];
				$feed_uids = explode(',',$userData['feed_uids']);
				$feed_uids[] = $feed_uid;
				$feed_uids = array_unique($feed_uids);
				$feed_uids_str = implode(',',$feed_uids);
				$repoUser->updateUser($feuser_uid,$feed_uids_str);
				//print_r($repoFeed->add($newFeed));
				//exit;
				
			}
			
			$this->flashMessageContainer->add('RSS feed is added.');
			$this->redirect('list');
			
		} else {
			$this->flashMessageContainer->add('This Feed could not be read. Please check your URL.');
			$this->redirect('list');
		}
	}

	/**
	 * action delete
	 *
	 * @param \TYPO3\Q8yRssmanager\Domain\Model\Rssmanager $rssmanager
	 * @return void
	 */
	public function deleteAction(\TYPO3\Q8yRssmanager\Domain\Model\Rssmanager $rssmanager) {
		//$this->rssmanagerRepository->remove($rssmanager);
		
		$this->flashMessageContainer->add('RSS feed was removed.');
		$this->redirect('list');
	}

}
?>