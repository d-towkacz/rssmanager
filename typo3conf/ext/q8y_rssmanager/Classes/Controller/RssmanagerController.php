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
		$feed_url = $this->settings['widgetmode'];
		$this->view->setTemplatePathAndFilename(\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extPath('q8y_rssmanager') . 'Resources/Private/Templates/Rssmanager/Widget.html');
		$repoFeed = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance("TYPO3\Q8yRssmanager\Domain\Repository\RssmanagerRepository"); 
		$out_records_list = array();
		$num_record = 0;
		$feed = new \SimplePie;
		    $feed->set_feed_url($feed_url);
		    $feed->set_cache_location(PATH_site.'typo3temp');
		    $feed->enable_cache();
		    $feed->set_cache_duration(50);
		    $feed->strip_htmltags(array('blink', 'marquee','img'));
		    $feed->init();
		    $feed->handle_content_type();
		    $first_feed = $feed->get_item(0);
		    //$second_feed = $feed->get_item(1);
		    
				//$out_records_list[$num_record]['title'] = html_entity_decode($item->get_title());
				//$out_records_list[$num_record]['date'] = $item->get_date();
			
		    
		
		
		$this->view->assign('first_title', html_entity_decode($first_feed->get_title()));
		$this->view->assign('first_date', $first_feed->get_date("d.m.Y H:i")); 
		
		//$this->view->assign('second_title', html_entity_decode($second_feed->get_title()));
		//$this->view->assign('second_date', $second_feed->get_date("d.m.Y H:i")); 
		    
		//$this->view->assign('widgettitle', $this->settings['widgettitle']);    
		$this->view->assign('icon1', $this->settings['icon1']); 
		//$this->view->assign('icon2', $this->settings['icon2']);     
		$this->view->assign('pidto', $this->settings['widgetoption']);
		$this->view->assign('rsssource', $feed_url);     
		$this->view->assign('rssrecords', $out_records_list);
		
		
		
	}	
	
	public function singleAction()
	{
		
		$feed_url = $this->settings['singlemode'];
		$this->view->setTemplatePathAndFilename(\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extPath('q8y_rssmanager') . 'Resources/Private/Templates/Rssmanager/Single.html');
		$repoFeed = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance("TYPO3\Q8yRssmanager\Domain\Repository\RssmanagerRepository"); 
		$out_records_list = array();
		$num_record = 0;
		$feed = new \SimplePie;
		    $feed->set_feed_url($feed_url);
		    $feed->set_cache_location(PATH_site.'typo3temp');
		    $feed->enable_cache();
		    $feed->set_cache_duration(50);
		    $feed->strip_htmltags(array('blink', 'marquee','img'));
		    $feed->init();
		    $feed->handle_content_type();
		    foreach ($feed->get_items() as $item)
		    {
				$out_records_list[$num_record]['title'] = html_entity_decode($item->get_title());
				$out_records_list[$num_record]['link'] = $item->get_link();
				$out_records_list[$num_record]['description'] = strip_tags(html_entity_decode ($item->get_description(), ENT_COMPAT, "UTF-8"));
				$out_records_list[$num_record]['date'] = $item->get_date();
				$num_record++;
		    }
		    
		$this->view->assign('rsssource', $feed_url);     
		$this->view->assign('rssrecords', $out_records_list);
		//echo "----";
		//exit;
	} 
	public function listAction() {
	
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
		    $feed->set_cache_duration(18000);
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
				$out_records_list[$num_chanel]['records'][$num_record]['description'] = strip_tags(html_entity_decode ($item->get_description(), ENT_COMPAT, "UTF-8"));
				$out_records_list[$num_chanel]['records'][$num_record]['date'] = $item->get_date();
				$num_record++;
		    }
		    $num_chanel++;
			//print_r($feed);
		}
		
		//exit;
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
	public function createAction(\TYPO3\Q8yRssmanager\Domain\Model\Rssmanager $newRssmanager) {
		$feed_url = $newRssmanager->getFeedurl();
		if ($feed_url == "")
		{
			//$this->flashMessageContainer->add('RSS-Liste wurde aktualisiert.','',\TYPO3\CMS\Core\Messaging\FlashMessage::WARNING);
			$box = $this->renderMessage("RSS-Liste wurde aktualisiert.","info");  
		    $GLOBALS['TSFE']->fe_user->setKey("ses","flashmess", $box);
			$this->redirect('list');
		}
		$feed = new \SimplePie;
		$feed->set_feed_url($feed_url);
		$feed->init();
		
		if ($feed->error == "")
		{
			//$feed_link = $feed->get_link();
			$feed_link = $feed_url; 
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
			
			//$this->flashMessageContainer->add('RSS-Feed hinzugefügt wurde.','',\TYPO3\CMS\Core\Messaging\FlashMessage::OK);
			$box = $this->renderMessage("RSS-Feed wurde hinzugefügt .","");
			$GLOBALS['TSFE']->fe_user->setKey("ses","flashmess", $box);
			$this->redirect('list');
			
		} else {
			//$this->flashMessageContainer->add('Wir konnten keinen RSS-Feed unter dieser URL finden. Bitte überprüfen Sie Ihre Angaben.','',\TYPO3\CMS\Core\Messaging\FlashMessage::OK);
			$box = $this->renderMessage("Wir konnten keinen RSS-Feed unter dieser URL finden. Bitte überprüfen Sie Ihre Angaben.","warning");  
			$GLOBALS['TSFE']->fe_user->setKey("ses","flashmess", $box);
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
		$uid = $rssmanager->getFeedUid();
		//$this->rssmanagerRepository->remove($rssmanager);
		$feuser_uid = $GLOBALS['TSFE']->fe_user->user['uid'];
		$repoUser = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance("TYPO3\Q8yRssmanager\Domain\Repository\UserRepository");
				$userData = $repoUser->findUser($feuser_uid);
				$userData = $userData[0];
				$feed_uids = explode(',',$userData['feed_uids']);
				$feed_uids = array_diff($feed_uids, array($uid));
				//$feed_uids[] = $feed_uid;
				$feed_uids = array_unique($feed_uids);
				$feed_uids_str = implode(',',$feed_uids);
				//print_r($userData['feed_uids']);
				//exit;
		$repoUser->updateUser($feuser_uid,$feed_uids_str);
		
		//$this->flashMessageContainer->add('RSS-Feed wurde gelöscht.','',\TYPO3\CMS\Core\Messaging\FlashMessage::OK);
		$box = $this->renderMessage("RSS-Feed wurde gelöscht.","info");  
		$GLOBALS['TSFE']->fe_user->setKey("ses","flashmess", $box);
		$this->redirect('list');
	}
	
	public function renderMessage($text, $type)
	{
	    
	    $box = '<div data-alert="" class="alert-box '.$type.'">'.$text.'<a href="#" class="close">×</a></div>';
	    return $box;
	}

}
?>