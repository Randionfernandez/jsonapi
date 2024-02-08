<?php

namespace App\JsonApi\Traits;

use Illuminate\Http\Request;

trait JsonApiResource
{
    abstract public function toJsonApi():array;

    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'type' => $this->getResourceType(), // o también $this->resource->getResourceType(),
            'id' => (string)$this->resource->getRouteKey(),
            'attributes' => $this->filterAttributes($this->toJsonApi()),
            'links' => [
                'self' => route('api.v1.' . $this->getResourceType() . '.show', $this->resource)
            ],
        ];
    }

    public function withResponse($request, $response): \Illuminate\Http\JsonResponse
    {
        return $response->header('Location',
            route('api.v1.' . $this->getResourceType() . '.show', $this->resource));
    }

    public function filterAttributes(array $attributes)
    {
        return array_filter($attributes, function ($value) {
            if (request()->isNotFilled('fields')) {
                return true;
            }

            $fields = explode(',', request('fields.' . $this->getResourceType()));
            if ($value === $this->getRouteKey()) {
                return in_array($this->getRouteKeyName(), $fields);
            }
            return $value;
        });
    }
}
