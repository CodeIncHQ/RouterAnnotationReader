<?php
//
// +---------------------------------------------------------------------+
// | CODE INC. SOURCE CODE                                               |
// +---------------------------------------------------------------------+
// | Copyright (c) 2018 - Code Inc. SAS - All Rights Reserved.           |
// | Visit https://www.codeinc.fr for more information about licensing.  |
// +---------------------------------------------------------------------+
// | NOTICE:  All information contained herein is, and remains the       |
// | property of Code Inc. SAS. The intellectual and technical concepts  |
// | contained herein are proprietary to Code Inc. SAS are protected by  |
// | trade secret or copyright law. Dissemination of this information or |
// | reproduction of this material is strictly forbidden unless prior    |
// | written permission is obtained from Code Inc. SAS.                  |
// +---------------------------------------------------------------------+
//
// Author:   Joan Fabrégat <joan@codeinc.fr>
// Date:     16/10/2018
// Project:  RouterAnnotationResolver
//
declare(strict_types=1);
namespace CodeInc\RouterAnnotationResolver;
use CodeInc\DirectoryClassesIterator\DirectoryClassesIterator;
use CodeInc\DirectoryClassesIterator\RecursiveDirectoryClassesIterator;
use CodeInc\Router\Resolvers\StaticResolver;
use CodeInc\RouterAnnotationResolver\Exception\MissingRoutableAnnotationException;
use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\Common\Annotations\CachedReader;
use Doctrine\Common\Annotations\Reader;
use Doctrine\Common\Cache\ArrayCache;


/**
 * Class AnnotationResolver
 *
 * @package CodeInc\RouterAnnotationResolver
 * @author Joan Fabrégat <joan@codeinc.fr>
 */
class AnnotationResolver extends StaticResolver
{
    /**
     * @var string
     */
    private $routePrefix;

    /**
     * @var Reader
     */
    private $annotationReader;

    /**
     * AnnotationResolver constructor.
     *
     * @param string $routePrefix
     * @param Reader|null $annotationReader
     * @throws \Doctrine\Common\Annotations\AnnotationException
     */
    public function __construct(string $routePrefix = '', ?Reader $annotationReader = null)
    {
        parent::__construct();
        $this->routePrefix = $routePrefix;
        $this->annotationReader = $annotationReader ?? new CachedReader(new AnnotationReader(), new ArrayCache());
    }

    /**
     * Adds an annotated controller.
     *
     * @param string $controllerClass
     * @throws \ReflectionException
     */
    public function addController(string $controllerClass):void
    {
        /** @var Routable $annotation */
        $annotation = $this->annotationReader->getClassAnnotation(
            new \ReflectionClass($controllerClass),
            Routable::class
        );
        if ($annotation === null) {
            throw new MissingRoutableAnnotationException($controllerClass);
        }
        $this->add($annotation, $controllerClass);
    }

    /**
     * @param Routable $routableAnnotation
     * @param string $controllerClass
     */
    private function add(Routable $routableAnnotation, string $controllerClass):void
    {

        $this->addRoute($this->routePrefix.$routableAnnotation->route, $controllerClass);
        if ($routableAnnotation->altRoutes) {
            foreach ($routableAnnotation->altRoutes as $route) {
                $this->addRoute($this->routePrefix.$route, $controllerClass);
            }
        }
    }

    /**
     * Adds all the handler in a directory having the annotation @Routable.
     *
     * @param string $dirPath
     * @param bool $recursively
     */
    public function addDirectory(string $dirPath, bool $recursively = true):void
    {
        $iterator = $recursively
            ? new RecursiveDirectoryClassesIterator($dirPath)
            : new DirectoryClassesIterator($dirPath);

        foreach ($iterator as $class)
        {
            /** @var Routable $annotation */
            if (($annotation = $this->annotationReader->getClassAnnotation($class, Routable::class)) !== null) {
                $this->add($annotation, $class->getName());
            }
        }
    }
}