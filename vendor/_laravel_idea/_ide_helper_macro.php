<?php
/** @noinspection PhpUndefinedClassInspection */
/** @noinspection PhpFullyQualifiedNameUsageInspection */

namespace Illuminate\Contracts\View {

    /**
     * @method $this layout($view, $params = [])
     * @method $this extends($view, $params = [])
     * @method $this section($section)
     * @method $this slot($slot)
     */
    class View {}
}

namespace Illuminate\Database\Eloquent {

    /**
     * @method $this customWithAggregate($relations, $aggregate, $column, $alias = null)
     * @method Builder hasAggregate($relation, $column, $aggregate, $operator = '>=', $count = 1)
     */
    class Builder {}
}

namespace Illuminate\Database\Eloquent\Relations {

    use Illuminate\Database\Eloquent\Builder;

    /**
     * @method Builder getRelationExistenceAggregatesQuery(Builder $query, Builder $parentQuery, $aggregate, $column)
     * @method string getRelationCountHashWithoutIncrementing()
     */
    class Relation {}
}

namespace Illuminate\Database\Query {

    /**
     * @method $this|false|\Illuminate\Database\Query\Builder|mixed leftJoinIfNotJoined(...$params)
     * @method $this|false|\Illuminate\Database\Query\Builder|mixed groupIfNotGrouped(...$params)
     */
    class Builder {}
}

namespace Illuminate\Http {

    /**
     * @method RedirectResponse banner($message)
     * @method RedirectResponse dangerBanner($message)
     */
    class RedirectResponse {}

    /**
     * @method array validate(array $rules, ...$params)
     * @method array validateWithBag(string $errorBag, array $rules, ...$params)
     * @method bool hasValidSignature($absolute = true)
     * @method bool hasValidRelativeSignature()
     */
    class Request {}
}

namespace Illuminate\Support {

    /**
     * @method void downloadExcel(string $fileName, string $writerType = null, $withHeadings = false)
     * @method void storeExcel(string $filePath, string $disk = null, string $writerType = null, $withHeadings = false)
     */
    class Collection {}
}

namespace Illuminate\Testing {

    /**
     * @method $this assertSeeLivewire($component)
     * @method $this assertDontSeeLivewire($component)
     */
    class TestResponse {}
}

namespace Illuminate\View {

    use Livewire\WireDirective;

    /**
     * @method WireDirective wire($name)
     */
    class ComponentAttributeBag {}

    /**
     * @method $this layout($view, $params = [])
     * @method $this extends($view, $params = [])
     * @method $this section($section)
     * @method $this slot($slot)
     */
    class View {}
}
