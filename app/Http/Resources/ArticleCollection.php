<?php
namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

class ArticleCollection extends ResourceCollection
{
//    public $collects= ArticleResource::class;   // usar solo si el Resource no sigue la convención de Laravel
    /**
     * Transform the resource collection into an array.
     *
     * @return array<int|string, mixed>
     */
    public function toArray(Request $request): array
    {
//        return parent::toArray($request);
        return [
            'data' =>  $this->collection,    // genera todos los artículos en formato definido (json:api)
            'links' => [
                'self' => route('api.v1.articles.index')
            ]
        ];
    }
}
