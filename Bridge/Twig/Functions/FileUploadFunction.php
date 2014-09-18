<?php

namespace Zebba\Bundle\AnnotationBundle\Bridge\Twig\Functions;

use Doctrine\Common\Annotations\Reader;
use Zebba\Bundle\AnnotationBundle\Annotation\FileUpload;

class FileUploadFunction extends \Twig_Extension
{
	/** @var Reader */
	private $reader;

	const ANNOTATION = 'Zebba\Bundle\AnnotationBundle\Annotation\FileUpload';

	/**
	 * Constructor
	 *
	 * @param string $upload_root_dir
	 * @param Reader $reader
	 */
	public function __construct(Reader $reader)
	{
		$this->reader = $reader;
	}

	/**
	 * (non-PHPdoc)
	 * @see Twig_Extension::getFunctions()
	 */
	public function getFunctions()
	{
		return array(
			new \Twig_SimpleFunction('file_upload', array($this, 'fileUploadFunction')),
		);
	}

	/**
	 *
	 * @param string $asset
	 * @param object $entity
	 * @return string
	 */
	public function fileUploadFunction($entity)
	{
		$annotation = $this->getFileUploadAnnotation($entity);

		if (! $annotation) { return; }

		return $annotation->getUploadSubDirectory() .'/'. $entity->{ $annotation->getFilepath() }();
	}

	/**
	 * (non-PHPdoc)
	 * @see Twig_ExtensionInterface::getName()
	 */
	public function getName()
	{
		return 'zebba_fileupload_function';
	}

	/**
	 *
	 * @param object $entity
	 * @return FileUpload|NULL
	 */
	private function getFileUploadAnnotation($entity)
	{
		$reflectionClass = new \ReflectionClass($entity);

		return $this->reader->getClassAnnotation($reflectionClass, self::ANNOTATION);
	}
}