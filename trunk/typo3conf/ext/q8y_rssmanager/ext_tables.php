<?php
if (!defined('TYPO3_MODE')) {
	die ('Access denied.');
}

\TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerPlugin(
	$_EXTKEY,
	'Rssmanager',
	'Rss Manager'
);

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addStaticFile($_EXTKEY, 'Configuration/TypoScript', 'Rss Manager');

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addLLrefForTCAdescr('tx_q8yrssmanager_domain_model_rssmanager', 'EXT:q8y_rssmanager/Resources/Private/Language/locallang_csh_tx_q8yrssmanager_domain_model_rssmanager.xlf');
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::allowTableOnStandardPages('tx_q8yrssmanager_domain_model_rssmanager');
$TCA['tx_q8yrssmanager_domain_model_rssmanager'] = array(
	'ctrl' => array(
		'title'	=> 'LLL:EXT:q8y_rssmanager/Resources/Private/Language/locallang_db.xlf:tx_q8yrssmanager_domain_model_rssmanager',
		'label' => 'feedurl',
		'tstamp' => 'tstamp',
		'crdate' => 'crdate',
		'cruser_id' => 'cruser_id',
		'dividers2tabs' => TRUE,

		'versioningWS' => 2,
		'versioning_followPages' => TRUE,
		'origUid' => 't3_origuid',
		'languageField' => 'sys_language_uid',
		'transOrigPointerField' => 'l10n_parent',
		'transOrigDiffSourceField' => 'l10n_diffsource',
		'delete' => 'deleted',
		'enablecolumns' => array(
			'disabled' => 'hidden',
			'starttime' => 'starttime',
			'endtime' => 'endtime',
		),
		'searchFields' => 'feedurl,feedtitle,feeddate,',
		'dynamicConfigFile' => \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extPath($_EXTKEY) . 'Configuration/TCA/Rssmanager.php',
		'iconfile' => \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extRelPath($_EXTKEY) . 'Resources/Public/Icons/tx_q8yrssmanager_domain_model_rssmanager.gif'
	),
);

?>