<?php
namespace DMKProjectM\Bundle\TaskBundle\Provider;

use Oro\Bundle\TranslationBundle\Translation\Translator;

class TaskTreeProvider
{
    /** @var array */
    protected $processedJsTrees = [];

    /** @var Translator */
    protected $translator;

    /**
     * @param array $nodes
     * @param int $correctMenuLevel
     * @param int $level
     * @param string $parentName
     * @param array $groupedData
     * @return array
     */
    protected function buildJsTree($nodes, $correctMenuLevel, $level = 0, $parentName = '#', array $groupedData = [])
    {
        //$nodes = $this->sortChildrenByPriority($nodes);

        $level++;
        foreach ($nodes as $name => $node) {
            if (is_array($node) && isset($node['children']) && $correctMenuLevel > $level) {
                $groupedData = $this->buildJsTree(
                    $node['children'],
                    $correctMenuLevel,
                    $level,
                    $name,
                    $groupedData
                );

                $groupedData[] = $this->buildJsTreeItem($name, $parentName, $node);
            } else {
                $groupedData[] = $this->buildJsTreeItemWithSearchData($name, $parentName, $node);
            }
        }

        return $groupedData;
    }

    /**
     * @param string $name
     * @param string $parentName
     * @param array $node
     * @return array
     */
    public function buildJsTreeItem($name, $parentName, array $node)
    {
        /*$group = $this->configBag->getGroupsNode($name);

        if ($group === false) {
            throw new ItemNotFoundException(sprintf('Group "%s" is not defined.', $name));
        }*/
        $text = isset($group['title']) ? $this->translator->trans($group['title']) : null;

        return [
            'id' => $name,
            'text' => $text,
            'icon' => $group['icon'] ?? null,
            'parent' => $parentName,
            'priority' => $node['priority'] ?? 0,
            'search_by' => [$text]
        ];
    }
}
