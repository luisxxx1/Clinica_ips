<?php

namespace App\Http\Controllers;

use App\Models\{User, Role, Setting}; // Asegúrate de crear el modelo Setting
use Illuminate\Http\Request;
use Illuminate\Support\Facades\{Auth, Storage, Artisan};

class AdminSettingsController extends Controller
{
    /**
     * Muestra el panel principal de administración.
     */
    public function index()
    {
        // El middleware 'role:Administrador' en las rutas ya hace este trabajo,
        // pero dejarlo aquí es una buena segunda capa de seguridad.
        if (Auth::user()->role->name !== 'Administrador') {
            abort(403, 'Acceso denegado.');
        }

        return view('admin.settings', [
            'users' => User::with('role')->get(),
            'roles' => Role::all(),
            // Recuperamos los ajustes de la BD como un array clave => valor
            'settings' => Setting::pluck('value', 'key')->toArray() 
        ]);
    }

    /**
     * ACTUALIZA LOS DATOS DEL USUARIO (Nombre, Rol, Cargo, Color)
     */
    public function updateUser(Request $request, User $user)
    {
        $request->validate([
            'name'      => 'required|string|max:255',
            'role_id'   => 'required|exists:roles,id',
            'job_title' => 'nullable|string|max:100',
            'ui_color'  => 'nullable|string|max:7', // Hexadecimal
        ]);

        $user->update($request->only(['name', 'role_id', 'job_title', 'ui_color']));

        return back()->with('success', "Configuración de {$user->name} actualizada.");
    }

    /**
     * ACTUALIZA EL BRANDING (Logo y Nombre de la I.P.S.) Persistente
     */
    public function updateBranding(Request $request)
    {
        $request->validate([
            'business_name' => 'required|string|max:100',
            'logo'          => 'nullable|image|mimes:png,jpg,jpeg|max:2048',
        ]);

        // Guardar nombre de la IPS de forma persistente
        Setting::updateOrCreate(['key' => 'business_name'], ['value' => $request->business_name]);

        if ($request->hasFile('logo')) {
            // 1. Obtener logo anterior de la BD
            $oldPath = Setting::where('key', 'logo_path')->value('value');
            
            // 2. Eliminar archivo físico si existe
            if ($oldPath && Storage::disk('public')->exists($oldPath)) {
                Storage::disk('public')->delete($oldPath);
            }

            // 3. Guardar el nuevo logo (en la carpeta public/branding)
            $path = $request->file('logo')->store('branding', 'public');
            
            // 4. Guardar la ruta en la base de datos
            Setting::updateOrCreate(['key' => 'logo_path'], ['value' => $path]);
        }

        return back()->with('success', 'Identidad corporativa actualizada correctamente.');
    }

    /**
     * RESTABLECER ACCESOS
     */
    public function resetAccess()
    {
        // Invalidar tokens de sesión y limpiar caché de seguridad
        User::where('id', '!=', Auth::id())->update(['remember_token' => null]);
        Artisan::call('cache:clear');
        
        return back()->with('success', 'Sesiones externas invalidadas y caché del sistema limpia.');
    }

    /**
     * REVOCAR PERMISOS
     */
    public function revokePermissions(User $user)
    {
        if ($user->id === Auth::id()) {
            return back()->with('error', 'No puedes revocar tus propios accesos.');
        }

        // Asignamos el rol por defecto (ID 3 suele ser el más bajo)
        $user->update(['role_id' => 3]); 
        
        return back()->with('success', "Se han limitado los permisos de {$user->name}.");
    }
}