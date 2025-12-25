<?php

namespace App\Livewire\Peran;

use App\Models\SpatieRole;
use Database\Seeders\PermissionSeeder;
use Illuminate\Support\Facades\View;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Component;

class Form extends Component
{
    use LivewireAlert;

    public ?int $roleId = null;

    public string $roleName = '';

    public ?int $maxUsers = null;

    /**
     * Permission yang dipilih (array of permission names).
     *
     * @var array<string>
     */
    public array $selectedPermissions = [];

    /**
     * Toggle kategori yang dibuka (untuk show/hide detail permissions).
     *
     * @var array<string, bool>
     */
    public array $categoryToggles = [
        'kasir' => false,
        'produksi' => false,
        'inventori' => false,
        'manajemen' => false,
    ];

    /**
     * Users yang memiliki role ini (untuk edit mode).
     */
    public $users;

    protected $listeners = [
        'delete',
    ];

    /**
     * Check apakah sedang dalam mode edit.
     */
    public function isEditMode(): bool
    {
        return $this->roleId !== null;
    }

    public function mount(?int $id = null): void
    {
        View::share('mainTitle', 'Pekerja');

        if ($id) {
            $this->roleId = $id;
            $role = SpatieRole::findOrFail($id);

            View::share('title', 'Rincian Peran');

            $this->roleName = $role->name;
            $this->maxUsers = $role->max_users;
            $this->selectedPermissions = $role->permissions->pluck('name')->toArray();
            $this->users = $role->users;

            // Set category toggles based on selected permissions
            $this->syncCategoryToggles();
        } else {
            View::share('title', 'Tambah Peran');
            $this->users = collect();
        }
    }

    /**
     * Sync category toggles based on selected permissions.
     */
    protected function syncCategoryToggles(): void
    {
        $permissions = PermissionSeeder::getPermissionsByCategory();

        foreach ($permissions as $category => $perms) {
            $categoryPermissionNames = array_keys($perms);
            $hasAnySelected = count(array_intersect($categoryPermissionNames, $this->selectedPermissions)) > 0;
            $this->categoryToggles[$category] = $hasAnySelected;
        }
    }

    /**
     * Toggle kategori permissions (called when main category toggle is changed).
     */
    public function toggleCategory(string $category): void
    {
        $isNowEnabled = $this->categoryToggles[$category];
        $permissions = PermissionSeeder::getPermissionsByCategory();
        $categoryPermissions = array_keys($permissions[$category] ?? []);

        if ($isNowEnabled) {
            // Add all permissions from this category
            $this->selectedPermissions = array_unique(array_merge(
                $this->selectedPermissions,
                $categoryPermissions
            ));
        } else {
            // Remove all permissions from this category
            $this->selectedPermissions = array_values(array_diff(
                $this->selectedPermissions,
                $categoryPermissions
            ));
        }
    }

    /**
     * Toggle individual permission checkbox.
     */
    public function togglePermission(string $permission): void
    {
        if (in_array($permission, $this->selectedPermissions)) {
            $this->selectedPermissions = array_values(array_diff($this->selectedPermissions, [$permission]));
        } else {
            $this->selectedPermissions[] = $permission;
        }

        // Sync category toggle when individual permission is changed
        $this->syncCategoryToggles();
    }

    /**
     * Check if a permission is selected.
     */
    public function isPermissionSelected(string $permission): bool
    {
        return in_array($permission, $this->selectedPermissions);
    }

    /**
     * Get validation rules.
     */
    protected function rules(): array
    {
        $uniqueRule = $this->isEditMode()
            ? 'required|unique:roles,name,'.$this->roleId
            : 'required|unique:roles,name';

        return [
            'roleName' => $uniqueRule,
            'maxUsers' => 'nullable|integer|min:1',
            'selectedPermissions' => 'array',
        ];
    }

    /**
     * Get validation messages.
     */
    protected function messages(): array
    {
        return [
            'roleName.required' => 'Nama peran wajib diisi.',
            'roleName.unique' => 'Nama peran sudah digunakan.',
            'maxUsers.integer' => 'Batas pekerja harus berupa angka.',
            'maxUsers.min' => 'Batas pekerja minimal 1.',
        ];
    }

    /**
     * Save role (create or update).
     */
    public function save(): mixed
    {
        $this->validate();

        if ($this->isEditMode()) {
            return $this->updateRole();
        }

        return $this->createRole();
    }

    /**
     * Create new role.
     */
    protected function createRole(): mixed
    {
        $role = SpatieRole::create([
            'name' => $this->roleName,
            'max_users' => $this->maxUsers,
        ]);

        $role->syncPermissions($this->selectedPermissions);

        session()->flash('success', 'Peran berhasil ditambahkan!');

        return redirect()->route('role');
    }

    /**
     * Update existing role.
     */
    protected function updateRole(): mixed
    {
        $role = SpatieRole::findOrFail($this->roleId);

        $role->update([
            'name' => $this->roleName,
            'max_users' => $this->maxUsers,
        ]);

        $role->syncPermissions($this->selectedPermissions);

        session()->flash('success', 'Peran berhasil diperbarui!');

        return redirect()->route('role');
    }

    /**
     * Confirm delete role.
     */
    public function confirmDelete(): void
    {
        $this->alert('warning', 'Hapus Peran?', [
            'text' => 'Apakah Anda yakin ingin menghapus peran ini? Data yang dihapus tidak dapat dikembalikan.',
            'showConfirmButton' => true,
            'showCancelButton' => true,
            'confirmButtonText' => 'Ya, Hapus',
            'cancelButtonText' => 'Batal',
            'onConfirmed' => 'delete',
            'confirmButtonColor' => '#ef4444',
            'cancelButtonColor' => '#6b7280',
            'width' => '400',
            'padding' => '1.5rem',
            'toast' => false,
            'position' => 'center',
            'timer' => null,
        ]);
    }

    /**
     * Delete role.
     */
    public function delete(): mixed
    {
        $role = SpatieRole::find($this->roleId);

        if ($role) {
            // Check if role has users
            if ($role->users()->count() > 0) {
                $this->alert('error', 'Peran tidak dapat dihapus karena masih digunakan oleh pekerja.', [
                    'toast' => true,
                    'position' => 'top-end',
                ]);

                return null;
            }

            $role->delete();

            return redirect()->intended(route('role'))->with('success', 'Peran berhasil dihapus.');
        }

        return null;
    }

    /**
     * Get permission categories with details.
     */
    public function getPermissionCategoriesProperty(): array
    {
        return [
            'kasir' => [
                'label' => 'Kasir',
                'description' => 'Bagian yang melayani transaksi penjualan kepada pelanggan.',
                'permissions' => PermissionSeeder::getPermissionsByCategory()['kasir'],
            ],
            'produksi' => [
                'label' => 'Produksi (Koki)',
                'description' => 'Bagian yang bertugas memproduksi produk sesuai rencana dan permintaan.',
                'permissions' => PermissionSeeder::getPermissionsByCategory()['produksi'],
            ],
            'inventori' => [
                'label' => 'Inventori (Inventaris)',
                'description' => 'Bagian yang mengelola persediaan.',
                'permissions' => PermissionSeeder::getPermissionsByCategory()['inventori'],
            ],
            'manajemen' => [
                'label' => 'Manajemen',
                'description' => 'Bagian yang mengelola akses tambahan dan dapat mencakup keseluruhan sistem.',
                'permissions' => PermissionSeeder::getPermissionsByCategory()['manajemen'],
            ],
        ];
    }

    public function render()
    {
        return view('livewire.peran.form');
    }
}
