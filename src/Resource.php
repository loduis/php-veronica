<?php

namespace Veronica;

use Illuminate\Api\Http\Restable;
use Illuminate\Api\Http\Resource as ApiResource;

/**
 * Base resource
 */
abstract class Resource extends ApiResource
{
    protected static $filterWith = Support\Filter::class;

    use Restable;

    public static function first()
    {
        return static::all(['page' => 0, 'size' => 2])->first();
    }


    /**
     * Save the current resource.
     *
     * @return $this
     */
    public function save()
    {
        return $this->store($this->id === null ? 'POST' : 'PUT', $this->id);
    }

    protected function postTo($path, $params)
    {
        return $this->store('POST', $this->id . '/' . $path, $this);
    }
}

