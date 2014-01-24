<?php
if (!defined('TYPO3_MODE')) {
	die ('Access denied.');
}

\TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin(
	'TYPO3.' . $_EXTKEY,
	'Rssmanager',
	array(
		'Rssmanager' => 'list, show, delete, new, create',
		
	),
	// non-cacheable actions
	array(
		'Rssmanager' => 'list, show, delete, new, create',
		
	)
);

?>