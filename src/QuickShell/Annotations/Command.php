<?php /** @noinspection PhpLanguageLevelInspection */

namespace QuickShell\Annotations;

use Attribute;
use Doctrine\Common\Annotations\Annotation\NamedArgumentConstructor;
use Doctrine\Common\Annotations\Annotation\Required;
use Doctrine\Common\Annotations\Annotation\Target;
use ZM\Annotation\AnnotationBase;
use ZM\Annotation\Interfaces\CustomAnnotation;
use ZM\Annotation\Interfaces\Level;

/**
 * @Annotation
 * @Target("ALL")
 * @NamedArgumentConstructor()
 */
#[Attribute(Attribute::TARGET_ALL | Attribute::IS_REPEATABLE)]
class Command extends AnnotationBase implements CustomAnnotation
{
    /**
     * @var string
     * @Required()
     */
    public string $name;

    /**
     * @var string
     */
    public string $description = '';

    /**
     * @var string
     */
    public string $alias = '';

    public function __construct(string $name, string $description = '', string $alias = '')
    {
        $this->name = $name;
        $this->description = $description;
        $this->alias = $alias;
    }
}