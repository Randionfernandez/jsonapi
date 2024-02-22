<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\SaveArticleRequest;
use App\Http\Resources\ArticleCollection;
use App\Http\Resources\ArticleResource;
use App\Models\Article;
use Illuminate\Http\Response;

class ArticleController extends Controller
{
    public function index(): ArticleCollection
    {
        if (auth()->user()->tokenCan('bla')) {
            $articles = Article::query()
                ->allowedFilters(['title', 'content', 'year', 'month'])
                ->allowedSorts(['title', 'content'])
                ->sparseFieldset()
                ->jsonPaginate();
            return ArticleCollection::make($articles);
        }
        return  response()->json("error");
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
        // article/the-slug?fields[articles]=title
        $article = Article::where('slug', $article)
            ->sparseFieldset()
            ->firstOrFail();
//dd($article);
        return ArticleResource::make($article);
    }

    public
    function destroy(Article $article): Response
    {
        $article->Delete();
        return response()->NoContent();
    }
}
