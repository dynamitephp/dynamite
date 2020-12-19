<?php
declare(strict_types=1);

namespace Dynamite\Configuration;


use Doctrine\Common\Annotations\Annotation\Required;

/**
 * @Annotation
 * @Target({"CLASS"})
 *
 * @author pizzaminded <mikolajczajkowsky@gmail.com>
 * @license MIT
 */
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
