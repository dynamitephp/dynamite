<?php
declare(strict_types=1);

namespace Dynamite\Configuration;


use Doctrine\Common\Annotations\Annotation\NamedArgumentConstructor;
use Doctrine\Common\Annotations\Annotation\Required;

/**
 * @Annotation
 * @NamedArgumentConstructor()
 * @Target({"CLASS"})
 *
 * @author pizzaminded <mikolajczajkowsky@gmail.com>
 * @license MIT
 */
#[\Attribute(flags: \Attribute::TARGET_CLASS)]
class Item
{
    /**
     * @Required
     * @var string
     */
    public string $objectType;

    /**
     * @var string
     */
    public ?string $repositoryClass;

    public function __construct(
        string $objectType,
        ?string $repositoryClass = null
    )
    {
        $this->objectType = $objectType;
        $this->repositoryClass = $repositoryClass;
    }

    /**
     * @return string
     */
    public function getObjectType(): string
    {
        return $this->objectType;
    }

    /**
     * @return string|null
     */
    public function getRepositoryClass(): ?string
    {
        return $this->repositoryClass;
    }

}
