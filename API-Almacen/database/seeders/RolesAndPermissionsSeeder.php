<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role as SpatieRole;
use App\Models\User;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\DB;

class RolesAndPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Limpia la caché de permisos de Spatie
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Opcional: Elimina roles y permisos existentes de Spatie si quieres empezar limpio cada vez que corres este seeder
        DB::table('role_has_permissions')->delete();
        DB::table('model_has_roles')->delete();
        DB::table('model_has_permissions')->delete();
        SpatieRole::query()->delete();
        Permission::query()->delete();

        // Gestión de Proveedores
        Permission::firstOrCreate(['name' => 'manage-suppliers']);

        // Gestión de Colores
        Permission::firstOrCreate(['name' => 'manage-colors']);

        // Gestión de Productos
        Permission::firstOrCreate(['name' => 'manage-products']); // Registrar/Editar/Eliminar
        Permission::firstOrCreate(['name' => 'generate-barcode-product']);
        Permission::firstOrCreate(['name' => 'import-products']);

        // Categoria de Productos
        Permission::firstOrCreate(['name' => 'manage-product-categories']);

        // Gestión de Materiales
        Permission::firstOrCreate(['name' => 'manage-materials']); // Registrar/Editar/Eliminar
        Permission::firstOrCreate(['name' => 'generate-barcode-material']);
        Permission::firstOrCreate(['name' => 'import-materials']);

        // Categoria de Materiales
        Permission::firstOrCreate(['name' => 'manage-material-categories']);

        // Inventario
        Permission::firstOrCreate(['name' => 'register-inventory']);
        Permission::firstOrCreate(['name' => 'perform-physical-count']);

        // Inventario Movimientos
        Permission::firstOrCreate(['name' => 'register-inventory-movement']);

        // Aprobaciones
        Permission::firstOrCreate(['name' => 'view-approvals-history']);

        // Reportes y Análisis
        Permission::firstOrCreate(['name' => 'view-sales-reports']); // Incluye Productos más rentables, Análisis de Rentabilidad
        Permission::firstOrCreate(['name' => 'view-stock-reports']); // Reporte de Stock por Sucursal

        // Sucursales
        Permission::firstOrCreate(['name' => 'manage-branches']); // Registrar/Editar/Eliminar

        // Usuario y Roles
        Permission::firstOrCreate(['name' => 'manage-users']); // Registrar/Editar/Eliminar
        Permission::firstOrCreate(['name' => 'manage-roles']); // Registrar/Editar/Eliminar

        // --- 1. Crea Roles usando el modelo de Spatie ---
        $rolAdmin = SpatieRole::firstOrCreate(['name' => 'Administrador']);
        $rolAlmacenista = SpatieRole::firstOrCreate(['name' => 'Almacenista']);
        $rolVendedor = SpatieRole::firstOrCreate(['name' => 'Vendedor']);

        // --- 2. Asigna Permisos a Roles ---
        // Administrador: Tiene todos los permisos.
        $rolAdmin->givePermissionTo(Permission::all()); // Asigna *todos* los permisos existentes

        // Almacenista: Permisos específicos
        $rolAlmacenista->givePermissionTo([
            'manage-material-categories',
        ]);

        // Vendedor: Permisos específicos
        $rolVendedor->givePermissionTo([
            'register-inventory',
        ]);

        // --- 3. Asigna Roles a Usuarios ---
        $adminUser = User::where('email', 'admin@test.com')->first();
        if ($adminUser) {
            $adminUser->assignRole('Administrador');
        }

        $almacenUser = User::where('email', 'almacen@test.com')->first();
        if ($almacenUser) {
            $almacenUser->assignRole('Almacenista');
        }
    }
}
