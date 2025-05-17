<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\Menu;
use App\Http\Resources\MenuResource;
use App\Http\Requests\MenuIndexRequest;
use App\Http\Requests\validationMenu;
use App\Models\submenu;
use App\Http\Resources\SubmenuResource;
use App\Http\Requests\SubmenuIndexRequest;
use App\Http\Requests\validationSubmenu;
use App\Helpers\ApiResponse;
use App\Models\Role;
use App\Http\Requests\RoleIndexRequest;
use App\Http\Resources\RoleResource;
use App\Http\Requests\validationRole;
use App\Models\users;
use App\Http\Resources\UserResource;
use App\Http\Requests\userIndexRequest;
use App\Http\Requests\validationUser;
use App\Http\Requests\validationUserUpdate;

class Administrator extends Controller
{

    protected $Menu;
    protected $submenu;
    protected $Role;
    protected $users;
    public function __construct(users $users, Role $Role, Menu $Menu, submenu $submenu) {
        $this->Menu = $Menu;
        $this->submenu = $submenu;
        $this->Role = $Role;
        $this->users = $users;
    }

    
    public function indexMenu(MenuIndexRequest $request): JsonResponse 
    {
        // Parameter dari query string
        $validated = $request->validated();

        $search      = $validated['search'] ?? null;
        $perPage     = $validated['per_page'] ?? 10;
        $sortBy      = $validated['sort_by'] ?? 'created_at';
        $sortDir     = $validated['sort_dir'] ?? 'desc';
        $onlyDeleted = $validated['only_deleted'] ?? false;
    
        // Contoh URL penggunaan:
        // ?search=Kilogram              -> cari berdasarkan menu
        // ?per_page=20                 -> jumlah data per halaman
        // ?search=admin&per_page=10    -> pencarian + pagination
        // ?sort_by=menu&sort_dir=asc   -> sorting berdasarkan kolom
        // ?only_deleted=true           -> hanya tampilkan soft deleted
    
        $query = $this->Menu->query();

        if ($onlyDeleted) {
            $query->onlyTrashed();
        }
    
        if ($search) {
            $query->where('menu', 'like', '%' . $search . '%');
        }
    
        // Sorting dan pagination
        $menus = $query->orderBy($sortBy, $sortDir)->paginate($perPage);
    

        if ($menus->isEmpty()) {
            return ApiResponse::error('Data tidak ditemukan atau tidak tersedia', [
                'menu' => [],
                'pagination' => [
                    'total' => 0,
                    'per_page' => $perPage,
                    'current_page' => 1,
                    'last_page' => 1,
                    'next_page_url' => null,
                    'prev_page_url' => null,
                ]
            ], 404);
        }
    

        return ApiResponse::success('Success', [
            'menu' => MenuResource::collection($menus),
            'pagination' => [
                'total' => $menus->total(),
                'per_page' => $menus->perPage(),
                'current_page' => $menus->currentPage(),
                'last_page' => $menus->lastPage(),
                'next_page_url' => $menus->nextPageUrl(),
                'prev_page_url' => $menus->previousPageUrl(),
            ]
        ]);
    }

    public function showMenu(string $id)
    {
        $menus = $this->Menu->find($id);

    if (!$menus) {
        return response()->json(['error' => 'Your Request data not found'], 404);
    }
    return ApiResponse::success('Success', new MenuResource($menus), 200);
    // return response()->json([
    //     'message' => 'Success',
    //     'menu' => new MenuResource($menus)
    // ], 200);
    }

    public function storeMenu(validationMenu $request)  {

        $data = $request->validated();
            if ($this->Menu->where('menu', $data['menu'])->exists()) {
                throw new HttpResponseException(response()->json([
                    'errors' => [
                        'menu' => ['Nama Menu sudah tersedia.']
                    ]
                ], 400));
            }
    
            $Menu = $this->Menu->create([
                'menu' => $data['menu'],
            ]);
    
            return ApiResponse::success('Success Create New Menu', new MenuResource($Menu), 200);
             // return response()->json([
        //     'message' => 'Success',
        //     'menu' => new MenuResource($menus)
        // ], 200);
    }
    
    
    public function updateMenu(validationMenu $request, $id)  {
        $data = $request->validated();
    
            $Menu = $this->Menu->find($id);
        
            if (!$Menu) {
                return response()->json(['error' => 'Your Request ID menu not found'], 404);
            }
        
            $Menu->update($data);
            return ApiResponse::success('success updated menu', new MenuResource($Menu), 200);
        
            // return response()->json([
            //     'message' => 'Success updated menu',
            //     'menu' => new MenuResource($Menu)
            // ], 200);
    }
    
