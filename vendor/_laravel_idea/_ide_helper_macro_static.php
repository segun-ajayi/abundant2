<?php
/** @noinspection PhpUndefinedClassInspection */
/** @noinspection PhpFullyQualifiedNameUsageInspection */

namespace Illuminate\Contracts\View {

    /**
     * @method static $this layout($view, $params = [])
     * @method static $this extends($view, $params = [])
     * @method static $this section($section)
     * @method static $this slot($slot)
     */
    class View {}
}

namespace Illuminate\Database\Eloquent {

    /**
     * @method static $this customWithAggregate($relations, $aggregate, $column, $alias = null)
     * @method static Builder hasAggregate($relation, $column, $aggregate, $operator = '>=', $count = 1)
     */
    class Builder {}
}

namespace Illuminate\Database\Eloquent\Relations {

    use Illuminate\Database\Eloquent\Builder;

    /**
     * @method static Builder getRelationExistenceAggregatesQuery(Builder $query, Builder $parentQuery, $aggregate, $column)
     * @method static string getRelationCountHashWithoutIncrementing()
     */
    class Relation {}
}

namespace Illuminate\Database\Query {

    /**
     * @method static $this|false|\Illuminate\Database\Query\Builder|mixed leftJoinIfNotJoined(...$params)
     * @method static $this|false|\Illuminate\Database\Query\Builder|mixed groupIfNotGrouped(...$params)
     */
    class Builder {}
}

namespace Illuminate\Http {

    /**
     * @method static RedirectResponse banner($message)
     * @method static RedirectResponse dangerBanner($message)
     */
    class RedirectResponse {}

    /**
     * @method static array validate(array $rules, ...$params)
     * @method static array validateWithBag(string $errorBag, array $rules, ...$params)
     * @method static bool hasValidSignature($absolute = true)
     * @method static bool hasValidRelativeSignature()
     */
    class Request {}
}

namespace Illuminate\Support {

    /**
     * @method static void downloadExcel(string $fileName, string $writerType = null, $withHeadings = false)
     * @method static void storeExcel(string $filePath, string $disk = null, string $writerType = null, $withHeadings = false)
     */
    class Collection {}
}

namespace Illuminate\Testing {

    /**
     * @method static $this assertSeeLivewire($component)
     * @method static $this assertDontSeeLivewire($component)
     */
    class TestResponse {}
}

namespace Illuminate\View {

    use Livewire\WireDirective;

    /**
     * @method static WireDirective wire($name)
     */
    class ComponentAttributeBag {}

    /**
     * @method static $this layout($view, $params = [])
     * @method static $this extends($view, $params = [])
     * @method static $this section($section)
     * @method static $this slot($slot)
     */
    class View {}
}
