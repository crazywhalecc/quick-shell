<?php

namespace QuickShell\Annotations;

use Attribute;
use Doctrine\Common\Annotations\Annotation\NamedArgumentConstructor;
use ZM\Annotation\AnnotationBase;
use ZM\Annotation\Interfaces\CustomAnnotation;

/**
 * @Annotation
 * @Target("METHOD")
 * @NamedArgumentConstructor()
 */
#[Attribute(Attribute::IS_REPEATABLE | Attribute::TARGET_METHOD)]
class CommandArgument extends AnnotationBase implements CustomAnnotation
{
    /**
     * @var string
     * @Required()
     */
    public string $argument_name;

    /**
     * @var string
     */
    public string $description = '';

    /**
     * @var bool
     */
    public bool $one_argument = false;

    /**
     * @var bool
     */
    public bool $allow_empty = false;

    public function __construct(string $argument_name, string $description = '', bool $one_argument = false, bool $allow_empty = false)
    {
        $this->argument_name = $argument_name;
        $this->description = $description;
        $this->one_argument = $one_argument;
        $this->allow_empty = $allow_empty;
    }
}