    public function destroyMenu(string $id)
    {
        // Cari kategori berdasarkan ID
    $Menu = $this->Menu->find($id);
    
    // Jika kategori tidak ditemukan, kembalikan response 404
    if (!$Menu) {
        return response()->json(['error' => 'Your Request Menu Not Found'], 404);
    }
    
    // Hapus kategori
    $Menu->delete();
    
    // Return response sukses
    return ApiResponse::success('Success Deleted Menu', new MenuResource($Menu), 200);
    // return response()->json([
    //     'message' => 'Success Deleted Menu'
    // ], 200);
    // }
    }





    // start api for submenu 
      public function indexSubMenu(SubmenuIndexRequest $request) {
         // Parameter dari query string
         $validated = $request->validated();

         $search      = $validated['search'] ?? null;
         $perPage     = $validated['per_page'] ?? 10;
         $sortBy      = $validated['sort_by'] ?? 'created_at';
         $sortDir     = $validated['sort_dir'] ?? 'desc';
         $onlyDeleted = $validated['only_deleted'] ?? false;
     
         // Contoh URL penggunaan:
         // ?search=Kilogram              -> cari berdasarkan menu
         // ?per_page=20                 -> jumlah data per halaman
         // ?search=admin&per_page=10    -> pencarian + pagination
         // ?sort_by=menu&sort_dir=asc   -> sorting berdasarkan kolom
         // ?only_deleted=true           -> hanya tampilkan soft deleted
     
        //  $query = $this->submenu->query();
        $query = $this->submenu
        ->select(
            'ms_submenu.*',
            'ms_submenu.title as titles',
            'ms_menu.menu',
            'parent_submenu.title AS parent_menu_name'
        )
        ->leftJoin('ms_menu', 'ms_submenu.id_menu', '=', 'ms_menu.id_menu')
        ->leftJoin('ms_submenu AS parent_submenu', 'ms_submenu.parent_id', '=', 'parent_submenu.id_submenu');
 
    

 
         if ($onlyDeleted) {
             $query->onlyTrashed();
         }
     
         if ($search) {
             $query->where('title', 'like', '%' . $search . '%');
         }
     
         // Sorting dan pagination
         $submenus = $query->orderBy($sortBy, $sortDir)->paginate($perPage);
     
 
         if ($submenus->isEmpty()) {
             return ApiResponse::error('Data tidak ditemukan atau tidak tersedia', [
                 'submenu' => [],
                 'pagination' => [
                     'total' => 0,
                     'per_page' => $perPage,
                     'current_page' => 1,
                     'last_page' => 1,
                     'next_page_url' => null,
                     'prev_page_url' => null,
                 ]
             ], 404);
         }
     
 
         return ApiResponse::success('Success', [
             'submenu' => SubmenuResource::collection($submenus),
             'pagination' => [
                 'total' => $submenus->total(),
                 'per_page' => $submenus->perPage(),
                 'current_page' => $submenus->currentPage(),
                 'last_page' => $submenus->lastPage(),
                 'next_page_url' => $submenus->nextPageUrl(),
                 'prev_page_url' => $submenus->previousPageUrl(),
             ]
         ]);
      }


      public function showSubMenu(string $id)
      {

        try {
            $submenus = $this->submenu->find($id);
            if (!$submenus) {
                return response()->json(['error' => 'Your Request data not found'], 404);
            }
            return ApiResponse::success('Success', new SubmenuResource($submenus), 200);
        } catch (\Exception $e) {
            return ApiResponse::error('Failed to Create SubMenu', $e->getMessage(), 500);
        }
          
      
      }



