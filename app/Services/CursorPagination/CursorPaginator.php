<?php


namespace App\Services\CursorPagination;


use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

class CursorPaginator
{
    /**
     * @param Builder $query
     * @param array $columns
     * @param int $limit
     * @param $cursor
     * @param bool $before
     * @return CursorPaginator
     */
    public static function create(Builder $query, int $limit = 15, array $columns = [], $cursor = [], $before = false) {
        $values = $cursor;

        // Filter all values before or after the given cursor.
        if(count($values) === count($columns)) {
            $apply = function (Builder $query, $columns, $values) use (&$apply, $before) {
                $query->where(function (Builder $query) use ($columns, $values, $apply, $before) {
                    $column = key($columns);
                    $direction = array_shift($columns);
                    $value = array_shift($values);

                    $query->where($column, ($direction === 'asc' xor $before) ? '>' : '<', $value);

                    if (!empty($columns)) {
                        $query->orWhere($column, $value);
                        $apply($query, $columns, $before);
                    }
                });
            };
            $apply($query, $columns, $values);
        }

        // Ensure that the columns are ordered according to this cursor.
        foreach ($columns as $column => $direction) {
            $query->orderBy($column, ($direction === 'asc' xor $before) ? 'asc' : 'desc');
        }

        // Get the items.
        $items = $query->limit($limit + 1)->get();

        // Check if there are next elements.
        $hasMore = $items->count() > $limit;
        if($hasMore) { $items->pop(); }

        // Invert items when `$before` is `true`.
        if($before) { $items = $items->reverse(); }

        // In the case there is no next page.
        return new CursorPaginator($items, $columns, $hasMore, $hasMore); // TODO: Compute hasNext and hasPrev
    }

    protected $items;
    protected $columns;
    protected $hasNext;
    protected $hasPrev;

    protected function __construct(Collection $items, array $columns, bool $hasNext, bool $hasPrev)
    {
        $this->items = $items;
        $this->columns = $columns;
        $this->hasNext = $hasNext;
        $this->hasPrev = $hasPrev;
    }

    public function items() {
        return $this->items;
    }

    /**
     * Gives the cursor of the given item relative to this CursorPagination.
     *
     * @param int|Model $item The index of the item or an real item.
     * @return array
     */
    public function getCursor($item) {
        if(is_int($item)) {
            $item = $this->items->get($item);
        }

        $result = [];
        foreach ($this->columns as $column => $direction) {
            $result[] = $item->getAttribute($column);
        }
        return $result;
    }

    /**
     * Gives the amount of pages.
     *
     * @return int
     */
    public function pageSize() {
        return $this->items->count();
    }

    /**
     * Whether or not there is a next page.
     *
     * @return bool
     */
    public function hasNext() {
        return $this->hasNext;
    }

    /**
     * Whether or not there is a previous page.
     *
     * @return bool
     */
    public function hasPrev() {
        return $this->hasPrev;
    }

    public function firstCursor() {
        return $this->getCursor($this->items->first());
    }

    public function lastCursor() {
        return $this->getCursor($this->items->last());
    }
}
