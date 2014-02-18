<?php
namespace TYPO3\Q8yRssmanager\Domain\Repository;

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

 class RssmanagerRepository extends \TYPO3\CMS\Extbase\Persistence\Repository {
 

     public function initializeObject() {
         /** @var $querySettings \TYPO3\CMS\Extbase\Persistence\Generic\Typo3QuerySettings */
         $querySettings = $this->objectManager->get('TYPO3\\CMS\\Extbase\\Persistence\\Generic\\Typo3QuerySettings');
         $querySettings->setRespectStoragePage(FALSE);
         $querySettings->setStoragePageIds(array(0));
         $querySettings->setRespectEnableFields(FALSE);
 
         $querySettings->setIgnoreEnableFields(TRUE);       
         $querySettings->setEnableFieldsToBeIgnored(array('disabled','starttime'));
         $querySettings->setIncludeDeleted(TRUE);
         $querySettings->setRespectSysLanguage(FALSE);
         $this->setDefaultQuerySettings($querySettings);
     }
	 
	
	 
	 public function findListFeeds($feeds) {
         $query = $this->createQuery();
         $query->getQuerySettings()->setRespectStoragePage(FALSE);
         $query->getQuerySettings()->setReturnRawQueryResult(TRUE);
         $query->matching(
		 $query->in('uid', $feeds)
		 );
         return $query->execute();
     }
	 
  
     public function findFeed($url) {
         $query = $this->createQuery();
         $query->getQuerySettings()->setRespectStoragePage(FALSE);
         $query->getQuerySettings()->setReturnRawQueryResult(TRUE);

    $query->matching(
        $query->logicalOr(
           $query->like('feedurl', $url)
        )
    );
         return $query->execute();
     }
	 public function createFeed($feedurl, $feedtitle, $feeddate) {
	    //$query = $this->createQuery();
	    $feedData = array(
	    	'feedurl' => $feedurl,
	    	'feedtitle' => $feedtitle,
	    	'feeddate' => $feeddate
	    );
	    $GLOBALS['TYPO3_DB']->exec_INSERTquery(
    		'tx_q8yrssmanager_domain_model_rssmanager',
    		$feedData
		);
		$lastID=$GLOBALS['TYPO3_DB']->sql_insert_id();
		//$query->statement('INSERT INTO tx_q8yrssmanager_domain_model_rssmanager (feedurl, feedtitle, feeddate) VALUES (?, ?, ?)', array($feedurl, $feedtitle, $feeddate));
		//$query->getQuerySettings()->setRespectStoragePage(FALSE);
        //$query->getQuerySettings()->setReturnRawQueryResult(FALSE);
		return $lastID;
		
		
	 }
 }

?>