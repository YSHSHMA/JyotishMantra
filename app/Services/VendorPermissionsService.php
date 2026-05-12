<?php

namespace App\Services;

use Illuminate\Support\Str;
use App\Traits\FileManagerTrait;

class VendorPermissionsService
{


    use FileManagerTrait;


    public function getAddData(object $request): array
    {
        return $this->extractPermissions($request['permissions'],$request['module_name']);
    }

    function extractPermissions($permissions,$type, $moduleName = null): array
    {
        $result = [];

        foreach ($permissions as $item) {
            $module = $item['name'] ?? $moduleName;

            if (isset($item['children'])) {
                foreach ($item['children'] as $child) {
                    $subModule = $child['name'] ?? null;
                    $subPermissions = [];

                    if (isset($child['subchildren'])) {
                        foreach ($child['subchildren'] as $subchild) {
                            $subPermissions[] = $subchild['name'];
                        }
                    }

                    $result[] = [
                        'type'      => $type,
                        'module'      => $module,
                        'sub_module'  => $subModule,
                        'permission'  => json_encode($subPermissions),
                    ];
                }
            } else {
                $result[] = [
                    'type'      => $type,
                    'module'      => $module,
                    'sub_module'  => null,
                    'permission'  => [],
                ];
            }
        }

        return $result;
    }
}
