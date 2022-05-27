<?php /** @noinspection PhpLanguageLevelInspection */

namespace QuickShell\Annotations;

use Attribute;
use Doctrine\Common\Annotations\Annotation\NamedArgumentConstructor;
use Doctrine\Common\Annotations\Annotation\Target;
use ZM\Annotation\AnnotationBase;
use ZM\Annotation\Interfaces\CustomAnnotation;

/**
 * @Annotation
 * @Target("METHOD")
 * @NamedArgumentConstructor()
 */
#[Attribute(Attribute::TARGET_METHOD | Attribute::IS_REPEATABLE)]
class CommandOption extends AnnotationBase implements CustomAnnotation
{
    /**
     * @var string
     * @Required()
     */
    public string $option_name;

    /**
     * @var string
     */
    public string $description = '';

    /**
     * @var bool
     */
    public bool $required = false;

    public function __construct(string $option_name, string $description = '', bool $required = false)
    {
        $this->option_name = $option_name;
        $this->description = $description;
        $this->required = $required;
    }
}