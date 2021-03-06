<?php

namespace App\Http\Controllers\Admin;

use App\Repositories\MenuRepository;
use App\Repositories\UserRepository;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use App\Http\Controllers\ApiController;

class UserController extends ApiController
{
    protected $userRepository;
    private $menuRepository ;

    /**
     * UserController constructor.
     * @param UserRepository $userRepository
     * @param MenuRepository $menuRepository
     */
    public function __construct(UserRepository $userRepository,MenuRepository $menuRepository)
    {
        $this->userRepository = $userRepository;
        $this->menuRepository = $menuRepository;
    }

    /**
     * 后台管理用户登录
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse|mixed
     */
    public function login(Request $request)
    {
        $data = $request->all();
        $http = new Client();
        try {
            $response = $http->post(env("APP_URL") . '/oauth/token', [
                'form_params' => [
                    'grant_type' => 'password',
                    'client_id' => env("CLIENT_ID"),
                    'client_secret' => env("CLIENT_SECRET"),
                    'username' => array_get($data, 'username'),
                    'password' => array_get($data, 'password'),
                    'scope' => '*',
                ],
            ]);
            return $this->successResponse(json_decode((string)$response->getBody(), true));
        } catch (\Exception $e) {
            $status = $e->getCode();
            return $this->errorResponse('error'.$e->getMessage(), $status);
        }

    }

    /**
     * 刷新token
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse|mixed
     */
    public function refresh(Request $request)
    {
        $data = $request->all();
        $http = new Client();
        try {
            $response = $http->post(env("APP_URL") . '/oauth/token', [
                'form_params' => [
                    'grant_type' => 'refresh_token',
                    'client_id' => env("CLIENT_ID"),
                    'client_secret' => env("CLIENT_SECRET"),
                    'refresh_token' => array_get($data, 'refresh_token'),
                    'scope' => '*',
                ],
            ]);
            return $this->successResponse(json_decode((string)$response->getBody(), true));
        } catch (\Exception $e) {
            $status = $e->getCode();
            return $this->errorResponse('error'.$e->getMessage(), $status);
        }

    }


    /**
     * 用户列表
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        $data = $this->userRepository->pageToArray();
        return $this->successResponse($data);
    }


    /**
     * 当前登录用户信息
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function userInfo(Request $request)
    {
        $user = $request->user();
        $menus = $this->menuRepository->menuNameIds();
        return $this->successResponse(compact('user','menus'));
    }

    /**
     * 新增用户
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        $inputs = $request->all();
        $inputs['password'] = bcrypt(array_get($inputs, 'password', '123456'));
        $inputs['date'] = date('Y-m-d', strtotime(array_get($inputs, 'date')));
        $user = $this->userRepository->store($inputs);
        return $this->successResponse($user);
    }


    /**
     * 修改用户信息
     * @param Request $request
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $id)
    {
        $inputs = $request->all();

        $user = $this->userRepository->getById($id);

        if ($user) {

            $result = $this->userRepository->update($id, $inputs);

            if ($result) {
                return $this->successResponse();
            }

        } else {
            return $this->errorResponse('参数有误');
        }
        return $this->errorResponse('系统繁忙', 500);
    }

}
