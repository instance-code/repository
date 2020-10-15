<?php

namespace Sabirepo\Repository\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Symfony\Component\HttpFoundation\Response as ResponseStatus;
use Illuminate\Support\Arr;

class ResponseResource extends JsonResource
{
    public static $wrap = '';
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        if(is_object($this->resource)) {
            $status = property_exists($this->resource, 'status') ? $this->resource->status : ResponseStatus::HTTP_OK;
            $messages = property_exists($this->resource, 'messages') ?  $this->resource->messages : [];
            $body = property_exists($this->resource, 'body') ?  $this->resource->body : $this->resource;
        } elseif(is_array($this->resource)) {
            $status = Arr::exists($this->resource, 'status') ? $this->resource['status'] : ResponseStatus::HTTP_OK;
            $messages = Arr::exists($this->resource, 'messages') ? $this->resource['messages'] : [];
            $body = Arr::exists($this->resource, 'body') ? $this->resource['body'] : [];
        } else {
            $status = ResponseStatus::HTTP_OK;
            $messages = [];
            $body = $this->resource;
        }

        return [
            'head' => [
                'status' => $status,
                "messages" => $messages
            ],
            'body' => $body,
        ];
    }

    /**
     * Customize the outgoing response for the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Illuminate\Http\Response  $response
     * @return void
     */
    public function withResponse($request, $response)
    {
        foreach (config('repository.response.headers') as $key => $value) {
            $response->header($key, $value);
        }
    }
}
