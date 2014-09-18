<?php

namespace Zebba\Bundle\AnnotationBundle\Event\Listener;

use Doctrine\Common\Annotations\Reader;
use Doctrine\Common\Persistence\Event\LifecycleEventArgs;
use Psr\Log\LoggerInterface;
use Zebba\Bundle\AnnotationBundle\Annotation\Handler\FileUploadHandler;

class FileUploadListener
{
	/** @var FileUploadHandler */
	private $handler;
	/** @var Reader */
	private $reader;
	/** @var LoggerInterface */
	private $logger;

	const ANNOTATION = 'Zebba\Bundle\AnnotationBundle\Annotation\FileUpload';

	/**
	 * Constructor
	 *
	 * @param Reader $reader
	 * @param FileUploadHandler $handler
	 * @param LoggerInterface $logger
	 */
	public function __construct(Reader $reader, FileUploadHandler $handler, LoggerInterface $logger)
	{
		$this->reader = $reader;
		$this->handler = $handler;

		$this->logger = $logger;
	}

	/**
	 *
	 * @param LifecycleEventArgs $args
	 */
	public function prePersist(LifecycleEventArgs $args)
	{
		$annotation = $this->getFileUploadAnnotation($args);

		if (! $annotation) { return; }

		$this->handler->preUpload($args, $annotation);
	}

	/**
	 *
	 * @param LifecycleEventArgs $args
	 */
	public function preUpdate(LifecycleEventArgs $args)
	{
		$annotation = $this->getFileUploadAnnotation($args);

		if (! $annotation) { return; }

		$this->handler->preUpload($args, $annotation);
	}

	/**
	 *
	 * @param LifecycleEventArgs $args
	 */
	public function postPersist(LifecycleEventArgs $args)
	{
		$annotation = $this->getFileUploadAnnotation($args);

		if (! $annotation) { return; }

		$this->handler->postUpload($args, $annotation);
	}

	/**
	 *
	 * @param LifecycleEventArgs $args
	 */
	public function postUpdate(LifecycleEventArgs $args)
	{
		$annotation = $this->getFileUploadAnnotation($args);

		if (! $annotation) { return; }

		$this->handler->postUpload($args, $annotation);
	}

	/**
	 *
	 * @param LifecycleEventArgs $args
	 */
	public function postRemove(LifecycleEventArgs $args)
	{
		$annotation = $this->getFileUploadAnnotation($args);

		if (! $annotation) { return; }

		$this->handler->postRemove($args, $annotation);
	}

	/**
	 *
	 * @param object $entity
	 * @return FileUpload|NULL
	 */
	private function getFileUploadAnnotation(LifecycleEventArgs $args)
	{
		$entity = $args->getEntity();

		$reflectionClass = new \ReflectionClass($entity);

		return $this->reader->getClassAnnotation($reflectionClass, self::ANNOTATION);
	}
}