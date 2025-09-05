<?php

namespace Database\Seeders;

/**use Illuminate\Database\Console\Seeds\WithoutModelEvents;*/
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Seeder;
use App\Models\User;

class DemoUsersSeeder extends Seeder
{
    public function run(): void
    {
        User::updateOrCreate(['email'=>'biblio@demo.com'],[
            'name'=>'Biblio',
            'password'=>Hash::make('password123'),
            'role'=>'bibliotecario'
        ]);
        User::updateOrCreate(['email'=>'est@demo.com'],[
            'name'=>'Estudiante',
            'password'=>Hash::make('password123'),
            'role'=>'estudiante'
        ]);
        User::updateOrCreate(['email'=>'doc@demo.com'],[
            'name'=>'Docente',
            'password'=>Hash::make('password123'),
            'role'=>'docente'
        ]);
    }
}
