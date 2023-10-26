<?php

namespace App\Http\Controllers;

use App\Models\Note;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Requests\NoteRequest;
use Illuminate\Support\Facades\Auth;

class NoteController extends Controller
{
    public function getAllNote(Request $request)
    {
        if(Auth::user()->role == 'admin' || Auth::user()->role == 'editor'){
            $title = $request->input('title');
            $date = $request->input('date');
    
            $query = Note::query();
    
            if ($title) {
                $query->whereHas('title', function ($q) use ($title) {
                    $q->where('title', 'like', '%' . $title . '%');
                });
            }

            if ($date) {
                $query->whereDate('created_at', $date);
            }
          
            $query->orderBy('created_at', 'desc');
            $note = $query->get();
    
        }else{
            $title = $request->input('title');
            $date = $request->input('date');
    
            $query = Note::query();
    
        
            if ($date) {
                $query->whereDate('created_at', $date);
            }
    
            if ($title) {
                $query->where('title', 'like', '%' . $title . '%');
            }
    
            if ($date) {
                $query->whereDate('created_at', $date);
            }
            $query->orderBy('created_at', 'desc');
    
            $note = $query->where('user_id', Auth::user()->id)->get();

            
        }
        if($note->isEmpty()){
            return response()->json([
                'success' => true,
                'message' => 'Note list',
                'data' => 'Note not found'
            ], Response::HTTP_OK);
        }else{
            return response()->json([
                'success' => true,
                'message' => 'Note list',
                'data' => $note
            ], Response::HTTP_OK);
        }
      
       
    }

    public function getNoteById($id)
    {
        $note = Note::find($id);

        if(!$note){
            return response()->json([
                'success' => false,
                'message' => 'Note not found',
            ], Response::HTTP_NOT_FOUND);
         }

         if (Auth::user()->role === 'admin' || Auth::user()->role === 'editor') {
            $response = [
                'success' => true,
                'message' => 'Note found successfully',
                'data' => $note,
            ];
            $statusCode = Response::HTTP_OK;
        } else {
            if ($note->user_id !== Auth::user()->id) {
                $response = [
                    'success' => false,
                    'message' => 'You are not authorized to view this note',
                ];
                $statusCode = Response::HTTP_FORBIDDEN;
            } else {
                $response = [
                    'success' => true,
                    'message' => 'Note found successfully',
                    'data' => $note,
                ];
                $statusCode = Response::HTTP_OK;
            }
        }
        
        return response()->json($response, $statusCode);

    }

    public function store(NoteRequest $request)
    {
        $note = Note::create([
            'title' => $request->input('title'),
            'content' => $request->input('content'),
            'user_id' => Auth::user()->id
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Note created successfully',
            'data' => $note,
         ], Response::HTTP_CREATED);
    }

    public function update(NoteRequest $request, $id)
    {
     
        $note = Note::find($id);

        if(!$note){
            return response()->json([
                'success' => false,
                'message' => 'Note not found',
            ], Response::HTTP_NOT_FOUND);
         }

         if (Auth::user()->role === 'admin' || Auth::user()->role === 'editor') {
            $note->update([
                'title' => $request->input('title'),
                'content' => $request->input('content'),
            ]);

            $response = [
                'success' => true,
                'message' => 'Note updated successfully',
                'data' => $note,
            ];
            $statusCode = Response::HTTP_OK;
        } else {
            if ($note->user_id !== Auth::user()->id) {
                $response = [
                    'success' => false,
                    'message' => 'You are not authorized to update this note',
                ];
                $statusCode = Response::HTTP_FORBIDDEN;
            } else {
                $note->update([
                    'title' => $request->input('title'),
                    'content' => $request->input('content'),
                ]);
                $response = [
                    'success' => true,
                    'message' => 'Note updated successfully',
                    'data' => $note,
                ];
                $statusCode = Response::HTTP_OK;
            }
        }

        return response()->json($response, $statusCode);
    }

    public function destroy($id)
    {
        $note = Note::find($id);

        if(!$note){
            return response()->json([
                'success' => false,
                'message' => 'Note not found',
            ], Response::HTTP_NOT_FOUND);
         }

         if (Auth::user()->role === 'admin' || Auth::user()->role === 'editor') {
            $note->delete();

            $response = [
                'success' => true,
                'message' => 'Note delete successfully',
                'data' => $note,
            ];
            $statusCode = Response::HTTP_OK;
        } else {
            if ($note->user_id !== Auth::user()->id) {
                $response = [
                    'success' => false,
                    'message' => 'You are not authorized to update this note',
                ];
                $statusCode = Response::HTTP_FORBIDDEN;
            } else {
                $note->delete();
                $response = [
                    'success' => true,
                    'message' => 'Note delete successfully',
                    'data' => $note,
                ];
                $statusCode = Response::HTTP_OK;
            }
        }

        return response()->json($response, $statusCode);

      
    }
}