      public function storeSubMenu(validationSubmenu $request) {
        $data = $request->validated();
        if ($this->submenu->where('title', $data['title'])->exists()) {
            throw new HttpResponseException(response()->json([
                'errors' => [
                    'title' => ['Title sudah tersedia.']
                ]
            ], 400));
        }

        try {
            $subMenu = $this->submenu->create([
                'id_menu' => $data['id_menu'],
                'title' => $data['title'],
                'url' => $data['url'],
                'icon' => $data['icon'] ?? null, // pakai null kalau kosong
                'noted' => $data['noted'] ?? null,
                'is_active' => $data['is_active'],
                'parent_id' => $data['parent_id'] ?? 0,
            ]);
    
            return ApiResponse::success('Success Create New SubMenu', new SubmenuResource($subMenu), 201);
        } catch (\Exception $e) {
            return ApiResponse::error('Failed to Create SubMenu', $e->getMessage(), 500);
        }
      }



      public function updateSubMenu(validationSubmenu $request, $id)  {
        $data = $request->validated();
    
            $SubMenu = $this->submenu->find($id);
        
            if (!$SubMenu) {
                return response()->json(['error' => 'Your Request ID Submenu not found'], 404);
            }
        

            try {
                $SubMenu->update($data);
                return ApiResponse::success('success updated Submenu', new SubmenuResource($SubMenu), 200);
            } catch (\Exception $e) {
                return ApiResponse::error('Failed to updated SubMenu', $e->getMessage(), 500);
            }
    }



    public function destroySubMenu(string $id)
    {
        try {
            $SubMenu = $this->submenu->find($id);
            // Jika submenu tidak ditemukan, kembalikan response 404
            if (!$SubMenu) {
                return response()->json(['error' => 'Your Request Menu Not Found'], 404);
            }
            // Hapus kategori
            $SubMenu->delete();
            // Return response sukses
            return ApiResponse::success('Success Deleted SubMenu', new SubmenuResource($SubMenu), 200);
        } catch (\Exception $e) {
            return ApiResponse::error('Failed to updated SubMenu', $e->getMessage(), 500);
        }
     
    
   
    }

    // end api for submenu 






    // start api for role

    public function indexRole(RoleIndexRequest $request)  {
        // Parameter dari query string
        $validated = $request->validated();

        $search      = $validated['search'] ?? null;
        $perPage     = $validated['per_page'] ?? 10;
        $sortBy      = $validated['sort_by'] ?? 'created_at';
        $sortDir     = $validated['sort_dir'] ?? 'desc';
        $onlyDeleted = $validated['only_deleted'] ?? false;
    
    
        $query = $this->Role->query();

        if ($onlyDeleted) {
            $query->onlyTrashed();
        }
    
        if ($search) {
            $query->where('role', 'like', '%' . $search . '%');
        }
    
        // Sorting dan pagination
        $role = $query->orderBy($sortBy, $sortDir)->paginate($perPage);
    

        if ($role->isEmpty()) {
            return ApiResponse::error('Data tidak ditemukan atau tidak tersedia', [
                'role' => [],
                'pagination' => [
                    'total' => 0,
                    'per_page' => $perPage,
                    'current_page' => 1,
                    'last_page' => 1,
                    'next_page_url' => null,
                    'prev_page_url' => null,
                ]
            ], 404);
        }
    

        return ApiResponse::success('Success', [
            'role' => RoleResource::collection($role),
            'pagination' => [
                'total' => $role->total(),
                'per_page' => $role->perPage(),
                'current_page' => $role->currentPage(),
                'last_page' => $role->lastPage(),
                'next_page_url' => $role->nextPageUrl(),
                'prev_page_url' => $role->previousPageUrl(),
            ]
        ]);
    }


    public function showRole(string $id)
    {
        $role = $this->Role->find($id);
        if (!$role) {
            return response()->json(['error' => 'Your Request data not found'], 404);
        }
        return ApiResponse::success('Success', new RoleResource($role), 200);
    }


    public function storeRole(validationRole $request)  {

        $data = $request->validated();
            if ($this->Role->where('role', $data['role'])->exists()) {
                throw new HttpResponseException(response()->json([
                    'errors' => [
                        'role' => ['Nama Role sudah tersedia.']
                    ]
                ], 400));
            }
    
            $Role = $this->Role->create([
                'role' => $data['role'],
            ]);
    
            return ApiResponse::success('Success Create New Role', new RoleResource($Role), 200);
    }


    public function updateRole(validationRole $request, $id)  {
        $data = $request->validated();
    
            $Role = $this->Role->find($id);
        
            if (!$Role) {
                return response()->json(['error' => 'Your Request ID role not found'], 404);
            }
        
            $Role->update($data);
            return ApiResponse::success('success updated Role', new RoleResource($Role), 200);
    }


