<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\Request;
use App\Models\Menu;
use App\Http\Resources\MenuResource;
use App\Http\Requests\MenuIndexRequest;
use App\Http\Requests\validationMenu;
use App\Models\submenu;
use App\Http\Resources\SubmenuResource;
use App\Http\Requests\SubmenuIndexRequest;
use App\Http\Requests\validationSubmenu;



use App\Helpers\ApiResponse;

class Administrator extends Controller
{

    protected $Menu;
    protected $submenu;
    public function __construct(Menu $Menu, submenu $submenu) {
        $this->Menu = $Menu;
        $this->submenu = $submenu;
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
     
         $query = $this->submenu->query();
 
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
            // Jika kategori tidak ditemukan, kembalikan response 404
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
}
