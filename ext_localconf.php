<?php
if (!defined ('TYPO3_MODE')) {
 	die ('Access denied.');
}

	// Register as Data Provider service
	// Note that the subtype corresponds to the name of the database table
t3lib_extMgm::addService($_EXTKEY,  'dataprovider' /* sv type */,  'tx_simpleprovider_selection' /* sv key */,
	array(
		'title' => 'Simple Provider',
		'description' => 'Data Provider based on simple record selections',

		'subtype' => 'tx_simpleprovider_selection',

		'available' => TRUE,
		'priority' => 50,
		'quality' => 50,

		'os' => '',
		'exec' => '',

		'classFile' => t3lib_extMgm::extPath($_EXTKEY, 'class.tx_simpleprovider_provider.php'),
		'className' => 'tx_simpleprovider_provider',
	)
);
?>