    public function destroyRole(string $id)
    {
        // Cari kategori berdasarkan ID
    $Role = $this->Role->find($id);
    
    // Jika kategori tidak ditemukan, kembalikan response 404
    if (!$Role) {
        return response()->json(['error' => 'Your Request Role Not Found'], 404);
    }
    
    // Hapus role
    $Role->delete();
    
    // Return response sukses
    return ApiResponse::success('Success Deleted Role', new RoleResource($Role), 200);
   
    }
 // start api for role



 // start api for role

    public function indexUser(userIndexRequest $request) {

        $validated = $request->validated();

        $search      = $validated['search'] ?? null;
        $perPage     = $validated['per_page'] ?? 10;
        $sortBy      = $validated['sort_by'] ?? 'created_at';
        $sortDir     = $validated['sort_dir'] ?? 'desc';
        $onlyDeleted = $validated['only_deleted'] ?? false;
    
    
        $query = $this->users
        ->select('users.*','ms_divisi.nama as nama_divisi','ms_role.role','ms_group_company.name_group')
        ->leftJoin('ms_role', 'users.role_id', '=', 'ms_role.id_role')
        ->leftJoin('ms_group_company', 'users.id_group', '=', 'ms_group_company.id_group')
        ->leftJoin('ms_divisi', 'users.divisi_id', '=', 'ms_divisi.id');
       

        if ($onlyDeleted) {
            $query->onlyTrashed();
        }
    
        if ($search) {
            $query->where('fullname', 'like', '%' . $search . '%');
        }
    
        // Sorting dan pagination
        $users = $query->orderBy($sortBy, $sortDir)->paginate($perPage);
    

        if ($users->isEmpty()) {
            return ApiResponse::error('Data tidak ditemukan atau tidak tersedia', [
                'user' => [],
                'pagination' => [
                    'total' => 0,
                    'per_page' => $perPage,
                    'current_page' => 1,
                    'last_page' => 1,
                    'next_page_url' => null,
                    'prev_page_url' => null,
                ]
            ], 404);
        }
    

        return ApiResponse::success('Success', [
            'user' => UserResource::collection($users),
            'pagination' => [
                'total' => $users->total(),
                'per_page' => $users->perPage(),
                'current_page' => $users->currentPage(),
                'last_page' => $users->lastPage(),
                'next_page_url' => $users->nextPageUrl(),
                'prev_page_url' => $users->previousPageUrl(),
            ]
        ]);
    }



   public function showUser(string $id)
      {
        try {
            $users = $this->users->find($id);
            if (!$users) {
                return response()->json(['error' => 'Your Request data not found'], 404);
            }
            return ApiResponse::success('Success', new UserResource($users), 200);
        } catch (\Exception $e) {
            return ApiResponse::error('Failed to Show user', $e->getMessage(), 500);
        }
      }

      public function storeUser(validationUser $request)  {
        $validated = $request->validated();

        // handle image
        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $filename = time() . '_' . $file->getClientOriginalName();
            $file->storeAs('users', $filename, 'public');
            $image = $filename;
        } else {
            $image = 'default.jpg';
        }

