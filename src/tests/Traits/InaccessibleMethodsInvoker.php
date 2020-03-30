<?php

namespace Elgndy\FileUploader\Tests\Traits;

use ReflectionClass;

trait InaccessibleMethodsInvoker
{
    protected function invokeMethod($obj, string $methodName, array $parameters = [])
    {
        $class = new ReflectionClass(get_class($obj));
        $method = $class->getMethod($methodName);
        $method->setAccessible(true);

        return $method->invokeArgs($obj, $parameters);
    }
}
