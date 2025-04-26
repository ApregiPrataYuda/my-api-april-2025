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

use App\Helpers\ApiResponse;

class Administrator extends Controller
{

    protected $Menu;
    public function __construct(Menu $Menu) {
        $this->Menu = $Menu;
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
                return response()->json(['error' => 'Your Request ID Category not found'], 404);
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
}
