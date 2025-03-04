<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Imports\UserImport;
use App\Models\User;
use Illuminate\Http\Request;
use Laravel\Pail\ValueObjects\Origin\Console;
use Maatwebsite\Excel\Facades\Excel;

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

            $users = User::with('santri')->select('id_user', 'nis', 'email', 'password', 'role', 'created_at');
            return datatables()->of($users)
                ->addIndexColumn()
                ->addColumn('santri', function ($row) {
                    return '<a href="' . route('santri.show', $row->santri) . '">' . $row->santri->nama_santri . '</a>';
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
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
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
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, User $user)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user)
    {
        //
    }
}
