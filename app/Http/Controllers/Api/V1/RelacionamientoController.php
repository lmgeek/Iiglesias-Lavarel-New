<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\Relacionamiento\StoreRelacionamientoRequest;
use App\Http\Requests\Api\V1\Relacionamiento\UpdateRelacionamientoRequest;
use App\Http\Resources\Api\V1\RelationshipCollection;
use App\Http\Resources\Api\V1\RelationshipResource;
use App\Models\Relationship;
use App\Models\User;
use Illuminate\Http\Request;

class RelacionamientoController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        $query = Relationship::with(['disciple', 'theme', 'mentor'])
            ->where('mentor_id', $user->id)
            ->whereNull('deleted_at');

        if ($request->has('search')) {
            $search = $request->search;
            $query->whereHas('disciple', fn ($q) => $q->where('fullname', 'like', "%{$search}%"));
        }

        if ($request->has('suspended')) {
            $query->where('suspended', $request->suspended);
        }

        $perPage = $request->get('per_page', 15);
        $relationships = $query->orderBy('f_meet', 'desc')->paginate($perPage);

        // Agregar stats
        $total = Relationship::where('mentor_id', $user->id)->whereNull('deleted_at')->count();
        $overdue = Relationship::where('mentor_id', $user->id)
            ->whereNull('deleted_at')
            ->where('suspended', 'No')
            ->where('f_meet', '<', now()->subDays(12))
            ->count();

        return (new RelationshipCollection($relationships))
            ->additional(['meta' => ['total' => $total, 'overdue' => $overdue]]);
    }

    public function myNetwork(Request $request)
    {
        $user = $request->user();

        // Red completa: discípulos directos e indirectos
        $directDisciples = User::whereHas('mentorRelationships', fn ($q) => $q->where('mentor_id', $user->id))
            ->withCount(['mentorRelationships as disciples_count' => fn ($q) => $q->whereNull('deleted_at')])
            ->get();

        return response()->json([
            'direct' => $directDisciples,
            'total' => $directDisciples->sum('disciples_count'),
        ]);
    }

    public function store(StoreRelacionamientoRequest $request)
    {
        $data = $request->validated();
        $data['mentor_id'] = $request->user()->id;

        $relationship = Relationship::create($data);

        return new RelationshipResource($relationship->load(['disciple', 'theme']));
    }

    public function show(Request $request, Relationship $relacionamiento)
    {
        // Verificar que el usuario actual es el mentor
        if ($relacionamiento->mentor_id !== $request->user()->id) {
            return response()->json(['error' => 'No autorizado'], 403);
        }

        return new RelationshipResource($relacionamiento->load(['disciple', 'theme', 'reports']));
    }

    public function update(UpdateRelacionamientoRequest $request, Relationship $relacionamiento)
    {
        if ($relacionamiento->mentor_id !== $request->user()->id) {
            return response()->json(['error' => 'No autorizado'], 403);
        }

        $relacionamiento->update($request->validated());

        return new RelationshipResource($relacionamiento->load(['disciple', 'theme']));
    }

    public function destroy(Request $request, Relationship $relacionamiento)
    {
        if ($relacionamiento->mentor_id !== $request->user()->id) {
            return response()->json(['error' => 'No autorizado'], 403);
        }

        $relacionamiento->delete();

        return response()->json(['message' => 'Relación eliminada']);
    }

    public function getReports(Request $request, int $discipleId)
    {
        $user = $request->user();
        $relationship = Relationship::where('mentor_id', $user->id)
            ->where('disciple_id', $discipleId)
            ->whereNull('deleted_at')
            ->firstOrFail();

        $reports = $relationship->reports()->orderBy('f_meet', 'desc')->get();

        return response()->json($reports);
    }

    public function availableDisciples(Request $request)
    {
        $user = $request->user();

        // Usuarios que no son discípulos del usuario actual
        $existingDisciples = Relationship::where('mentor_id', $user->id)
            ->whereNull('deleted_at')
            ->pluck('disciple_id');

        // Solo la red del usuario (lo tienen como mentor en su perfil o ya existe relación)
        // y del mismo sexo que el usuario
        $available = User::whereNull('deleted_at')
            ->where('id', '!=', $user->id)
            ->whereNotIn('id', $existingDisciples)
            ->where('is_active', true)
            ->where('sex', $user->sex)
            ->where(function ($q) use ($user) {
                $q->where('mentor', $user->id)
                    ->orWhereHas('mentorRelationships', fn ($r) => $r->where('disciple_id', $user->id)->whereNull('deleted_at'))
                    ->orWhereHas('discipleRelationships', fn ($r) => $r->where('mentor_id', $user->id)->whereNull('deleted_at'));
            })
            ->get(['id', 'fullname', 'email', 'phone', 'celula']);

        return response()->json($available);
    }
}
