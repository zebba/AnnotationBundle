<?php

namespace Zebba\Bundle\AnnotationBundle\Annotation;

use Doctrine\Common\Annotations\AnnotationException;

/**
 * @Annotation
 * @Target({"CLASS"})
 */
final class FileUpload
{
	/** @var string */
	private $filepath_property;
	/** @var string */
	private $file_property;
	/** @var string */
	private $filename_method;
	/** @var string */
	private $upload_sub_directory;
	/** @var string */
	private $temp_filepath_property;

	/**
	 *
	 * @param array $values
	 * @throws AnnotationException on missing parameters
	 */
	public function __construct(array $values = array())
	{
		if (array_key_exists('filepath', $values)) {
			$this->filepath_property = $values['filepath'];
		} else {
			throw AnnotationException::requiredError('filepath', '@FileUpload');
		}

		if (array_key_exists('file', $values)) {
			$this->file_property = $values['file'];
		} else {
			throw AnnotationException::requiredError('fileProperty', '@FileUpload');
		}

		if (array_key_exists('tempFilePath', $values)) {
			$this->temp_filepath_property = $values['tempFilePath'];
		} else {
			throw AnnotationException::requiredError('tempFilePath', '@FileUpload', get_class($this), 'expected');
		}

		if (array_key_exists('filenameMethod', $values)) {
			$this->filename_method = $values['filenameMethod'];
		} else {
			throw AnnotationException::requiredError('filenameMethod', '@FileUpload');
		}

		if (array_key_exists('uploadSubDirectory', $values)) {
			$this->upload_sub_directory = $values['uploadSubDirectory'];
		} else {
			throw AnnotationException::requiredError('uploadSubDirectory', '@FileUpload');
		}
	}

	/**
	 * @return string
	 */
	public function getFilepathProperty()
	{
		return $this->filepath_property;
	}

	/**
	 * @return string
	 */
	public function setFilepath()
	{
		return self::getMethodFromProperty('set', $this->filepath_property);
	}

	/**
	 * @return string
	 */
	public function getFilepath()
	{
		return self::getMethodFromProperty('get', $this->filepath_property);
	}

	/**
	 * @return string
	 */
	public function getFileProperty()
	{
		return $this->file_property;
	}

	/**
	 * @return string
	 */
	public function setFile()
	{
		return self::getMethodFromProperty('set', $this->file_property);
	}

	/**
	 * @return string
	 */
	public function getFile()
	{
		return self::getMethodFromProperty('get', $this->file_property);
	}

	/**
	 * @return string
	 */
	public function getTempFilepathProperty()
	{
		return $this->temp_filepath_property;
	}

	/**
	 * @return string
	 */
	public function setTempFilepath()
	{
		return self::getMethodFromProperty('set', $this->temp_filepath_property);
	}

	/**
	 * @return string
	 */
	public function getTempFilepath()
	{
		return self::getMethodFromProperty('get', $this->temp_filepath_property);
	}

	/**
	 * @return string
	 */
	public function getFilename()
	{
		return $this->filename_method;
	}

	/**
	 * @return string
	 */
	public function getUploadSubDirectory()
	{
		return $this->upload_sub_directory;
	}

	/**
	 *
	 * @param string $access
	 * @param string $property
	 * @return string
	 */
	static private function getMethodFromProperty($access, $property)
	{
		return sprintf('%s%s', strtolower($access), str_replace(' ', '', ucwords(str_replace('_', ' ', $property))));
	}
}