        $user = $this->users->create([
            'fullname' => $validated['fullname'],
            'username' => $validated['username'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role_id' => $validated['role_id'],
            'id_group' => $validated['id_group'],
            'divisi_id' => $validated['divisi_id'],
            'is_active' => $validated['is_active'],
            'image' => $image, 
        ]);

        $userWithRelations = $this->users
            ->select('users.*', 'ms_divisi.nama as nama_divisi', 'ms_role.role', 'ms_group_company.name_group')
            ->leftJoin('ms_role', 'users.role_id', '=', 'ms_role.id_role')
            ->leftJoin('ms_group_company', 'users.id_group', '=', 'ms_group_company.id_group')
            ->leftJoin('ms_divisi', 'users.divisi_id', '=', 'ms_divisi.id')
            ->where('users.id_user', $user->id_user) // ambil data yang baru dibuat
            ->first();
        return ApiResponse::success('Success Create New User', new UserResource($userWithRelations), 200);
      }



// ini adalah controller update oke yang perlu di perhatikan saat mencoba update data di postman
// entry point di postman tetap pake post 
// di route juga sama pake post
// untuk update yang ada gambar kamu harus pake form-data ga bisa klo pake raw
// --Body-Form data- dan masukan key dan valuenya
//emang sedikit aneh dari cara routenya tapi ini fix problem klo pake put ga bisa saya sudah 2 hari cari solusinya
public function updateUser(validationUserUpdate $request, $id)
{
    $validated = $request->validated();
    $user = $this->users->findOrFail($id);

    $oldimages = $user->image;
    $newImageFile = $request->file('image');
    // dd($request->all(), $request->file('image'));

    if ($newImageFile) {
        $filename = time() . '_' . $newImageFile->getClientOriginalName();
        $newImageFile->storeAs('users', $filename, 'public');
        $imageName = $filename;
 
        // Hapus gambar lama jika ada dan bukan gambar default
        if ($oldimages && $oldimages !== 'default.jpg') {
            $imagePath = storage_path('app/public/users/' . $oldimages);
            if (file_exists($imagePath)) {
                unlink($imagePath);
            }
        }
    } else {
        // Jika tidak ada gambar baru, gunakan gambar lama
        $imageName = $oldimages;
    }

    // Handle password
    if (isset($validated['password'])) {
        $validated['password'] = Hash::make($validated['password']);
    } else {
        $validated['password'] = $user->password;
    }

    // Gabungkan data default dan yang di-update
    $data = array_merge([
        'fullname'   => $user->fullname,
        'username'   => $user->username,
        'email'      => $user->email,
        'role_id'    => $user->role_id,
        'id_group'   => $user->id_group,
        'divisi_id'  => $user->divisi_id,
        'is_active'  => $user->is_active,
    ], $validated);

    $data['image'] = $imageName;

    $user->update($data);

    $userWithRelations = $this->users
        ->select('users.*', 'ms_divisi.nama as nama_divisi', 'ms_role.role', 'ms_group_company.name_group')
        ->leftJoin('ms_role', 'users.role_id', '=', 'ms_role.id_role')
        ->leftJoin('ms_group_company', 'users.id_group', '=', 'ms_group_company.id_group')
        ->leftJoin('ms_divisi', 'users.divisi_id', '=', 'ms_divisi.id')
        ->where('users.id_user', $user->id_user)
        ->first();

    return ApiResponse::success('Success Update User', new UserResource($userWithRelations), 200);
}





public function destroyUser(string $id)
{
    try {
        $user = $this->users->find($id);

        // Jika user tidak ditemukan, langsung return 404
        if (!$user) {
            return response()->json(['error' => 'Your Request user Not Found'], 404);
        }

        // Cek dan hapus gambar kalau bukan default
        $getimageurl = $user->image;

        if ($getimageurl && $getimageurl !== 'default.jpg') {
            // Path file di storage/app/public/users/ atau sesuai config kamu
            $imagePath = storage_path('app/public/users/' . $getimageurl);

            if (file_exists($imagePath)) {
                unlink($imagePath);
            }
        }

        // Hapus user
        $user->delete();

        return ApiResponse::success('Success Deleted User', new UserResource($user), 200);
    } catch (\Exception $e) {
        return ApiResponse::error('Failed to delete user', $e->getMessage(), 500);
    }
}



// public function destroyUser(string $id)
// {
//     try {
//         $user = $this->users->find($id);

//         $getimageurl = $user->image;
        
//         if ($getimageurl && $getimageurl !== 'default.jpg') {
//             // Menentukan path file gambar di storage/app/avatar
//             $imagePath = storage_path('app/users/' . $getimageurl);
        
//             // Memastikan gambar ada di folder tersebut dan menghapusnya
//             if (file_exists($imagePath)) {
//                 unlink($imagePath);
//             }
//         }



//         // Jika submenu tidak ditemukan, kembalikan response 404
//         if (!$user) {
//             return response()->json(['error' => 'Your Request user Not Found'], 404);
//         }
//         // Hapus kategori
//         $user->delete();
//         // Return response sukses
//         return ApiResponse::success('Success Deleted User', new UserResource($user), 200);
//     } catch (\Exception $e) {
//         return ApiResponse::error('Failed to updated User', $e->getMessage(), 500);
//     }
 


// }

}
