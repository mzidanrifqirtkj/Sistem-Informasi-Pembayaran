<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Imports\UserImport;
use App\Models\Santri;
use App\Models\User;
use Hash;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Maatwebsite\Excel\Facades\Excel;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('user.index');
    }

    /**
     * Get user data via DataTables.
     */
    public function getUser()
    {
        try {
            $users = User::with(['santri', 'roles'])->select('id_user', 'email', 'password', 'created_at');
            return datatables()->of($users)
                ->addIndexColumn()
                ->addColumn('santri', function ($row) {
                    if ($row->santri) {
                        return '<a href="' . route('santri.show', $row->santri->id_santri) . '">' . $row->santri->nama_santri . '</a>';
                    }
                    return '-';
                })
                ->addColumn('roles', function ($row) {
                    return $row->roles->map(function ($role) {
                        return '<span class="badge badge-primary">' . ucfirst($role->name) . '</span>';
                    })->implode(' ');
                })
                ->addColumn('action', function ($row) {
                    return '<a href="' . route('user.edit', $row->id_user) . '" class="btn btn-sm btn-info"><i class="fas fa-pen"></i></a>
                    <button class="btn btn-sm btn-danger" onclick="deleteData(' . $row->id_user . ')"><i class="fas fa-trash"></i></button>';
                })
                ->rawColumns(['santri', 'roles', 'action'])
                ->make(true);
        } catch (\Exception $e) {
            return response()->json([
                'error' => true,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function importForm()
    {
        return view('user.import');
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls,csv|max:2048',
        ]);

        try {
            Excel::import(new UserImport, $request->file('file'));
            return redirect()->route('user.index')->with('alert', 'Data user berhasil diimpor.');
        } catch (\Exception $e) {
            return redirect()->route('user.index')->with('error', 'Terjadi kesalahan saat mengimpor data: ' . $e->getMessage());
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $santris = Santri::whereDoesntHave('user')->get();
        $roles = Role::all();
        return view('user.create', compact('santris', 'roles'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'santri_id' => 'nullable|exists:santris,id_santri|unique:users,id_user',
            'email' => 'nullable|email|unique:users,email',
            'password' => 'required|min:6',
            'roles' => 'required|array',
            'roles.*' => 'exists:roles,name',
        ], [
            'santri_id.required_without' => 'Pilih santri atau isi email',
            'email.required_without' => 'Isi email atau pilih santri',
        ]);

        // Validasi minimal salah satu harus diisi
        if (empty($request->santri_id) && empty($request->email)) {
            return back()->withErrors(['santri_id' => 'Harus memilih santri atau mengisi email', 'email' => 'Harus mengisi email atau memilih santri']);
        }

        $userData = [
            'password' => Hash::make($request->password),
        ];

        // Jika ada santri_id, gunakan sebagai id_user
        if ($request->filled('santri_id')) {
            $userData['id_user'] = $request->santri_id;
        }

        // Jika ada email, tambahkan ke data user
        if ($request->filled('email')) {
            $userData['email'] = $request->email;
        }

        $user = User::create($userData);
        $user->syncRoles($request->roles);

        // Jika ada santri_id, update relasi
        if ($request->filled('santri_id')) {
            Santri::find($request->santri_id)->update(['user_id' => $user->id_user]);
        }

        return redirect()->route('user.index')->with('success', 'User berhasil ditambahkan');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(User $user)
    {
        $roles = Role::all();
        $santris = Santri::whereDoesntHave('user')->orWhere('user_id', $user->id_user)->get();
        return view('user.edit', compact('user', 'roles', 'santris'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, User $user)
    {
        $request->validate([
            'santri_id' => 'nullable|exists:santris,id_santri|unique:users,id_user,' . $user->id_user . ',id_user',
            'email' => [
                'nullable',
                'email',
                'max:255',
                Rule::unique('users')->ignore($user->id_user, 'id_user')
            ],
            'password' => 'nullable|min:6',
            'roles' => 'required|array',
            'roles.*' => 'exists:roles,name',
        ], [
            'santri_id.required_without' => 'Pilih santri atau isi email',
            'email.required_without' => 'Isi email atau pilih santri',
        ]);

        // Validasi minimal salah satu harus diisi
        if (empty($request->santri_id) && empty($request->email)) {
            return back()->withErrors(['santri_id' => 'Harus memilih santri atau mengisi email', 'email' => 'Harus mengisi email atau memilih santri']);
        }

        $userData = [];

        // Update email jika diisi
        if ($request->filled('email')) {
            $userData['email'] = $request->email;
        }

        // Update password jika diisi
        if ($request->filled('password')) {
            $userData['password'] = Hash::make($request->password);
        }

        // Update santri_id jika diisi
        if ($request->filled('santri_id')) {
            // Hapus relasi sebelumnya jika ada
            if ($user->santri) {
                $user->santri->update(['user_id' => null]);
            }

            $userData['id_user'] = $request->santri_id;
            Santri::find($request->santri_id)->update(['user_id' => $request->santri_id]);
        }

        $user->update($userData);
        $user->syncRoles($request->roles);

        return redirect()->route('user.index')->with('success', 'Data user berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user)
    {
        // Hapus relasi dengan santri jika ada
        if ($user->santri) {
            $user->santri->update(['user_id' => null]);
        }

        // Hapus semua role terkait
        $user->syncRoles([]);

        // Hapus user
        $user->delete();

        return redirect()->route('user.index')->with('alert', 'User berhasil dihapus.');
    }
}
