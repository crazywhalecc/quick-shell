<?php

namespace QuickShell\Annotations;

use Attribute;
use Doctrine\Common\Annotations\Annotation\NamedArgumentConstructor;
use Doctrine\Common\Annotations\Annotation\Required;
use Doctrine\Common\Annotations\Annotation\Target;
use ZM\Annotation\AnnotationBase;
use ZM\Annotation\Interfaces\CustomAnnotation;
use ZM\Annotation\Interfaces\ErgodicAnnotation;

/**
 * @Annotation
 * @Target("CLASS")
 * @NamedArgumentConstructor()
 */
#[Attribute(Attribute::IS_REPEATABLE | Attribute::TARGET_CLASS)]
class CommandCategory extends AnnotationBase implements CustomAnnotation, ErgodicAnnotation
{
    /**
     * @var string
     * @Required()
     */
    public string $category;

    public string $description = '';

    public function __construct(string $category, string $description = '')
    {
        $this->category = $category;
        $this->description = $description;
    }
}