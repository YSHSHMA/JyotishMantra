<?php
namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Http\Controllers\Controller;

class JsonController extends Controller
{

    // Show the form to create a new JSON entry
    public function create()
    {
        return view('admin-views.muhurat-json.add-json');
    }

    // Handle the form submission to add a new JSON entry
    public function store(Request $request)
    {
        // Validate the request data
        $validatedData = $request->validate([
            'year' => 'required|string',
            'type' => 'required|string',
            'titleLink' => 'required|string',
            'message' => 'required|string',
            'details' => 'nullable|string',
            'about_festival' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048', // Validation for image
        ]);

        // Handle the image upload
        $imagePath = null;
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $imageName = time().'_'.$image->getClientOriginalName();
            $image->move(public_path('muhurat-images'), $imageName);
            $imagePath = 'muhurat-images/'.$imageName;
        }

        // Read the existing JSON file
        $json = file_get_contents(public_path('muhurat-json/muhurats.json'));
        $data = json_decode($json, true);

        // Add the new entry
        $data[] = [
            'year' => $validatedData['year'],
            'type' => $validatedData['type'],
            'titleLink' => $validatedData['titleLink'],
            'message' => $validatedData['message'],
            'details' => $validatedData['details'],
            'about_festival' => $validatedData['about_festival'],
            'image' => $imagePath, // Store the image path in the JSON file
        ];

        // Save the updated data back to the JSON file
        $newJson = json_encode($data, JSON_PRETTY_PRINT);
        //dd($newJson);
        file_put_contents(public_path('muhurat-json/muhurats.json'), $newJson);

        // Redirect back to the list with a success message
        return redirect()->route('admin.json-list')->with('success', 'New JSON entry added successfully!');
    }
    
    
    public function index()
    {
        // Step 1: Read the JSON file from the public directory
        $json = file_get_contents(public_path('muhurat-json/muhurats.json'));
        $data = json_decode($json, true);

        // Step 2: Pass the data to the view
        return view('admin-views.muhurat-json.list-json', compact('data'));
    }

    public function edit($type, $index)
    {
        $jsonPath = public_path('muhurat-json/muhurats.json');

        if (!file_exists($jsonPath)) {
            return redirect()->route('admin.json-list')->with('error', 'Data file not found.');
        }

        $json = file_get_contents($jsonPath);
        $data = json_decode($json, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            return redirect()->route('admin.json-list')->with('error', 'Failed to load data. Invalid JSON format.');
        }

        $entries = collect($data)->values();
        // dd($index, $entries->count());
        // Debugging: Check the count and the last index in entries
        if ($index >= $entries->count()) {
            return redirect()->route('admin.json-list')->with('error', "Entry not found at index $index.");
        }

        $entry = $entries[$index];
        return view('admin-views.muhurat-json.edit-json', compact('entry', 'type', 'index'));
    }


    // public function edit($type, $index)
    // {
    //     $jsonPath = public_path('muhurat-json/muhurats.json');
        
    //     if (!file_exists($jsonPath)) {
    //         return redirect()->route('admin.json-list')->with('error', 'Data file not found.');
    //     }

    //     $json = file_get_contents($jsonPath);
    //     $data = json_decode($json, true);

    //     if (json_last_error() !== JSON_ERROR_NONE) {
    //         return redirect()->route('admin.json-list')->with('error', 'Failed to load data. Invalid JSON format.');
    //     }

    //     $entries = collect($data)->where('type', $type)->values();
    //      dd($entries[$index]); // For debugging
    //     if (!isset($entries[$index])) {
    //         return redirect()->route('admin.json-list')->with('error', 'Entry not found.');
    //     }

    //     $entry = $entries[$index];
    //     //dd($entry);
    //     return view('admin-views.muhurat-json.edit-json', compact('entry', 'type', 'index'));
    // }

    // public function edit($type, $index)
    // {
    //     // Read the JSON file
    //     $json = file_get_contents(public_path('muhurat-json/muhurats.json'));
    //     $data = json_decode($json, true);

    //     // Filter entries by type and get the specific entry by index
    //     $entries = collect($data)->where('type', $type)->values();
    //     dd($entries[$index]);
    //     // Check if the specific entry exists at the provided index
    //     if (!isset($entries[$index])) {
    //         // Redirect to a different route if entry is not found to avoid redirect loop
    //         return redirect()->route('admin.json-list')->with('error', 'Entry not found.');
    //     }

    //     $entry = $entries[$index];

    //     // Pass the specific entry, type, and index to the view
    //     return view('admin-views.muhurat-json.edit-json', compact('entry', 'type', 'index'));
    // }




    // public function edit($type)
    // {
    //     // Read the JSON file
    //     $json = file_get_contents(public_path('muhurat-json/muhurats.json'));
    //     $data = json_decode($json, true);

    //     // Find the specific entry by 'type'
    //     $entry = collect($data)->firstWhere('type', $type);
    //     //dd($entry);
    //     if (!$entry) {
    //         return redirect()->route('admin-views.muhurat-json.edit-json', ['type' => $type])->with('error', 'Entry not found.');
    //     }

    //     // Pass the entry data to the view
    //     return view('admin-views.muhurat-json.edit-json', compact('entry', 'type'));
    // }

    public function update(Request $request, $index)
    {
         // Path to JSON file in the public directory
         $jsonFile = public_path('muhurat-json/muhurats.json');

         // Check if the JSON file exists
         if (!file_exists($jsonFile)) {
             return response()->json(['error' => 'JSON file not found'], 404);
         }
 
         // Load and decode the JSON data
         $jsonContent = file_get_contents($jsonFile);
         $data = json_decode($jsonContent, true);
 
         if (json_last_error() !== JSON_ERROR_NONE) {
             return response()->json(['error' => 'Failed to decode JSON'], 500);
         }
 
         // Validate if index is within bounds
         if ($index < 0 || $index >= count($data)) {
             return response()->json(['error' => 'Index out of bounds'], 400);
         }
 
         // Validate request data
         $validator = Validator::make($request->all(), [
             'titleLink' => 'sometimes|string',
             'about_festival' => 'sometimes|string',
             'details' => 'sometimes|string',
             'image' => 'sometimes|string',
         ]);
 
         if ($validator->fails()) {
             return response()->json(['error' => $validator->errors()], 422);
         }
 
         // Update the specific item by merging the new data
         $data[$index] = array_merge($data[$index], $request->only(['titleLink', 'details', 'about_festival', 'image']));
 
         // Save the updated JSON data back to the file
         file_put_contents($jsonFile, json_encode($data, JSON_PRETTY_PRINT));
 
         return redirect()->route('admin.edit-json', ['type' => $request->type,'index'=>$index])->with('success', 'JSON data updated successfully!');
    }

}
