<?php

namespace App\Http\Resources;

use App\Models\Article;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin Article */
class ArticleResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'published_at' => $this->published_at,
            'url' => $this->url,
            'image_url' => $this->image_url,
            'category' => new CategoryResource($this->whenLoaded('category')),
            'source' => new SourceResource($this->whenLoaded('source')),
        ];
    }
}
