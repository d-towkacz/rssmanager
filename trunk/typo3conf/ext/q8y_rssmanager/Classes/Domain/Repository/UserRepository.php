<?php
namespace TYPO3\Q8yRssmanager\Domain\Repository;

class UserRepository extends \TYPO3\CMS\Extbase\Domain\Repository\FrontendUserRepository {
	protected $userRepository;
	
	
	public function injectFrontendUserRepository (TYPO3\Q8yRssmanager\Domain\Repository\UserRepository $userRepository) {
		$this->userRepository = $userRepository;
	}
	
	public function initializeObject() {
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
	
	public function findUser($uid) {
         $query = $this->createQuery();
		 $query->statement('SELECT * FROM fe_users WHERE uid=?', array($uid));
         $query->getQuerySettings()->setRespectStoragePage(FALSE);
         $query->getQuerySettings()->setReturnRawQueryResult(TRUE);
         //$query->equals("uid",$uid);
         return $query->execute();
     }
	
    public function updateUser($uid, $feed_uid) {
         $query = $this->createQuery();
		 $query->statement('UPDATE fe_users set feed_uids = ? WHERE uid=?', array($feed_uid, $uid));
         $query->getQuerySettings()->setRespectStoragePage(FALSE);
         $query->getQuerySettings()->setReturnRawQueryResult(TRUE);
         @$query->execute();
		 //$query->equals("uid",$uid);
         return true;
     }	


}
?>