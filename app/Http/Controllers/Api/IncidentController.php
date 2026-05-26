<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\StoreIncidentRequest;
use App\Http\Requests\UpdateIncidentRequest;
use App\Http\Resources\IncidentResource;
use App\Models\Incident;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller;

class IncidentController extends Controller
{
    /**
     * Get all incidents with filtering and pagination.
     */
    public function index(Request $request)
    {
        try {
            $query = Incident::with('creator', 'assignee');

            // Filter by status
            if ($request->has('status')) {
                $query->where('status', $request->status);
            }

            // Filter by severity
            if ($request->has('severity')) {
                $query->where('severity', $request->severity);
            }

            // Filter by assigned user
            if ($request->has('assigned_to')) {
                $query->where('assigned_to', $request->assigned_to);
            }

            // For non-admin users, show only assigned incidents
            if (!$request->user()->isAdmin()) {
                $query->where(function ($q) use ($request) {
                    $q->where('assigned_to', $request->user()->id)
                        ->orWhere('created_by', $request->user()->id);
                });
            }

            $incidents = $query->orderBy('created_at', 'desc')
                ->paginate($request->get('per_page', 15));

            return response()->json([
                'success' => true,
                'data' => IncidentResource::collection($incidents->items()),
                'pagination' => [
                    'total' => $incidents->total(),
                    'per_page' => $incidents->perPage(),
                    'current_page' => $incidents->currentPage(),
                    'last_page' => $incidents->lastPage(),
                ],
            ], Response::HTTP_OK);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener incidentes.',
                'error' => $e->getMessage(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Get a specific incident.
     */
    public function show(Incident $incident, Request $request)
    {
        try {
            // Verify access
            if (!$request->user()->isAdmin() &&
                $request->user()->id !== $incident->created_by &&
                $request->user()->id !== $incident->assigned_to) {
                return response()->json([
                    'success' => false,
                    'message' => 'No tiene permiso para ver este incidente.',
                ], Response::HTTP_FORBIDDEN);
            }

            $incident->load('creator', 'assignee', 'comments.user');

            return response()->json([
                'success' => true,
                'data' => new IncidentResource($incident),
            ], Response::HTTP_OK);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener incidente.',
                'error' => $e->getMessage(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Create a new incident.
     */
    public function store(StoreIncidentRequest $request)
    {
        try {
            // Only admin and analyst can create incidents
            if (!$request->user()->isAdmin() && !$request->user()->isAnalyst()) {
                return response()->json([
                    'success' => false,
                    'message' => 'No tiene permiso para crear incidentes.',
                ], Response::HTTP_FORBIDDEN);
            }

            $incident = Incident::create([
                'title' => $request->title,
                'description' => $request->description,
                'severity' => $request->severity,
                'status' => 'open',
                'source_ip' => $request->source_ip,
                'affected_host' => $request->affected_host,
                'assigned_to' => $request->assigned_to,
                'created_by' => $request->user()->id,
            ]);

            $incident->load('creator', 'assignee');

            return response()->json([
                'success' => true,
                'message' => 'Incidente creado exitosamente.',
                'data' => new IncidentResource($incident),
            ], Response::HTTP_CREATED);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al crear incidente.',
                'error' => $e->getMessage(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Update an incident.
     */
    public function update(Incident $incident, UpdateIncidentRequest $request)
    {
        try {
            // Verify access
            if (!$request->user()->isAdmin() && $request->user()->id !== $incident->created_by) {
                return response()->json([
                    'success' => false,
                    'message' => 'No tiene permiso para actualizar este incidente.',
                ], Response::HTTP_FORBIDDEN);
            }

            $incident->update($request->validated());
            $incident->load('creator', 'assignee');

            return response()->json([
                'success' => true,
                'message' => 'Incidente actualizado exitosamente.',
                'data' => new IncidentResource($incident),
            ], Response::HTTP_OK);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al actualizar incidente.',
                'error' => $e->getMessage(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Delete an incident.
     */
    public function destroy(Incident $incident, Request $request)
    {
        try {
            // Only admin can delete incidents
            if (!$request->user()->isAdmin()) {
                return response()->json([
                    'success' => false,
                    'message' => 'No tiene permiso para eliminar incidentes.',
                ], Response::HTTP_FORBIDDEN);
            }

            $incident->delete();

            return response()->json([
                'success' => true,
                'message' => 'Incidente eliminado exitosamente.',
            ], Response::HTTP_OK);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al eliminar incidente.',
                'error' => $e->getMessage(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Assign an incident to a user.
     */
    public function assign(Incident $incident, Request $request)
    {
        try {
            $request->validate([
                'assigned_to' => 'required|exists:users,id',
            ]);

            // Only admin and creator can assign
            if (!$request->user()->isAdmin() && $request->user()->id !== $incident->created_by) {
                return response()->json([
                    'success' => false,
                    'message' => 'No tiene permiso para asignar este incidente.',
                ], Response::HTTP_FORBIDDEN);
            }

            $incident->update(['assigned_to' => $request->assigned_to]);
            $incident->load('creator', 'assignee');

            return response()->json([
                'success' => true,
                'message' => 'Incidente asignado exitosamente.',
                'data' => new IncidentResource($incident),
            ], Response::HTTP_OK);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al asignar incidente.',
                'error' => $e->getMessage(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Change incident status.
     */
    public function updateStatus(Incident $incident, Request $request)
    {
        try {
            $request->validate([
                'status' => 'required|in:open,investigating,resolved,closed',
            ]);

            // Verify access
            if (!$request->user()->isAdmin() &&
                $request->user()->id !== $incident->created_by &&
                $request->user()->id !== $incident->assigned_to) {
                return response()->json([
                    'success' => false,
                    'message' => 'No tiene permiso para cambiar el estado de este incidente.',
                ], Response::HTTP_FORBIDDEN);
            }

            $incident->update(['status' => $request->status]);
            $incident->load('creator', 'assignee');

            return response()->json([
                'success' => true,
                'message' => 'Estado del incidente actualizado exitosamente.',
                'data' => new IncidentResource($incident),
            ], Response::HTTP_OK);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al actualizar estado.',
                'error' => $e->getMessage(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
