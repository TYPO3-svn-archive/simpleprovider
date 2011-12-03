<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2011 Francois Suter (Cobweb) <typo3@cobweb.ch>
*  All rights reserved
*
*  This script is part of the TYPO3 project. The TYPO3 project is
*  free software; you can redistribute it and/or modify
*  it under the terms of the GNU General Public License as published by
*  the Free Software Foundation; either version 2 of the License, or
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
 * Implementation of the simple provider Data Provider
 *
 * @author Francois Suter (Cobweb) <typo3@cobweb.ch>
 * @package TYPO3
 * @subpackage tx_simpleprovider
 *
 * $Id$
 */
class tx_simpleprovider_provider extends tx_tesseract_providerbase {
    /**
     * @var string Type of data structure to provide (default is idList)
     */
    protected $dataStructureType = tx_tesseract::IDLIST_STRUCTURE_TYPE;

	/**
	 * @var language Local instance of a language object
	 */
	protected $languageObject;

	/**
	 * This method returns the type of data structure that the Data Provider can prepare
	 *
	 * @return string Type of the provided data structure
	 */
	public function getProvidedDataStructure() {
		return $this->dataStructureType;
    }

	/**
	 * This method indicates whether the Data Provider can create the type of data structure requested or not
	 *
	 * @param string $type Type of data structure
	 * @return boolean TRUE if it can handle the requested type, FALSE otherwise
	 */
	public function providesDataStructure($type) {
		// Check which type was requested and return true if type can be provided
		// Store requested type internally for later processing
		if ($type == tx_tesseract::IDLIST_STRUCTURE_TYPE) {
			$this->dataStructureType = tx_tesseract::IDLIST_STRUCTURE_TYPE;
			$result = TRUE;
		} elseif ($type == tx_tesseract::RECORDSET_STRUCTURE_TYPE) {
			$this->dataStructureType = tx_tesseract::RECORDSET_STRUCTURE_TYPE;
			$result = TRUE;
		} else {
			$result = FALSE;
		}
		return $result;
    }

	/**
	 * This method returns the type of data structure that the Data Provider can receive as input
	 *
	 * @return string Type of used data structures
	 */
	public function getAcceptedDataStructure() {
		return tx_tesseract::IDLIST_STRUCTURE_TYPE;
    }

	/**
	 * This method indicates whether the Data Provider can use as input the type of data structure requested or not
	 *
	 * @param string $type Type of data structure
	 * @return boolean TRUE if it can use the requested type, FALSE otherwise
	 */
	public function acceptsDataStructure($type) {
		return $type == tx_tesseract::IDLIST_STRUCTURE_TYPE;
    }

	/**
	 * This method assembles the data structure and returns it
	 *
	 * @return array standardised data structure
	 */
	public function getDataStructure() {

    }

	/**
     * This method returns a list of tables and fields (or equivalent) available in the data structure,
     * complete with localized labels
     *
     * @param string $language 2-letter iso code for language
     * @return array List of tables and fields
     */
	public function getTablesAndFields($language = '') {
		$localizedStructure = array();

			// Get language object
		$this->languageObject = tx_tesseract_utilities::getLanguageObject($language);

			// Include the full TCA ctrl section
		if (TYPO3_MODE == 'FE') {
			$GLOBALS['TSFE']->includeTCA();
		}

			// If any table can be chosen from, loop on all tables from the TCA
			// Otherwise, load localized information just for the selected table
		if ($this->providerData['reference_table'] == '*') {
			foreach ($GLOBALS['TCA'] as $table => $tableInformation) {
				$localizedStructure[$table] = $this->getLocalizedInformationForTable($table);
			}
		} else {
			$table = $this->providerData['reference_table'];
			$localizedStructure[$table] = $this->getLocalizedInformationForTable($table);
		}
		return $localizedStructure;
    }

	/**
	 * This method gets localized information for a table and its fields, if defined
	 *
	 * @param string $table Name of the table to get the information for
	 * @return array Localized information
	 */
	protected function getLocalizedInformationForTable($table) {
		$localizedInformation = array('table' => $table, 'fields' => array());
			// Set the table name, if defined in the TCA
		if (isset($GLOBALS['TCA'][$table]['ctrl']['title'])) {
			$tableName = $this->languageObject->sL($GLOBALS['TCA'][$table]['ctrl']['title']);
			$localizedInformation['name'] = $tableName;
		} else {
			$localizedInformation['name'] = $table;
		}
			// Load the full TCA for the table
		t3lib_div::loadTCA($table);
			// Get all the database fields for the table
		$fields = tx_overlays::getAllFieldsForTable($table);
			// Loop on all fields and get their localized label, if defined
		foreach ($fields as $fieldName) {
			if (isset($GLOBALS['TCA'][$table]['columns'][$fieldName]['label'])) {
				$localizedInformation['fields'][$fieldName] = $this->languageObject->sL($GLOBALS['TCA'][$table]['columns'][$fieldName]['label']);
			} else {
				$localizedInformation['fields'][$fieldName] = $fieldName;
			}
		}

		return $localizedInformation;
	}
}
?>