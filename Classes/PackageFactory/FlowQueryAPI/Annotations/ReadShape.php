<?php
namespace PackageFactory\FlowQueryAPI\Annotations;

/**
 * @Annotation
 * @Target("CLASS")
 */
final class ReadShape
{
    /**
     * @var string
     */
    protected $alias;

    /**
     * @var string
     */
    protected $type;

    /**
     * @param array $values
     * @throws \InvalidArgumentException
     */
    public function __construct(array $values)
    {
        if (!isset($values['alias'])) {
            throw new \InvalidArgumentException('ReadShape must specify an alias', 1460300360);
        }

        if (!isset($values['type'])) {
            throw new \InvalidArgumentException('ReadShape must specify a type', 1460300378);
        }

        $this->alias = $values['alias'];
        $this->type = $values['type'];
    }

    /**
     * Get the alias
     *
     * @return string
     */
    public function getAlias()
    {
        return $this->alias;
    }

    /**
     * Get the type
     *
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }
}
