<?php

namespace App\Services;

use App\Traits\FileManagerTrait;

class BirthJournalService
{
    use FileManagerTrait;

    public function getAddData(object $request): array
    {
        return [
            'short_description' => $request['short_description'][array_search('en', $request['lang'])],
            'description' => $request['description'][array_search('en', $request['lang'])],
            'selling_price' => $request['selling_price'],
            'pages' => $request['pages'],
            'name' => $request['name'],
            'type' => $request['type'],
            'image' => $this->upload('birthjournal/image/', 'webp', $request->file('image')),
            'status' => 1,
        ];
    }

    public function getUpdateData(object $request, object $data): array
    {
        $image = $request->file('image') ? $this->update('birthjournal/image/', $data['image'], 'webp', $request->file('image')) : $data['image'];
        return [
            'short_description' => $request['short_description'][array_search('en', $request['lang'])],
            'description' => $request['description'][array_search('en', $request['lang'])],
            'selling_price' => $request['selling_price'],
            'pages' => $request['pages'],
            'type' => $request['type'],
            'name' => $request['name'],
            'image' => $image,
        ];
    }

    public function removeImage(object $data): bool
    {
        if ($data['image']) {
            $this->delete('birthjournal/image/' . $data['image']);
        };
        return true;
    }

    public function uploadKundliMilan(object $request, object $data): array
    {
        if ($request->hasFile('kundli_milan_pdf')) {
            $this->removeOldPDF('app/public/birthjournal/kundali_milan/', $data['kundali_pdf']);
            $kundali_pdf = $this->uploadPDF('app/public/birthjournal/kundali_milan/', $request->file('kundli_milan_pdf'));
        } else {
            $kundali_pdf = $data['kundali_pdf'];
        }
        return [
            'kundali_pdf' => $kundali_pdf,
            'milan_verify'=>0,
        ];
    }

    protected function uploadPDF($path, $pdfFile)
    {
        $fileName = 'kundli_milan_' . time() . '.' . $pdfFile->getClientOriginalExtension();
        $destinationPath = storage_path($path);
        $pdfFile->move($destinationPath, $fileName);
        return $fileName;
    }

    protected function removeOldPDF($path, $fileName)
    {
        $filePath = storage_path($path . $fileName);
        if (file_exists($filePath)) {
            @unlink($filePath);
        }
    }
}
