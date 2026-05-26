<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\StoreCommentRequest;
use App\Http\Resources\CommentResource;
use App\Models\Comment;
use App\Models\Incident;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller;

class CommentController extends Controller
{
    /**
     * Get all comments for an incident.
     */
    public function index(Incident $incident)
    {
        try {
            $comments = $incident->comments()
                ->with('user')
                ->orderBy('created_at', 'desc')
                ->get();

            return response()->json([
                'success' => true,
                'data' => CommentResource::collection($comments),
            ], Response::HTTP_OK);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener comentarios.',
                'error' => $e->getMessage(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Store a new comment.
     */
    public function store(Incident $incident, StoreCommentRequest $request)
    {
        try {
            $comment = Comment::create([
                'content' => $request->content,
                'incident_id' => $incident->id,
                'user_id' => $request->user()->id,
            ]);

            $comment->load('user');

            return response()->json([
                'success' => true,
                'message' => 'Comentario creado exitosamente.',
                'data' => new CommentResource($comment),
            ], Response::HTTP_CREATED);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al crear comentario.',
                'error' => $e->getMessage(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Update a comment.
     */
    public function update(Comment $comment, StoreCommentRequest $request)
    {
        try {
            // Verify user owns the comment
            if ($comment->user_id !== $request->user()->id && !$request->user()->isAdmin()) {
                return response()->json([
                    'success' => false,
                    'message' => 'No tiene permiso para actualizar este comentario.',
                ], Response::HTTP_FORBIDDEN);
            }

            $comment->update([
                'content' => $request->content,
            ]);

            $comment->load('user');

            return response()->json([
                'success' => true,
                'message' => 'Comentario actualizado exitosamente.',
                'data' => new CommentResource($comment),
            ], Response::HTTP_OK);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al actualizar comentario.',
                'error' => $e->getMessage(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Delete a comment.
     */
    public function destroy(Comment $comment, Request $request)
    {
        try {
            // Verify user owns the comment or is admin
            if ($comment->user_id !== $request->user()->id && !$request->user()->isAdmin()) {
                return response()->json([
                    'success' => false,
                    'message' => 'No tiene permiso para eliminar este comentario.',
                ], Response::HTTP_FORBIDDEN);
            }

            $comment->delete();

            return response()->json([
                'success' => true,
                'message' => 'Comentario eliminado exitosamente.',
            ], Response::HTTP_OK);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al eliminar comentario.',
                'error' => $e->getMessage(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
