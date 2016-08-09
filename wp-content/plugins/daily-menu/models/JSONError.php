<?php
/**
 * A JSON error correspond to the JSON object used by jTable to print an error message
 * @author Laurent Morretton
 *
 */
class JSONError implements JsonSerializable {
	const RESULT = "KO";
	private $messages;
	
	/**
	 * Initialize the object with a error message
	 * @param unknown $arg
	 */
	function __construct($arg = null){
		$messages[] = $arg;
	}
	
	/**
	 * Serialize the error message (non-PHPdoc) and return
	 * a table that will be encodeed by json_encode() method 
	 * @see JsonSerializable::jsonSerialize()
	 */
	public function jsonSerialize() {
		$jTableResult = array();
		$jTableResult['Result'] = RESULT;
		if (isset($messages)) {
			$jTableResult['Message'] = $messages[1];
		} else {
			$jTableResult['Message'] = __("A error occurs",DM_DOMAIN_NAME);
		}
		return $jTableResult;
	}
	
	/**
	 * Return the JSON serialized object
	 * @return string
	 */
	public function getJSON () {
		return json_encode($this->jsonSerialize());
	}
	
	/**
	 * Sets the error message
	 * @param string $arg
	 */
	public function setNewError($arg) {
		$messages [] = $arg;
	}
}