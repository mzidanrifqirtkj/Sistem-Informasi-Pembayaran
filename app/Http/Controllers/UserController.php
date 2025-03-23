<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Imports\UserImport;
use App\Models\Santri;
use App\Models\User;
use Hash;
use Illuminate\Http\Request;
use Laravel\Pail\ValueObjects\Origin\Console;
use Maatwebsite\Excel\Facades\Excel;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // $users = User::with('santri')->paginate(10);
        return view('user.index');
    }

    /**
     * Get user data via DataTables.
     */
    public function getUser()
    {
        try {

            $users = User::with('santri')->select('id_user', 'email', 'password', 'created_at');
            return datatables()->of($users)
                ->addIndexColumn()
                ->addColumn('santri', function ($row) {
                    if ($row->santri) {
                        return '<a href="' . route('santri.show', $row->santri->id_santri) . '">' . $row->santri->nama_santri . '</a>';
                    }
                    return '-';
                })
                ->addColumn('action', function ($row) {
                    return '<a href="' . route('user.edit', $row->id_user) . '" class="btn btn-sm btn-info"><i class="fas fa-pen"></i></a>
                    <button class="btn btn-sm btn-danger" onclick="deleteData(' . $row->id_user . ')"><i class="fas fa-trash"></i></button>';
                })

                ->rawColumns(['santri', 'action'])
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
        $santris = Santri::all();
        // dd($santris);
        $santris = Santri::whereDoesntHave('user')->get(); // Ambil santri yang belum punya akun user
        $roles = Role::all(); // Ambil semua role
        return view('user.create', compact('santris', 'roles'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'santri_id' => 'required|exists:santris,id_santri|unique:users,id_user',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:6',
            'role' => 'required|exists:roles,name',

        ]);

        $user = User::create([
            'id_user' => $request->santri_id, // Menggunakan santri_id sebagai user_id
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);
        $user->assignRole($request->role);

        // Hubungkan dengan santri
        $santri = Santri::find($request->santri_id);
        $santri->user_id = $user->id_user;
        $santri->save();

        return redirect()->route('user.index')->with('success', 'User berhasil ditambahkan');
    }

    /**
     * Display the specified resource.
     */
    public function show(User $user)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(User $user)
    {
        $roles = Role::all(); // Ambil semua role dari database
        return view('user.edit', compact('user', 'roles'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, User $user)
    {
        $request->validate([
            'email' => [
                'required',
                'email',
                'max:255',
            ],
            'role' => 'required|exists:roles,name', // Validasi role harus ada di database
        ], [
            'email.required' => 'Email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'email.max' => 'Email tidak boleh lebih dari 255 karakter.',
            'email.unique' => 'Email sudah digunakan oleh pengguna lain.',
            'role.required' => 'Role wajib dipilih.',
            'role.exists' => 'Role yang dipilih tidak valid.',
        ]);

        $user->update(['email' => $request->email]);
        $user->syncRoles($request->role);

        return redirect()->route('user.index')->with('success', 'Email dan Role berhasil diperbarui.');
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user)
    {
        // Hapus semua role terkait di tabel model_has_roles (Spatie)
        $user->syncRoles([]);

        // Hapus user dari tabel users tanpa menghapus santri
        $user->delete();

        return redirect()->route('user.index')->with('alert', 'User deleted successfully.');
    }



}
