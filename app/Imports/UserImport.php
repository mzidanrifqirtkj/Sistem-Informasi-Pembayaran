<?php

namespace App\Imports;

use App\Models\User;
use Maatwebsite\Excel\Concerns\ToModel;

class UserImport implements ToModel
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        // dd($row);
        if (!isset($row[0],$row[1], $row[2], $row[3])) {
            throw new \Exception('Header file tidak valid atau kolom tidak lengkap.');
        }

        return new User([
                'email'    => $row[0],
                'nis'      => (int)$row[1],
                'password' => bcrypt($row[2]),
                'role'     => $row[3]
        ]);
    }
    public function rules(): array
    {
        return [
            'email'    => 'required|email|unique:users,email',
            'nis' => 'required|integer|unique:users,nis',
            'password' => 'required|min:8', // Minimum 8 karakter
            'role' => 'required|in:admin,user'
        ];
    }

}
