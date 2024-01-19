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
//        $articles = Article::all();
        $sortField = $request->input('sort');
        $sortDirection = Str::of($sortField)->startsWith('-') ? 'desc' : 'asc';
        $sortField = ltrim($sortField, '-');

        $url = route('api.v1.articles.index', ['sort' => $sortField]);

        $articles = Article::orderBy($sortField, $sortDirection)->get();
        return ArticleCollection::make($articles);
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
