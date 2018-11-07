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
// Date:     07/11/2018
// Project:  RouterAnnotationResolver
//
declare(strict_types=1);
namespace CodeInc\RouterAnnotationResolver\Exception;
use CodeInc\Router\Exceptions\RouterException;
use Throwable;


/**
 * Class MissingRoutableAnnotationException
 *
 * @package CodeInc\RouterAnnotationResolver\Exception
 * @author Joan Fabrégat <joan@codeinc.fr>
 */
class MissingRoutableAnnotationException extends \RuntimeException implements RouterException
{
    /**
     * @var string
     */
    private $controllerClass;

    /**
     * MissingRoutableAnnotationException constructor.
     *
     * @param string $controllerClass
     * @param int $code
     * @param Throwable|null $previous
     */
    public function __construct(string $controllerClass, int $code = 0, Throwable $previous = null)
    {
        $this->controllerClass = $controllerClass;
        parent::__construct(
            sprintf("The controller '%s' does not have a @Routable annotation.", $controllerClass),
            $code,
            $previous
        );
    }

    /**
     * @return string
     */
    public function getControllerClass():string
    {
        return $this->controllerClass;
    }
}