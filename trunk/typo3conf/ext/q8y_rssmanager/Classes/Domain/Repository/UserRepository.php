<?php
namespace TYPO3\Q8yRssmanager\Domain\Repository;

class UserRepository extends \TYPO3\CMS\Extbase\Persistence\Repository {
	
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
	/**
	 * Returns logged user.
	 *
	 * @return Tx_Kettfitcomm_Domain_Model_User
	 *         
	 * @api
	 */
	public function findUser($uid) {
         $query = $this->createQuery();
         $query->getQuerySettings()->setRespectStoragePage(FALSE);
         $query->getQuerySettings()->setReturnRawQueryResult(TRUE);

         $query->equals("uid",$uid);
         return $query->execute();
     }


}
?>