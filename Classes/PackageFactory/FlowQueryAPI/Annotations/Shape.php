<?php
namespace PackageFactory\FlowQueryAPI\Annotations;

/**
 * @Annotation
 * @Target("CLASS")
 */
final class Shape
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
            throw new \InvalidArgumentException('Shape must specify an alias', 1460300360);
        }

        $this->alias = $values['alias'];
        $this->type = isset($values['type']) ? $values['type'] : null;
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
