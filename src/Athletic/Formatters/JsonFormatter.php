<?php
namespace Athletic\Formatters;

use Athletic\Results\ClassResults;
use Athletic\Results\MethodResults;

/**
 * This formatter outputs test results in JSON form.
 *
 * @version 0.1.0
 * @since   0.1.9
 * @package Athletic\Formatters
 * @author  Fike Etki <etki@etki.name>
 */
class JsonFormatter implements FormatterInterface
{
    /**
     * List of stats MethodResults has.
     *
     * @type string[]
     * @since 0.1.0
     */
    protected $statKeys = array(
        'iterations',
        'sum',
        'min',
        'max',
        'avg',
        'ops',
        'group',
    );
    /**
     * This constant may be used to force formatter to show all results in a
     * plain list under tested class name key. This is intended to be default
     * behavior.
     *
     * @type int
     * @since 0.1.0
     */
    const STRATEGY_LIST_ALL = 1;
    /**
     * This constant may be used to force formatter to show only results for
     * methods that belong to group.
     *
     * @type int
     * @since 0.1.0
     */
    const STRATEGY_SHOW_GROUPED = 2;
    /**
     * This constant may be used to force formatter to show only results that
     * don't belong to any group.
     *
     * @type int
     * @since 0.1.0
     */
    const STRATEGY_SHOW_NONGROUPED = 3;
    /**
     * This constant may be used to force formatter to show both grouped and
     * non-grouped results under `groups` and `methods` keys correspondingly.
     *
     * @type int
     * @since 0.1.0
     */
    const STRATEGY_MIX_VIEWS = 4;
    /**
     * Prints results in JSON format.
     *
     * @param ClassResults[] $results         List of testing results.
     * @param int            $displayStrategy Which strategy to use to display
     *                                        results (defaults to listing all
     *                                        results). Use `self::STRATEGY_*`
     *                                        constants to set it.
     * @param bool           $prettyPrint     Whether to return data in
     *                                        human-readable format or just as
     *                                        single string.
     *
     * @return string JSON-encoded data.
     * @since 0.1.0
     */
    public function getFormattedResults(
        $results,
        $displayStrategy = self::STRATEGY_LIST_ALL,
        $prettyPrint = false
    ) {
        $data = $this->sortResults($results, $displayStrategy);
        $options = JSON_FORCE_OBJECT;
        if ($prettyPrint) {
            $options |= JSON_PRETTY_PRINT;
        }
        return json_encode($data, $options) . PHP_EOL;
    }

    /**
     * Reformats results from original Athletic result data to encode-ready
     * nested array.
     *
     * @param ClassResults[] $results         Athletic internal output.
     * @param int            $displayStrategy Strategy to force particular
     *                                        kind of output (groups /
     *                                        nongrouped methods / both / both
     *                                        as plain list). Use
     *                                        `self::STRATEGY_*` constants to
     *                                        set this value.
     *
     * @return array List of Athletic results as a nested array, filtered
     *               according to provided strategy.
     * @since 0.1.0
     */
    protected function sortResults(
        array $results,
        $displayStrategy = self::STRATEGY_LIST_ALL
    ) {
        $classes = array();
        foreach ($results as $classResult) {
            $className = $classResult->getClassName();
            $results = $this->getClassResults($classResult);
            $results = $this->filterClassResults($results, $displayStrategy);
            $classes[$className] = $results;
        }
        return $classes;
    }

    /**
     * Formats class results as a single array of method results (formatted as
     * arrays too).
     *
     * @param ClassResults $classResults Class results.
     *
     * @return array Set of method results in [method => [results]] format.
     * @since 0.1.0
     */
    protected function getClassResults(ClassResults $classResults) {
        $stats = array();
        /** @type MethodResults $result */
        foreach ($classResults as $result) {
            $data = array();
            foreach ($this->statKeys as $key) {
                $data[$key] = $result->$key;
            }
            $stats[$result->methodName] = $data;
        }
        return $stats;
    }

    /**
     * Filters or recombines class results according to provided strategy.
     *
     * @param array $results  Array of raw class results.
     * @param int   $strategy One of `self::STRATEGY_*` constant values.
     *
     * @return array List of class results.
     * @since 0.1.0
     */
    protected function filterClassResults(
        array $results,
        $strategy
    ) {
        if ($strategy === self::STRATEGY_LIST_ALL) {
            return $results;
        }
        $groups = array();
        $methods = array();
        foreach ($results as $method => $result) {
            $groupName = $result['group'];
            unset($result['group']);
            if ($groupName) {
                if (!isset($groups[$groupName])) {
                    $groups[$groupName] = array();
                }
                $groups[$groupName][$method] = $result;
            } else {
                $methods[$method] = $result;
            }
        }
        switch ($strategy) {
            case self::STRATEGY_SHOW_GROUPED:
                return $groups;
            case self::STRATEGY_SHOW_NONGROUPED:
                return $methods;
            case self::STRATEGY_MIX_VIEWS:
                return array('methods' => $methods, 'groups' => $groups);
            default:
                throw new \InvalidArgumentException(
                    'Unknown results display strategy'
                );
        }
    }
}
