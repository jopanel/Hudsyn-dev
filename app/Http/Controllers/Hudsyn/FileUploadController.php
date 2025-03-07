<?php

namespace App\Http\Controllers\Hudsyn;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Hudsyn\FileUpload;
use Illuminate\Support\Facades\File; // For file system functions
use Illuminate\Http\JsonResponse;

class FileUploadController extends Controller
{
    /**
     * Display a listing of uploaded files.
     */
    public function index()
    {
        $files = FileUpload::orderBy('created_at', 'desc')->get();
        return view('hudsyn.files.index', compact('files'));
    }

    /**
     * Show the form for uploading a new file.
     * (We can also include this form in the index view.)
     */
    public function create()
    {
        return view('hudsyn.files.create');
    }

    /**
     * Display a gallery of image files.
     */
    public function gallery(Request $request)
    {
        // Get only files that are images
        $images = \App\Hudsyn\FileUpload::where('mime_type', 'like', 'image/%')
                    ->orderBy('created_at', 'desc')
                    ->get();

        return view('hudsyn.files.gallery', compact('images'));
    }

    /**
     * Handle image uploads from CKEditor.
     * This method accepts an image file, moves it to the uploads folder,
     * and returns a JSON response with the image URL.
     */
    public function uploadImage(Request $request)
    {
        $request->validate([
            'upload' => 'required|image|max:10240' // max 10MB, for example
        ]);

        $uploadedFile = $request->file('upload');

        // Get properties before moving
        $originalName = $uploadedFile->getClientOriginalName();
        $fileSize     = $uploadedFile->getSize();
        $mimeType     = $uploadedFile->getClientMimeType();

        $fileName = time() . '_' . $originalName;
        $uploadDir = 'uploads';
        
        if (!\Illuminate\Support\Facades\File::exists(public_path($uploadDir))) {
            \Illuminate\Support\Facades\File::makeDirectory(public_path($uploadDir), 0755, true);
        }
        
        $uploadedFile->move(public_path($uploadDir), $fileName);
        $filePath = $uploadDir . '/' . $fileName;

        // Save the file details to the database
        $file = \App\Hudsyn\FileUpload::create([
            'file_name'     => $fileName,
            'original_name' => $originalName,
            'file_path'     => $filePath,
            'file_size'     => $fileSize,
            'mime_type'     => $mimeType,
        ]);

        // CKEditor expects a JSON response with "uploaded" flag and the URL of the image.
        return response()->json([
            'uploaded' => 1,
            'fileName' => $fileName,
            'url'      => asset($filePath)
        ]);
    }

    /**
     * Store a newly uploaded file.
     */
    public function store(Request $request)
    {
        $request->validate([
            'file' => 'required|file|max:10240',
        ]);

        if ($request->hasFile('file')) {
            $uploadedFile = $request->file('file');

            // Get properties before moving the file
            $originalName = $uploadedFile->getClientOriginalName();
            $fileSize     = $uploadedFile->getSize();
            $mimeType     = $uploadedFile->getClientMimeType();

            $fileName = time() . '_' . $originalName;
            $uploadDir = 'uploads';
            
            if (!\Illuminate\Support\Facades\File::exists(public_path($uploadDir))) {
                \Illuminate\Support\Facades\File::makeDirectory(public_path($uploadDir), 0755, true);
            }
            
            // Move the file to the uploads directory
            $uploadedFile->move(public_path($uploadDir), $fileName);
            $filePath = $uploadDir . '/' . $fileName;

            \App\Hudsyn\FileUpload::create([
                'file_name'     => $fileName,
                'original_name' => $originalName,
                'file_path'     => $filePath,
                'file_size'     => $fileSize,
                'mime_type'     => $mimeType,
            ]);

            return redirect()->route('hudsyn.files.index')
                             ->with('success', 'File uploaded successfully.');
        }

        return redirect()->back()->withErrors('File upload failed.');
    }


    /**
     * Optionally, add methods for show, edit, update if needed.
     * For now, we implement destroy.
     */

    /**
     * Remove the specified file.
     */
    public function destroy($id)
    {
        $file = FileUpload::findOrFail($id);
        $fullPath = public_path($file->file_path);
        if (File::exists($fullPath)) {
            unlink($fullPath);
        }
        $file->delete();

        return redirect()->route('hudsyn.files.index')
                         ->with('success', 'File deleted successfully.');
    }
}
