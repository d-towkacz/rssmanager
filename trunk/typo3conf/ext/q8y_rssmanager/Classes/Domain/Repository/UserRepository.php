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
         //$query = $this->createQuery();
		 //$query->statement('UPDATE fe_users set feed_uids = ? WHERE uid=?', array($feed_uid, $uid));
         //$query->getQuerySettings()->setRespectStoragePage(FALSE);
         //$query->getQuerySettings()->setReturnRawQueryResult(TRUE);
         //@$query->execute();
         $userData = array(
         	'feed_uids' => $feed_uid
         );
         $GLOBALS['TYPO3_DB']->exec_UPDATEquery(
    	 	'fe_users',
    		'uid = '.$uid,
    		$userData
		 );
         
		 //$query->equals("uid",$uid);
         return true;
     }	


}
?>