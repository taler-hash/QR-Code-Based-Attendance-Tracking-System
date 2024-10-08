<?php

namespace App\Services;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Models\SectionBean;

class UserService
{
    public function __construct(private string $role)
    {
        $this->role = $role;
    }

    public function display($request)
    {
        // dd($request->filter)
        // TIwasa ni sa relationship belongstomany
        $model = new User();
        $determinedSort = $this->determineSort();
        $sections = auth()->user()->sections;

        $user = $model::when(!empty($this->role), function ($q) {
            $q->role($this->role);
        })
            ->whereHas('sections', function ($query) use ($sections, $request) {
                if ($sections->isNotEmpty()) {
                    $query->whereIn('section_beans.section', $sections->pluck('id'));
                }

                if($request->section != 0) {
                    $query->where('section_beans.section', $request->section);
                }
            })
            ->with(['sections' => function ($q) use ($sections) {
                if($sections->isNotEmpty()) {
                    $q->whereIn('section_beans.section', $sections->pluck('id'));
                }
            }])
          
            ->whereAny($model->getFillable(), 'LIKE', "%{$request->filter}%")
            ->orderBy($determinedSort['sortBy'], $determinedSort['sortType'])
            ->paginate($request->rows ?? 10);


        $props = $user->toArray();

        $props['filter'] = $request->filter;
        $props['sortBy'] = $request->sortBy;
        $props['sortType'] = (int)$request->sortType;
        $props['page'] = (int)$request->page;



        return $props;
    }
    
    public function create($request): void
    {
        DB::transaction(function () use ($request) {

            $props = $request->all();
            $user = User::create($props);

            $sections = collect($request->sections)->map(function ($item) use ($user) {
                return [
                    'user' => $user->id,
                    'section' => $item
                ];
            })->toArray();

            $user->sections()->sync($sections);
            $user->assignRole($this->role);
        });
    }

    public function update($request)
    {
        $user = User::find($request->id);

        $user->fill($request->except('password'));

        if (!is_null($request->password)) {
            $user->password = $request->password;
        }

        if (!empty($request->sections)) {
            $user->sections()->detach();
            $user->sections()->attach($request->sections);
        }

        if (!empty($request->role)) {
            $user->syncRoles([$request->role]);
        }

        $user->save();
    }

    public function delete($request)
    {
        $user = User::find($request->id);
        $user->delete();
    }

    public function get($id): User {
        return User::whereId($id)->with('sections')->first();
    }

    private function determineSort()
    {
        return [
            'sortBy' => request()->sortBy ?? 'id',
            'sortType' => in_array(request()->sortType, [1, null]) ? 'asc' : 'desc'
        ];
    }
}
