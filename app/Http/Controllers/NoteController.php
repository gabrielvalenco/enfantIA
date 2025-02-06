<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Note;

class NoteController extends Controller
{
    public function index()
    {
        $notes = auth()->user()->notes()->orderBy('updated_at', 'desc')->get();
        return view('notes.index', compact('notes'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|max:255',
            'content' => 'required'
        ]);

        $notesCount = auth()->user()->notes()->count();
        if ($notesCount >= 3) {
            return response()->json(['error' => 'Você já atingiu o limite máximo de 3 notas.'], 422);
        }

        $note = auth()->user()->notes()->create([
            'title' => $request->title,
            'content' => $request->content
        ]);

        return response()->json($note);
    }

    public function update(Request $request, Note $note)
    {
        $request->validate([
            'title' => 'required|max:255',
            'content' => 'required'
        ]);

        if ($note->user_id !== auth()->id()) {
            return response()->json(['error' => 'Não autorizado'], 403);
        }

        $note->update([
            'title' => $request->title,
            'content' => $request->content
        ]);

        return response()->json($note);
    }

    public function destroy(Note $note)
    {
        if ($note->user_id !== auth()->id()) {
            return response()->json(['error' => 'Não autorizado'], 403);
        }

        $note->delete();
        return response()->json(['message' => 'Nota excluída com sucesso']);
    }
}
