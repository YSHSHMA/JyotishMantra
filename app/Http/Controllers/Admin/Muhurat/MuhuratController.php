<?php 
namespace App\Http\Controllers\Admin\Muhurat;

use App\Http\Controllers\Controller;
use App\Models\Muhurat;
use Illuminate\Http\Request;
use Carbon\Carbon;

class MuhuratController extends Controller
{

    public function getList()
    {
        $currentYear = Carbon::now()->year;
        $getMuhurat = Muhurat::where('year', '>=', $currentYear)->orderBy('id', 'asc')->get();
        return view('admin-views.muhurat.list', compact('getMuhurat'));
    }
    // Add the Muhurat Add the Code
    // public function muhurat_store(Request $request)
    // {
        
    //     $request->validate([
    //         'year' => 'required|string|max:255',
    //         'type' => 'required|string|max:255',
    //         'titleLink' => 'required|date',
    //         'muhurat'   => 'string|max:255',
    //     ]);
    //     if ($request->hasFile('image')) {
    //         $filename = time() . '_' . $request->file('image')->getClientOriginalName();
    //         $request->file('image')->move(public_path('muhurat-images'), $filename);
    //         $imagePath = 'muhurat-images/' . $filename;
    //     }
        
    //     $muhurat = Muhurat::create([
    //         'year' => $request->year,
    //         'type' => $request->type,
    //         'titleLink' => $request->titleLink,
    //         'muhurat' => $request->muhurat,
    //         'nakshatra' => $request->nakshatra,
    //         'tithi' => $request->tithi,
    //         'image'     => $imagePath ?? '',
    //         'added_by' => auth('admin')->user()->role->name ?? '', 
    //     ]);
    //     $path = public_path('muhurat-json/muhurats.json');
    //     if (!\File::exists($path)) {
    //         \File::put($path, json_encode([], JSON_PRETTY_PRINT));
    //     }
    //     $data = json_decode(\File::get($path), true);

    //     if (json_last_error() !== JSON_ERROR_NONE || !is_array($data)) {
    //         $data = [];
    //     }
    //     $newEntry = [
    //         'year'      => $request->year,
    //         'type'      => $request->type,
    //         'titleLink' => $request->titleLink,
    //         'image'     => $imagePath ?? '',
    //         'details'   => "Muhurat: {$request->muhurat}; Nakshatra: {$request->nakshatra}; Tithi: {$request->tithi}",
    //     ];

    //     $data[] = $newEntry;
    //     \File::put($path, json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

    //     return response()->json([
    //     'success' => true,
    //     'message' => 'DB & JSON file Add successfully!'
    // ]);
    // }
    public function muhurat_store(Request $request)
    {
        $request->validate([
            'year' => 'required|string|max:255',
            'type' => 'required|string|max:255',
            'titleLink' => 'required|date',
            'muhurat' => 'nullable|string|max:255',
        ]);

        // Handle image upload
        $imagePath = '';
        if ($request->hasFile('image')) {
            $filename = time() . '_' . $request->file('image')->getClientOriginalName();
            $request->file('image')->move(public_path('muhurat-images'), $filename);
            $imagePath = 'muhurat-images/' . $filename;
        }

        // Save in Database
        $muhurat = Muhurat::create([
            'year' => $request->year,
            'type' => $request->type,
            'titleLink' => $request->titleLink,
            'muhurat' => $request->muhurat,
            'nakshatra' => $request->nakshatra,
            'tithi' => $request->tithi,
            'image' => $imagePath,
            'added_by' => auth('admin')->user()->role->name ?? '',
        ]);

        // JSON file path
        $path = public_path('muhurat-json/muhurats.json');

        // Ensure folder exists
        if (!\File::exists(dirname($path))) {
            \File::makeDirectory(dirname($path), 0755, true, true);
        }

        // Read existing data safely
        $data = [];
        if (\File::exists($path)) {
            $content = \File::get($path);
            $decoded = json_decode($content, true);

            if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                $data = $decoded;
            }
        }

        // New entry
        $newEntry = [
            'year'      => $request->year,
            'type'      => $request->type,
            'titleLink' => $request->titleLink,
            'image'     => $imagePath,
            'details'   => "Muhurat: {$request->muhurat}; Nakshatra: {$request->nakshatra}; Tithi: {$request->tithi}",
        ];

        // Append new record without removing old ones
        $data[] = $newEntry;

        // Save updated JSON
        \File::put($path, json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

        return response()->json([
            'success' => true,
            'message' => 'DB & JSON file updated successfully (old records preserved)!'
        ]);
    }
    //update the Muhurat Update the Code
    // public function muhurat_update(Request $request, $id)
    // {
    //     $muhurat = Muhurat::findOrFail($id);

    //     $imagePath = $muhurat->image; // default old image

    //     // Handle image upload
    //     if ($request->hasFile('image')) {
    //         $filename = time() . '_' . $request->file('image')->getClientOriginalName();
    //         $request->file('image')->move(public_path('muhurat-images'), $filename);
    //         $imagePath = 'muhurat-images/' . $filename; // relative path
    //     }

