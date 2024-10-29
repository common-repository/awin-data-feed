<?php

namespace Datafeed\Models\Csv;

class UploadErrorHandler
{
	/** @var  string */
	private $message;

	/** @var bool */
	private $valid = true;

	/**
	 * @param array $fileData
	 */
	public function handleError(array $fileData)
	{
		$this->getFileErrorMessage($fileData);
		$this->getFileErrorCodeToMessage($fileData['error']);
	}

	/**
	 * @return string
	 */
	public function getMessage()
	{
		return $this->message;
	}

	/**
	 * @return boolean
	 */
	public function isValid()
	{
		return $this->valid;
	}

	/**
	 * @param string $code
	 * @return string
	 */
	private function getFileErrorCodeToMessage($code)
	{
		$max_file_size = ini_get('upload_max_filesize');
		switch ($code) {
			case UPLOAD_ERR_INI_SIZE:
				$this->message = "The uploaded file exceeds " . $max_file_size . "B limit in your php.ini";
				break;
			case UPLOAD_ERR_FORM_SIZE:
				$this->message = "The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form";
				break;
			case UPLOAD_ERR_PARTIAL:
				$this->message = "The uploaded file was only partially uploaded";
				break;
			case UPLOAD_ERR_NO_FILE:
				$this->message = "No file was uploaded";
				break;
			case UPLOAD_ERR_NO_TMP_DIR:
				$this->message = "Missing a temporary folder";
				break;
			case UPLOAD_ERR_CANT_WRITE:
				$this->message = "Failed to write file to disk";
				break;
			case UPLOAD_ERR_EXTENSION:
				$this->message = "File upload stopped by extension";
				break;
		}
		if ($this->message) {
			$this->valid = false;
		}

		return $this->message;
	}

	/**
	 * @param array $file
	 * @return string
	 */
	private function getFileErrorMessage(array $file)
	{
		$mimes = array(
			'text/plain',
			'text/comma-separated-values',
			'text/csv',
			'application/csv',
			'application/excel',
			'application/vnd.ms-excel',
			'application/vnd.msexcel',
			'text/anytext',
			'application/octet-stream',
			'text/tsv'
		);

		if (!in_array($file["type"], $mimes)) {
			$this->valid = false;
			$this->message = "Invalid csv file ?";
		}
	}
}
