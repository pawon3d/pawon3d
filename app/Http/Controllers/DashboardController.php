<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function ringkasan()
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        $permissionRoutes = [
            'Manajemen Sistem' => 'ringkasan-kasir',
            'Kasir' => 'ringkasan-kasir',
            'Produksi' => 'ringkasan-produksi',
            'Inventori' => 'ringkasan-inventori',
        ];

        foreach ($permissionRoutes as $permission => $routeName) {
            if ($user->hasPermissionTo($permission)) {
                return redirect()->route($routeName);
            }
        }

        return redirect()->route('home');
    }
}
