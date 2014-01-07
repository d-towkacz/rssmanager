<?php
namespace TYPO3\Q8yRssmanager\Domain\Repository;
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
	    $query = $this->createQuery();
		$query->statement('INSERT INTO tx_q8yrssmanager_domain_model_rssmanager (feedurl, feedtitle, feeddate) VALUES (?, ?, ?)', array($feedurl, $feedtitle, $feeddate));
		$query->getQuerySettings()->setRespectStoragePage(FALSE);
        $query->getQuerySettings()->setReturnRawQueryResult(FALSE);
		return $query->execute()->toArray();
		
		
	 }
 }

?>