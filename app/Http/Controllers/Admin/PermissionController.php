<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\ApiController;
use App\Repositories\PermissionRepository;
use Illuminate\Http\Request;


class PermissionController extends ApiController
{
    private $permissionRepository ;

    public function __construct(PermissionRepository $permissionRepository)
    {
        $this->permissionRepository = $permissionRepository;
    }

    public function index(Request $request)
    {
        $number = $request->input('number',10);
        $data = $this->permissionRepository->pageToArray($number);
        return  $this->successResponse($data);
    }

    public function store(Request $request)
    {
        $inputs = $request->only(['title','sub_title','name']);
        $data = $this->permissionRepository->store($inputs);
        return $this->successResponse($data);
    }

    public function update(Request $request)
    {
        $id = $request->input('id');
        $inputs = $request->only('title','sub_title','name');
        $data = $this->permissionRepository->update($id,$inputs);
        return $this->successResponse($data);

    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(Request $request)
    {
        $id = $request->input('id');
        $data = $this->permissionRepository->destroy($id);
        return $this->successResponse($data);
    }
}
