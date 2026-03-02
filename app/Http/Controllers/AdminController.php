<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Validation\Rules;

class AdminController extends Controller
{
    /**
     * Dashboard do Administrador
     */
    public function dashboard()
    {
        $stats = [
            'total_personals' => User::where('role', 'personal')->count(),
            'active_personals' => User::where('role', 'personal')->where('is_active', true)->count(),
            'inactive_personals' => User::where('role', 'personal')->where('is_active', false)->count(),
            'total_students' => User::where('role', 'aluno')->count(),
            'total_nutritionists' => User::where('role', 'nutri')->count(),
        ];

        return view('admin.dashboard', compact('stats'));
    }

    /**
     * Listar todos os personals
     */
    public function listPersonals(Request $request)
    {
        $query = User::where('role', 'personal');

        // Filtro por status
        if ($request->has('status') && $request->status !== '') {
            $status = $request->status;
            $query->where('is_active', $status);
        }

        // Busca por nome ou email
        if ($request->has('search') && $request->search !== '') {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        // Ordenação
        $sortBy = $request->get('sort_by', 'created_at');
        $sortOrder = $request->get('sort_order', 'desc');
        $query->orderBy($sortBy, $sortOrder);

        $personals = $query->paginate(15);

        return view('admin.personals.index', compact('personals'));
    }

    /**
     * Visualizar detalhes de um personal
     */
    public function showPersonal(User $user)
    {
        if ($user->role !== 'personal') {
            abort(404);
        }

        // Contar alunos do personal
        $studentCount = User::whereHas('professionalStudents', function ($query) use ($user) {
            $query->where('professional_id', $user->id);
        })->count();

        $user->load('professionalStudents');

        return view('admin.personals.show', compact('user', 'studentCount'));
    }

    /**
     * Forma para editar personal
     */
    public function editPersonal(User $user)
    {
        if ($user->role !== 'personal') {
            abort(404);
        }

        return view('admin.personals.edit', compact('user'));
    }

    /**
     * Atualizar dados do personal
     */
    public function updatePersonal(Request $request, User $user)
    {
        if ($user->role !== 'personal') {
            abort(404);
        }

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email,' . $user->id],
            'phone' => ['nullable', 'string', 'max:20'],
            'profession' => ['nullable', 'string', 'max:255'],
            'license_expires_at' => ['nullable', 'date'],
            'admin_notes' => ['nullable', 'string', 'max:1000'],
        ]);

        $user->update($validated);

        return redirect()->route('admin.personals.show', $user->id)
                       ->with('success', 'Personal atualizado com sucesso!');
    }

    /**
     * Criar novo personal (formulário)
     */
    public function createPersonal()
    {
        return view('admin.personals.create');
    }

    /**
     * Armazenar novo personal
     */
    public function storePersonal(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'phone' => ['nullable', 'string', 'max:20'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'profession' => ['nullable', 'string', 'max:255'],
            'license_expires_at' => ['nullable', 'date'],
            'admin_notes' => ['nullable', 'string', 'max:1000'],
        ]);

        $validated['password'] = bcrypt($validated['password']);
        $validated['role'] = 'personal';
        $validated['is_active'] = true;

        User::create($validated);

        return redirect()->route('admin.personals.index')
                       ->with('success', 'Personal criado com sucesso!');
    }

    /**
     * Ativar/Desativar personal
     */
    public function togglePersonalStatus(User $user)
    {
        if ($user->role !== 'personal') {
            abort(404);
        }

        $user->update(['is_active' => !$user->is_active]);

        $status = $user->is_active ? 'ativado' : 'desativado';

        return redirect()->back()
                       ->with('success', "Personal {$status} com sucesso!");
    }

    /**
     * Atualizar status da licença
     */
    public function updateLicenseStatus(Request $request, User $user)
    {
        if ($user->role !== 'personal') {
            abort(404);
        }

        $validated = $request->validate([
            'license_active' => ['required', 'boolean'],
            'license_expires_at' => ['nullable', 'date'],
        ]);

        $user->update($validated);

        return redirect()->route('admin.personals.show', $user->id)
                       ->with('success', 'Status da licença atualizado!');
    }

    /**
     * Deletar personal
     */
    public function deletePersonal(User $user)
    {
        if ($user->role !== 'personal') {
            abort(404);
        }

        $name = $user->name;
        $user->delete();

        return redirect()->route('admin.personals.index')
                       ->with('success', "Personal {$name} deletado com sucesso!");
    }

    /**
     * Gerenciar alunos do personal
     */
    public function personalStudents(User $user)
    {
        if ($user->role !== 'personal') {
            abort(404);
        }

        $students = User::whereHas('professionalStudents', function ($query) use ($user) {
            $query->where('professional_id', $user->id);
        })->paginate(15);

        return view('admin.personals.students', compact('user', 'students'));
    }

    /**
     * Listar todos os usuários (para auditoria)
     */
    public function allUsers(Request $request)
    {
        $query = User::query();

        // Filtro por role
        if ($request->has('role') && $request->role !== '') {
            $query->where('role', $request->role);
        }

        // Filtro por status
        if ($request->has('status') && $request->status !== '') {
            $status = $request->status;
            $query->where('is_active', $status);
        }

        // Busca
        if ($request->has('search') && $request->search !== '') {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $users = $query->paginate(20);

        return view('admin.users.index', compact('users'));
    }

    /**
     * Tela de auditoria/logs
     */
    public function logs()
    {
        return view('admin.logs');
    }
}
