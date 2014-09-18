<?php

namespace Zebba\Bundle\AnnotationBundle\Annotation\Handler;

use Doctrine\Common\Annotations\AnnotationException;
use Doctrine\Common\Persistence\Event\LifecycleEventArgs;
use Doctrine\Common\Persistence\Event\PreUpdateEventArgs;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Zebba\Bundle\AnnotationBundle\Annotation\FileUpload;

final class FileUploadHandler
{
	/** @var string */
	private $uploadRootDir;
	/** @var LoggerInterface */
	private $logger;

	/**
	 * Constructor
	 *
	 * @param string $upload_root_dir
	 * @param LoggerInterface $logger
	 */
	public function __construct($upload_root_dir, LoggerInterface $logger)
	{
		$this->uploadRootDir = $upload_root_dir;

		$this->logger = $logger;
	}

	/**
	 *
	 * @param LifecycleEventArgs $args
	 * @param FileUpload $annotation
	 */
	public function preUpload(LifecycleEventArgs $args, FileUpload $annotation)
	{
		$entity = $args->getEntity();

		$file = self::getFile($entity, $annotation);

		if ($file instanceof UploadedFile) {
			$now = new \DateTime('now');

			$filename = sprintf('%s_%s.%s',
				$entity->{ $annotation->getFilename() }(),
				$now->format('Ymd_Hi'),
				$file->guessExtension());

			$entity->{ $annotation->setFilepath() }($filename);

			/* @var $args PreUpdateEventArgs */
			if (method_exists($args, 'setNewValue')) {
				$args->setNewValue($annotation->getFilepathProperty(), $filename);
			}
		}
	}

	/**
	 *
	 * @param LifecycleEventArgs $args
	 * @param FileUpload $annotation
	 */
	public function postUpload(LifecycleEventArgs $args, FileUpload $annotation)
	{
		$entity = $args->getEntity();

		$file = self::getFile($entity, $annotation);

		if (is_null($file)) { return; }

		$directory = $this->uploadRootDir . $annotation->getUploadSubDirectory();
		$filename = $entity->{ $annotation->getFilepath() }();

		$this->logger->debug('directory: '. $directory);
		$this->logger->debug('filename: '. $filename);

		$file->move($directory, $filename);

		$temp_path = $entity->{ $annotation->getTempFilepath() }();

		if ($temp_path && 'initial' !== $temp_path) {
			unlink($directory .'/'. $temp_path);

			$entity->{ $annotation->setTempFilepath() }(null);
		}

		$entity->{ $annotation->setFile() }(null);
	}

	/**
	 *
	 * @param LifecycleEventArgs $args
	 * @param FileUpload $annotation
	 */
	public function postRemove(LifecycleEventArgs $args, FileUpload $annotation)
	{
		$entity = $args->getEntity();

		$entity = $args->getEntity();

		$annotation = $this->getFileUploadAnnotation();

		if (! $annotation) { return; }

		$file = sprintf('%s/%s%s',
			$this->uploadRootDir,
			$annotation->getUploadSubDirectory(),
			$entity->{ $annotation->getFilepath() }()
		);

		if (is_file($file) && is_writable($file)) {
			unlink($file);
		}
	}

	/**
	 *
	 * @param object $entity
	 * @param FileUpload $annotation
	 */
	static private function getFile($entity, FileUpload $annotation)
	{
		return $entity->{ $annotation->getFile() }();
	}
}