    //     // Update DB
    //     $muhurat->titleLink = $request->muhuratdate;
    //     $muhurat->muhurat   = $request->muhurattime;
    //     $muhurat->nakshatra = $request->nakshatra ?? '';
    //     $muhurat->tithi     = $request->tithi ?? '';
    //     $muhurat->image     = $imagePath;
    //     $muhurat->added_by  = auth('admin')->user()->role->name ?? '';
    //     $muhurat->save();

    //     // ==== Update JSON file ====
    //     $path = public_path('muhurat-json/muhurats.json');

    //     if (!\File::exists($path)) {
    //         \File::put($path, json_encode([], JSON_PRETTY_PRINT));
    //     }

    //     $data = json_decode(\File::get($path), true);
    //     if (json_last_error() !== JSON_ERROR_NONE || !is_array($data)) {
    //         $data = [];
    //     }

    //     // Prepare updated entry in same format as add
    //     $updatedEntry = [
    //         'year'      => $muhurat->year,
    //         'type'      => $muhurat->type,
    //         'titleLink' => $muhurat->titleLink,
    //         'details'   => "Muhurat: {$muhurat->muhurat}; Nakshatra: {$muhurat->nakshatra}; Tithi: {$muhurat->tithi}",
    //         'image'     => $imagePath,
    //         'added_by'  => $muhurat->added_by,
    //     ];

    //     // Find & replace by ID (or titleLink if no id in JSON)
    //     $found = false;
    //     foreach ($data as $index => $item) {
    //         if (isset($item['titleLink']) && $item['titleLink'] == $muhurat->titleLink) {
    //             $data[$index] = $updatedEntry;
    //             $found = true;
    //             break;
    //         }
    //     }

    //     if (!$found) {
    //         $data[] = $updatedEntry;
    //     }

    //     \File::put($path, json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

    //     return response()->json([
    //         'success' => true,
    //         'message' => 'DB & JSON file updated successfully!'
    //     ]);
    // }
    public function muhurat_update(Request $request, $id)
    {
        $muhurat = Muhurat::findOrFail($id);
    
        // === Handle image ===
        $imagePath = $muhurat->image; // keep old image by default
        if ($request->hasFile('image')) {
            $filename = time() . '_' . $request->file('image')->getClientOriginalName();
            $request->file('image')->move(public_path('muhurat-images'), $filename);
            $imagePath = 'muhurat-images/' . $filename;
        }
    
        // === Update DB fields ===
        $muhurat->year      = $request->year ?? $muhurat->year;
        $muhurat->type      = $request->type ?? $muhurat->type;
        $muhurat->titleLink = $request->muhuratdate ?? $muhurat->titleLink;
        $muhurat->muhurat   = $request->muhurattime ?? $muhurat->muhurat;
        $muhurat->nakshatra = $request->nakshatra ?? '';
        $muhurat->tithi     = $request->tithi ?? '';
        $muhurat->image     = $imagePath;
        $muhurat->added_by  = auth('admin')->user()->role->name ?? '';
        $muhurat->save();
    
        // === JSON File Path ===
        $path = public_path('muhurat-json/muhurats.json');
    
        // Ensure folder exists
        if (!\File::exists(dirname($path))) {
            \File::makeDirectory(dirname($path), 0755, true, true);
        }
    
        // === Read existing JSON safely ===
        $data = [];
        if (\File::exists($path)) {
            $content = \File::get($path);
            $decoded = json_decode($content, true);
            if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                $data = $decoded;
            }
        }
    
        // === Prepare updated entry ===
        $updatedEntry = [
            'year'      => $muhurat->year,
            'type'      => $muhurat->type,
            'titleLink' => $muhurat->titleLink,
            'details'   => "Muhurat: {$muhurat->muhurat}; Nakshatra: {$muhurat->nakshatra}; Tithi: {$muhurat->tithi}",
            'image'     => $imagePath,
            'added_by'  => $muhurat->added_by,
        ];
    
        // === Find & Update (by DB ID for reliability) ===
        $found = false;
        foreach ($data as $index => $item) {
            // Match by unique key (ID or titleLink)
            if (
                (isset($item['id']) && $item['id'] == $muhurat->id) ||
                (isset($item['titleLink']) && $item['titleLink'] == $muhurat->getOriginal('titleLink'))
            ) {
                $data[$index] = array_merge($item, $updatedEntry);
                $found = true;
                break;
            }
        }
    
        // === If not found, append new entry ===
        if (!$found) {
            // Add 'id' field for future updates
            $updatedEntry['id'] = $muhurat->id;
            $data[] = $updatedEntry;
        }
    
        // === Save updated JSON ===
        \File::put($path, json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
    
        return response()->json([
            'success' => true,
            'message' => 'DB & JSON file updated successfully (old data preserved)!',
        ]);
    }
   


}