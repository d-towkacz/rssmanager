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
	* @author d.towkacz@quintinity.de
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

		public $flashBox = ""; 


		protected function initializeView(\TYPO3\CMS\Extbase\Mvc\View\ViewInterface $view) {

		}
		protected function initializeAction() {

			$mode = intval($this->settings['rssSettings']);
			if ($mode < 1)
			{
				//$this->actionMethodName = "listAction";
			} else {
				if ($mode == 1)
				{
					$this->actionMethodName = "singleAction";
				} else if ($mode == 2)
					{
						$this->actionMethodName = "widgetAction"; 
					}	
			} 


		}

		public function widgetAction()
		{	
			
			$modeSettings = $this->settings['widgetMode'];
			$feed_url = $this->settings['widgetmode'];
			$this->view->setTemplatePathAndFilename(\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extPath('q8y_rssmanager').$modeSettings['layoutTemplate']);
			$repoFeed = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance("TYPO3\Q8yRssmanager\Domain\Repository\RssmanagerRepository"); 
			$out_records_list = array();
			$num_record = 0;
			$feed = new \SimplePie;
			$feed->set_feed_url($feed_url);
			$feed->set_cache_location(PATH_site.$this->settings['application']['cachePath']);
			$feed->enable_cache();
			$feed->set_cache_duration(intval($modeSettings['cacheTime']));
			$feed->strip_htmltags(array('blink', 'marquee','img'));
			$feed->init();
			$feed->handle_content_type();
			$first_feed = $feed->get_item(0);
			$second_feed = $feed->get_item(1);

			//$out_records_list[$num_record]['title'] = html_entity_decode($item->get_title());
			//$out_records_list[$num_record]['date'] = $item->get_date();

			$this->view->assign('first_title', html_entity_decode($first_feed->get_title()));
			$this->view->assign('first_date', $first_feed->get_date($modeSettings['formatDate'])); 

			$this->view->assign('second_title', html_entity_decode($second_feed->get_title()));
			$this->view->assign('second_date', $second_feed->get_date($modeSettings['formatDate'])); 

			//$this->view->assign('widgettitle', $this->settings['widgettitle']);    
			$this->view->assign('icon1', $this->settings['icon1']); 
			//$this->view->assign('icon2', $this->settings['icon2']);     
			$this->view->assign('pidto', $this->settings['widgetoption']);
			$this->view->assign('rsssource', $feed_url);     
			$this->view->assign('rssrecords', $out_records_list);



		}	

		public function singleAction()
		{

			$modeSettings = $this->settings['singleMode'];
			$feed_url = $this->settings['singlemode'];
			$this->view->setTemplatePathAndFilename(\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extPath('q8y_rssmanager').$modeSettings['layoutTemplate']);
			$repoFeed = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance("TYPO3\Q8yRssmanager\Domain\Repository\RssmanagerRepository"); 
			$out_records_list = array();
			$num_record = 0;
			/* Init SimplePie RSS parser*/
			$feed = new \SimplePie;
			$feed->set_feed_url($feed_url);
			$feed->set_cache_location(PATH_site.$this->settings['application']['cachePath']);
			$feed->enable_cache();
			$feed->set_cache_duration(intval($modeSettings['cacheTime']));
			$feed->strip_htmltags(array('blink', 'marquee','img'));
			$feed->init();
			$feed->handle_content_type();
			foreach ($feed->get_items() as $item)
			{
				$out_records_list[$num_record]['title'] = html_entity_decode($item->get_title());
				$out_records_list[$num_record]['link'] = $item->get_link();
				$out_records_list[$num_record]['description'] = strip_tags(html_entity_decode ($item->get_description(), ENT_COMPAT, $this->settings['application']['convertEncoding']));
				$out_records_list[$num_record]['date'] = $item->get_date($modeSettings['formatDate']);
				$num_record++;
			}

			$this->view->assign('rsssource', $feed_url);     
			$this->view->assign('rssrecords', $out_records_list);
		} 
		public function listAction() {

			$modeSettings = $this->settings['managerMode'];
			$feuser_uid = $GLOBALS['TSFE']->fe_user->user['uid'];
			$repoFeed = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance("TYPO3\Q8yRssmanager\Domain\Repository\RssmanagerRepository");
			$repoUser = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance("TYPO3\Q8yRssmanager\Domain\Repository\UserRepository");
			$userData = $repoUser->findUser($feuser_uid);
			$userData = $userData[0];	
			$feed_uids = explode(',',$userData['feed_uids']);	
			$out_feed_list = $repoFeed->findListFeeds($feed_uids);
			$out_records_list = array();
			$num_chanel = 0;
			$num_record = 0;
			foreach ($out_feed_list as $item) {
				/* Init SimplePie RSS parser*/
				$feed = new \SimplePie;
				$feed->set_feed_url($item['feedurl']);
				$feed->set_cache_location(PATH_site.$this->settings['application']['cachePath']);
				$feed->enable_cache();
				if ($GLOBALS["TSFE"]->fe_user->getKey("ses","purgecache") == "clear")
				{
					$feed->set_cache_duration(0);	
					$GLOBALS['TSFE']->fe_user->setKey("ses","purgecache", "");
				} else {
					$feed->set_cache_duration(intval($modeSettings['cacheTime']));	
				}
				$feed->strip_htmltags(array('blink', 'marquee','img'));
				$feed->init();
				$feed->handle_content_type();
				$out_records_list[$num_chanel]['title'] = $feed->get_title();
				$out_records_list[$num_chanel]['uid'] = $item['uid'];
				$out_records_list[$num_chanel]['records'] = array();
				foreach ($feed->get_items() as $item)
				{
					$out_records_list[$num_chanel]['records'][$num_record]['title'] = html_entity_decode($item->get_title());
					$out_records_list[$num_chanel]['records'][$num_record]['link'] = $item->get_link();
					$out_records_list[$num_chanel]['records'][$num_record]['description'] = strip_tags(html_entity_decode ($item->get_description(), ENT_COMPAT, $this->settings['application']['convertEncoding']));
					$out_records_list[$num_chanel]['records'][$num_record]['date'] = $item->get_date($modeSettings['formatDate']);
					$num_record++;
				}
				$num_chanel++;
			}


			$flashBox = $GLOBALS["TSFE"]->fe_user->getKey("ses","flashmess"); 
			$GLOBALS['TSFE']->fe_user->setKey("ses","flashmess", "");
			$this->view->assign('flashmessage', $flashBox);
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
		public function createAction(\TYPO3\Q8yRssmanager\Domain\Model\Rssmanager $newRssmanager = NULL) {
			$feed_url = $newRssmanager->getFeedurl();

			if ($feed_url == "")
			{
				$box = $this->renderMessage(\TYPO3\CMS\Extbase\Utility\LocalizationUtility::translate("actualCreate"),"info");  
				$GLOBALS['TSFE']->fe_user->setKey("ses","flashmess", $box);
				$GLOBALS['TSFE']->fe_user->setKey("ses","purgecache", "clear");
				$this->redirect('list');
			}

			/* Init SimplePie RSS parser*/
			$feed = new \SimplePie;
			$feed->set_feed_url($feed_url);
			$feed->init();

			if ($feed->error == "")
			{
				$feed_link = $feed_url; 
				$feed_title = $feed->get_title();
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
					$repoUser->updateUser($feuser_uid,$feed_uids_str);
				}
				else
				{
					$feed_uid = $repoFeed->createFeed($feed_link, $feed_title, 0);
					$userData = $repoUser->findUser($feuser_uid);
					$userData = $userData[0];
					$feed_uids = explode(',',$userData['feed_uids']);
					$feed_uids[] = $feed_uid;
					$feed_uids = array_unique($feed_uids);
					$feed_uids_str = implode(',',$feed_uids);
					$repoUser->updateUser($feuser_uid,$feed_uids_str);		
				}
				$box = $this->renderMessage(\TYPO3\CMS\Extbase\Utility\LocalizationUtility::translate("infoCreate"),"");
				$GLOBALS['TSFE']->fe_user->setKey("ses","flashmess", $box);
				$this->redirect('list');

			} else {
				$box = $this->renderMessage(\TYPO3\CMS\Extbase\Utility\LocalizationUtility::translate("errorCreate"),"warning");  
				$GLOBALS['TSFE']->fe_user->setKey("ses","flashmess", $box);
				$this->redirect('list');
			
			}
		}



		public function addurlAction() {
			//$feed_url = $rssmanager->getFeedurl();
			$req = $this->request->getArguments();
			$feed_url = $req['feedurl'];
			
			if ($feed_url == "")
			{
				$box = $this->renderMessage(\TYPO3\CMS\Extbase\Utility\LocalizationUtility::translate("actualCreate"),"info");  
				$GLOBALS['TSFE']->fe_user->setKey("ses","flashmess", $box);
				$GLOBALS['TSFE']->fe_user->setKey("ses","purgecache", "clear");
				$this->redirect('list');
			}

			/* Init SimplePie RSS parser*/
			$feed = new \SimplePie;
			$feed->set_feed_url($feed_url);
			$feed->init();

			if ($feed->error == "")
			{
				$feed_link = $feed_url; 
				$feed_title = $feed->get_title();
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
					$repoUser->updateUser($feuser_uid,$feed_uids_str);
				}
				else
				{
					$feed_uid = $repoFeed->createFeed($feed_link, $feed_title, 0);
					$userData = $repoUser->findUser($feuser_uid);
					$userData = $userData[0];
					$feed_uids = explode(',',$userData['feed_uids']);
					$feed_uids[] = $feed_uid;
					$feed_uids = array_unique($feed_uids);
					$feed_uids_str = implode(',',$feed_uids);
					$repoUser->updateUser($feuser_uid,$feed_uids_str);		
				}
				$box = $this->renderMessage(\TYPO3\CMS\Extbase\Utility\LocalizationUtility::translate("infoCreate"),"");
				$GLOBALS['TSFE']->fe_user->setKey("ses","flashmess", $box);
				$this->uriBuilder->setTargetPageUid(342);

			$link = $this->uriBuilder->build();
			$this->redirectToUri($link);
				//$this->redirect('list');

			} else {
				$box = $this->renderMessage(\TYPO3\CMS\Extbase\Utility\LocalizationUtility::translate("errorCreate"),"warning");  
				$GLOBALS['TSFE']->fe_user->setKey("ses","flashmess", $box);
				$this->uriBuilder->setTargetPageUid(342);

			$link = $this->uriBuilder->build();
			$this->redirectToUri($link);
				//$this->redirect('list');
			
			}
		}

		/**
		* action delete
		*
		* @param \TYPO3\Q8yRssmanager\Domain\Model\Rssmanager $rssmanager
		* @return void
		*/
		public function deleteAction(\TYPO3\Q8yRssmanager\Domain\Model\Rssmanager $rssmanager) {
			$uid = $rssmanager->getFeedUid();
			//$this->rssmanagerRepository->remove($rssmanager);
			$feuser_uid = $GLOBALS['TSFE']->fe_user->user['uid'];
			$repoUser = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance("TYPO3\Q8yRssmanager\Domain\Repository\UserRepository");
			$userData = $repoUser->findUser($feuser_uid);
			$userData = $userData[0];
			$feed_uids = explode(',',$userData['feed_uids']);
			$feed_uids = array_diff($feed_uids, array($uid));
			$feed_uids = array_unique($feed_uids);
			$feed_uids_str = implode(',',$feed_uids);
			$repoUser->updateUser($feuser_uid,$feed_uids_str);
			$box = $this->renderMessage(\TYPO3\CMS\Extbase\Utility\LocalizationUtility::translate("infoDelete"),"info");  
			$GLOBALS['TSFE']->fe_user->setKey("ses","flashmess", $box);
			$this->uriBuilder->setTargetPageUid(342);

			$link = $this->uriBuilder->build();
			$this->redirectToUri($link);
		}

		public function renderMessage($text, $type)
		{

			$box = '<div data-alert="" class="alert-box '.$type.'">'.$text.'<a href="#" class="close">×</a></div>';
			return $box;
		}

	}
?>