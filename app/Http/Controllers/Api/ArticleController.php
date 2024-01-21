<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\SaveArticleRequest;
use App\Http\Resources\ArticleCollection;
use App\Http\Resources\ArticleResource;
use App\Models\Article;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Str;

class ArticleController extends Controller
{
    public function index(Request $request): ArticleCollection
    {
        $sortFields = explode(',', $request->input('sort'));
        $articles = Article::query();
        
        if ($request->filled('sort')) {
            $allowedSorts = ['title', 'content'];
            foreach ($sortFields as $sortField) {
                $sortDirection = Str::of($sortField)->startsWith('-') ? 'desc' : 'asc';
                $sortField = ltrim($sortField, '-');

// si el primer parámetro retorna true, devuelve un estado 400
                abort_unless(in_array($sortField, $allowedSorts), 400);
//            Alternativamente a abort_unless() podemos obtener lo mismo con
//                if (!in_array($sortField, $allowedSorts)) {
//                    abort(400);    // este helper lanza una HttpException con estado 400
//                }
                $articles->orderBy($sortField, $sortDirection);
            }
        }

        return ArticleCollection::make($articles->get());
    }

    public function store(SaveArticleRequest $request)
    {
        $article = Article::create($request->validated('data.attributes'));
//        $article = Article::create($request->validated()['data']['attributes']); // notación antigua
        return ArticleResource::make($article);
    }

    public function update(Article $article, SaveArticleRequest $request,)
    {
        $article->update($request->validated('data.attributes'));
        return ArticleResource::make($article);
    }

    public function show(Article $article): ArticleResource
    {
        return ArticleResource::make($article);
    }

    public function destroy(Article $article): Response
    {
        $article->Delete();
        return response()->NoContent();
    }
}
