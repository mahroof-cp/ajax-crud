<?php

namespace App\Imports;

use App\Models\User;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class UserImport implements ToModel, WithHeadingRow
{
    public function model(array $row)
    {
        if (!empty($row['name']) && !empty($row['email'])) {
            return new User([
                'name'     => $row['name'],
                'email'    => $row['email'],
                'password' => bcrypt(12345678) 
            ]);
        }
    }
}
