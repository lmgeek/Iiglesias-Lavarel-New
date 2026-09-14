<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\MeetingsTheme;
use App\Models\Relationship;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class RelacionamientoController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();

        $relationships = Relationship::with(['disciple', 'theme'])
            ->where('mentor_id', $user->id)
            ->whereNull('deleted_at')
            ->orderBy('f_meet', 'desc')
            ->get();

        $latest = $relationships->unique('disciple_id');

        if ($request->filled('search')) {
            $search = mb_strtolower(trim($request->search));
            $latest = $latest->filter(fn ($rel) => $rel->disciple && str_contains(mb_strtolower($rel->disciple->fullname), $search));
        }

        $total = $latest->count();
        $overdue = $latest->filter(fn ($rel) => $rel->suspended !== 'Si' && $rel->f_meet && $rel->f_meet->lt(now()->subDays(12)))->count();

        $paginator = new LengthAwarePaginator(
            $latest->forPage($request->integer('page', 1), 15)->loadMissing(['disciple', 'theme']),
            $total,
            15,
            $request->integer('page', 1),
            ['path' => $request->url(), 'query' => $request->query()]
        );

        return view('relacionamiento.index', [
            'relationships' => $paginator,
            'total' => $total,
            'overdue' => $overdue,
            'search' => $request->search,
        ]);
    }

    public function informes(Request $request)
    {
        $user = Auth::user();

        $query = Relationship::with(['disciple', 'theme'])
            ->where('mentor_id', $user->id)
            ->whereNull('deleted_at');

        if ($request->filled('start_date') && $request->filled('end_date')) {
            $query->whereBetween('f_meet', [$request->start_date, $request->end_date]);
        }

        if ($request->filled('search')) {
            $search = mb_strtolower(trim($request->search));
            $query->whereHas('disciple', fn ($q) => $q->whereNull('deleted_at')->whereRaw('LOWER(fullname) LIKE ?', ["%{$search}%"]));
        }

        $relationships = $query->orderBy('f_meet', 'desc')->paginate(15)->withQueryString();

        return view('relacionamiento.informes', [
            'relationships' => $relationships,
            'total' => Relationship::where('mentor_id', $user->id)->whereNull('deleted_at')->count(),
            'thisMonth' => Relationship::where('mentor_id', $user->id)
                ->whereMonth('f_meet', now()->month)
                ->whereYear('f_meet', now()->year)
                ->whereNull('deleted_at')
                ->count(),
        ]);
    }

    public function searchDisciples(Request $request)
    {
        $user = Auth::user();
        $term = trim((string) $request->get('search'));

        $query = User::whereNull('deleted_at')
            ->where('is_active', true)
            ->where('sex', $user->sex)
            ->where('id', '!=', $user->id);

        if ($term !== '') {
            $query->where('fullname', 'like', "%{$term}%");
        }

        $members = $query->orderBy('fullname')->limit(10)->get(['id', 'fullname', 'email', 'phone', 'sex']);

        return response()->json($members->map(fn (User $u) => [
            'id' => $u->id,
            'fullname' => $u->fullname,
            'email' => $u->email,
            'phone' => $u->phone,
            'sex' => $u->sex,
            'avatar' => mb_substr($u->fullname, 0, 1),
        ]));
    }

    public function red()
    {
        $user = Auth::user();

        $relationships = Relationship::whereNull('deleted_at')
            ->get(['id', 'mentor_id', 'disciple_id']);

        $userIds = $relationships->pluck('mentor_id')
            ->merge($relationships->pluck('disciple_id'))
            ->push($user->id)
            ->unique()
            ->values();

        $users = User::whereIn('id', $userIds)
            ->get(['id', 'fullname', 'email', 'phone', 'celula', 'lider_celula'])
            ->keyBy('id');

        $children = [];
        foreach ($relationships as $relationship) {
            $children[$relationship->mentor_id][$relationship->disciple_id] = $relationship->disciple_id;
        }

        $tree = $this->buildRelationshipTree($user->id, $children, $users);

        return view('relacionamiento.red', [
            'tree' => $tree,
            'me' => $user,
            'total' => $this->countRelationshipTree($tree),
        ]);
    }

    private function buildRelationshipTree(int $parentId, array $children, Collection $users, ?Collection $visited = null): array
    {
        $visited ??= collect();

        if ($visited->contains($parentId)) {
            return [];
        }

        $visited = $visited->push($parentId);

        $nodes = [];

        foreach ($children[$parentId] ?? [] as $childId) {
            $child = $users->get($childId);

            if (! $child || ! $this->reachesRoot($childId, $parentId, $visited)) {
                continue;
            }

            $nodes[] = [
                'user' => $child,
                'children' => $this->buildRelationshipTree($childId, $children, $users, clone $visited),
            ];
        }

        foreach ($nodes as &$node) {
            $node['totalBelow'] = $this->countRelationshipTree($node['children']);
        }

        return $nodes;
    }

    private function reachesRoot(int $childId, int $parentId, Collection $visited): bool
    {
        return ! $visited->contains($childId);
    }

    private function countRelationshipTree(array $tree): int
    {
        $total = 0;

        foreach ($tree as $node) {
            $total += 1 + $this->countRelationshipTree($node['children']);
        }

        return $total;
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'disciple_id' => 'required|integer|exists:users,id',
            'suspended' => 'sometimes|in:Si,No',
            'why_suspended' => 'nullable|string',
            'theme_meetings_id' => 'nullable|integer|exists:meetings_themes,id',
            'other_theme' => 'nullable|string|max:255',
            'culminate' => 'nullable|string|max:255',
            'initiative' => 'nullable|string|max:255',
            'reading' => 'nullable|string|max:255',
            'testimonials' => 'nullable|string|max:255',
            'pray_together' => 'nullable|string|max:255',
            'description' => 'nullable|string',
        ]);

        $data['f_meet'] = $request->filled('f_meet') ? $request->date('f_meet') : now();

        $user = Auth::user();
        $disciple = User::whereNull('deleted_at')->find($data['disciple_id']);

        if (! $disciple) {
            return back()->withInput()->withErrors(['disciple_id' => 'El discípulo seleccionado no existe.']);
        }

        if ($disciple->sex !== $user->sex) {
            return back()->withInput()->withErrors(['disciple_id' => 'El mentor y el discípulo deben ser del mismo sexo.']);
        }

        $data['mentor_id'] = $user->id;
        $data['uuid'] = (string) Str::uuid();

        Relationship::create($data);

        return redirect()->route('relacionamiento.index')
            ->with('success', 'Discípulo asignado correctamente');
    }

    public function informeCreate(User $disciple)
    {
        $user = Auth::user();

        if ($disciple->id === $user->id || $disciple->sex !== $user->sex) {
            abort(403, 'No tenés una relación de discipulado con este miembro.');
        }

        $isMine = Relationship::where('mentor_id', $user->id)
            ->where('disciple_id', $disciple->id)
            ->whereNull('deleted_at')
            ->exists()
            || $disciple->mentor == $user->id;

        if (! $isMine) {
            abort(403, 'No tenés una relación de discipulado con este miembro.');
        }

        return view('relacionamiento.informe', [
            'disciple' => $disciple,
            'themes' => MeetingsTheme::whereNull('deleted_at')->orderBy('classname')->get(),
        ]);
    }

    public function informeStore(Request $request, User $disciple)
    {
        $user = Auth::user();

        if ($disciple->id === $user->id || $disciple->sex !== $user->sex) {
            abort(403, 'No tenés una relación de discipulado con este miembro.');
        }

        $isMine = Relationship::where('mentor_id', $user->id)
            ->where('disciple_id', $disciple->id)
            ->whereNull('deleted_at')
            ->exists()
            || $disciple->mentor == $user->id;

        if (! $isMine) {
            abort(403, 'No tenés una relación de discipulado con este miembro.');
        }

        $data = $request->validate([
            'f_meet' => 'required|date',
            'suspended' => 'sometimes|in:Si,No',
            'why_suspended' => 'nullable|string',
            'theme_meetings_id' => 'nullable|integer|exists:meetings_themes,id',
            'other_theme' => 'nullable|string|max:255',
            'culminate' => 'nullable|string|max:255',
            'initiative' => 'nullable|string|max:255',
            'reading' => 'nullable|string|max:255',
            'testimonials' => 'nullable|string|max:255',
            'pray_together' => 'nullable|string|max:255',
            'description' => 'nullable|string',
        ]);

        $data['mentor_id'] = $user->id;
        $data['disciple_id'] = $disciple->id;
        $data['uuid'] = (string) Str::uuid();

        Relationship::create($data);

        return redirect()->route('relacionamiento.index')
            ->with('success', 'Informe de relacionamiento registrado correctamente');
    }
}
