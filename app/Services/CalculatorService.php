<?php

namespace App\Services;
use Illuminate\Support\Str;

use App\Traits\FileManagerTrait;

class CalculatorService
{
    use FileManagerTrait;

    public function getSlug(object $request): string
    {
        return Str::slug($request['name'][array_search('en', $request['lang'])]);
    }

    public function getAddData(object $request): array
    {
        return [
            'name' => $request['name'][array_search('en', $request['lang'])],
            'slug' => $this->getSlug($request),
            'url' => $request['url'],
            'description' => $request['description'][array_search('en', $request['lang'])],
            'logo' => $this->upload('calculator-img/', 'webp', $request->file('logo')),
            'detail_image' => $this->upload('calculator-img/', 'webp', $request->file('detail_image')),
        ];   
    }

    public function getUpdateData(object $request, object $data): array
    {
        $logo = $request->file('logo') ? $this->update('calculator-img/', $data['logo'],'webp', $request->file('logo')) : $data['logo'];
        $detail_image = $request->file('detail_image') ? $this->update('calculator-img/', $data['detail_image'],'webp', $request->file('detail_image')) : $data['detail_image'];
        return [
            'name' => $request['name'][array_search('en', $request['lang'])],
            'url' => $request['url'],
            'description' => $request['description'][array_search('en', $request['lang'])],
            'logo' => $logo,
            'detail_image' => $detail_image
        ];
    }

    public function deleteImage(object $data): bool
    {
        if ($data['image']) {$this->delete('calculator-img/'.$data['image']);};
        return true;
    }

}
