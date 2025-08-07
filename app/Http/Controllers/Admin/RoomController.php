<?php

namespace App\Http\Controllers\Admin;

use App\Models\Room;

use Illuminate\Http\Request;
use Illuminate\Facades\Validator;
use Illuminate\Support\Facades\DB;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class RoomController extends Controller
{
    /**
     * Display a list of the rooms.
     */
    public function index()
    {
        $rooms = Room::get();
        return $this->renderView('admin.rooms.index', compact('rooms'), 'Rooms');
    }

    /**
     * Show the form for creating a new room.
     */
    public function create()
    {
        // Get the last room number and increment it
        $lastRoom = Room::get('room_no')->last();
        $roomNo = $lastRoom ? $lastRoom->room_no + 1 : 101;
        return $this->renderView('admin.rooms.create', compact('roomNo'),'Add New Room');
    }

    /**
     * Store a newly created room in storage.
     */
    public function store(Request $request)
    {
        // Debug: Log what we're receiving
        \Log::info('Store method called', [
            'has_file' => $request->hasFile('image'),
            'files' => $request->hasFile('image') ? count($request->file('image')) : 0,
            'all_data' => $request->except(['image'])
        ]);

        if ($request->hasFile('image')) {
            foreach ($request->file('image') as $key => $file) {
                \Log::info("File $key details", [
                    'name' => $file->getClientOriginalName(),
                    'size' => $file->getSize(),
                    'mime' => $file->getMimeType(),
                    'is_valid' => $file->isValid(),
                    'error' => $file->getError()
                ]);
            }
        }

        // Validation with more specific error messages
        $request->validate([
            'room_no' => 'required|unique:rooms',
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'rate' => 'required|numeric|min:0',
            'occupancy' => 'required|integer|min:1',
            'image' => 'nullable|array',
            'image.*' => 'nullable|file|image|mimes:jpeg,png,jpg,gif,webp|max:2048'
        ]);

        $room = new Room();
        $room->room_no = $request->room_no;
        $room->name = $request->name;
        $room->description = $request->description;
        $room->rate = $request->rate;
        $room->occupancy = $request->occupancy;

        // Handle image upload
        if ($request->hasFile('image')) {
            $images = [];
            
            // Create directory if it doesn't exist
            $uploadPath = public_path('assets/img/rooms');
            if (!file_exists($uploadPath)) {
                mkdir($uploadPath, 0755, true);
            }
            
            foreach ($request->file('image') as $key => $image) {
                if ($image && $image->isValid()) {
                    $imageName = time() . '_' . $key . '_' . uniqid() . '.' . $image->getClientOriginalExtension();
                    
                    try {
                        $moved = $image->move($uploadPath, $imageName);
                        if ($moved) {
                            $images[] = 'assets/img/rooms/' . $imageName;
                            \Log::info("Image uploaded successfully: $imageName");
                        }
                    } catch (\Exception $e) {
                        \Log::error("Image upload failed: " . $e->getMessage());
                        return back()->withErrors(['image' => 'Failed to upload image ' . ($key + 1) . ': ' . $e->getMessage()])->withInput();
                    }
                } else {
                    \Log::error("Invalid image at index $key");
                    return back()->withErrors(['image' => 'Invalid image file at position ' . ($key + 1)])->withInput();
                }
            }
            
            if (!empty($images)) {
                $room->image = $images; // Model casting will handle JSON encoding
            }
        }

        $room->save();

        return redirect()->route('admin.rooms.index')->with('success', 'Room created successfully!');
    }

    /**
     * Display the specified room.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified room.
     */
    public function edit( Room $room)
    {
        return $this->renderView('admin.rooms.edit', compact('room'),'Add New Room');
    }

    /**
     * Update the specified room in storage.
     */
    public function update(Request $request, Room $room)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'rate' => 'required|numeric|min:0',
            'occupancy' => 'required|integer|min:1',
            'image.*' => 'image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        $room->name = $request->name;
        $room->description = $request->description;
        $room->rate = $request->rate;
        $room->occupancy = $request->occupancy;

        // Handle image upload for updates
        if ($request->hasFile('image')) {
            $images = [];
            foreach ($request->file('image') as $image) {
                $imageName = time() . '_' . uniqid() . '.' . $image->getClientOriginalExtension();
                $image->move(public_path('assets/img/rooms'), $imageName);
                $images[] = 'assets/img/rooms/' . $imageName;
            }
            $room->image = json_encode($images); // Store as JSON array
        }

        $room->save();

        return redirect()->route('admin.rooms.index')->with('success', 'Room details updated successfully.');
    }

    /**
     * Remove the specified room from storage.
     */
    public function destroy(Room $room)
    {
        $room->delete();
        return  redirect()->route('admin.rooms.index')->with('success', 'Room deleted successfully!');
    }
}
