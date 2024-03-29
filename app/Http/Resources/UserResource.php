<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'profile' => new ProfileResource($this->whenLoaded('profile')),
            'photos' => PhotoResource::collection($this->whenLoaded('photos')),
            'posts' => PostResource::collection($this->whenLoaded('posts')),
            'comment' => CommentResource::collection($this->whenLoaded('comment'))
        ];
    }
}
