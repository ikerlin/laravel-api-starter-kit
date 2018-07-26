<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Api\v1\ApiController;
use Carbon\Carbon;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Support\Facades\Auth;
use Laravel\Passport\Client;
use App\Models\User;
use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Validator;

class AuthenticateController extends ApiController
{
    use AuthenticatesUsers;

    public function __construct()
    {
        $this->middleware('auth:api')->only([
            'logout'
        ]);
    }

    public function username()
    {
        return 'email';
    }

    /**
     * 登录
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\Response|mixed
     */
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            $this->username() => 'required|exists:users',
            'password' => 'required|between:5,32',
        ]);

        if ($validator->fails()) {
            $request->request->add([
                'errors' => $validator->errors()->toArray(),
                'code' => 401,
            ]);
            return $this->sendFailedLoginResponse($request);
        }

        $credentials = $this->credentials($request);

        if ($this->guard('api')->attempt($credentials, $request->has('remember'))) {
            return $this->sendLoginResponse($request);
        }

        return $this->setStatusCode(401)->failed('登录失败');
    }

    /**
     * 退出登录
     *
     * @param Request $request
     * @return mixed
     */
    public function logout(Request $request)
    {
        if (Auth::guard('api')->check()) {
            $token = Auth::guard('api')->user()->token();

            \DB::table('oauth_refresh_tokens')->whereIn('access_token_id', [$token->id])
                ->delete();

            $token->delete();
        }

        return $this->message('退出登录成功');
    }

    /**
     * 调用认证接口获取授权码
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\Response
     */
    protected function authenticateClient(Request $request)
    {
        $credentials = $this->credentials($request);

        // 直接从数据库查更方便
        $password_client = Client::query()->where('password_client', 1)->latest()->first();

        $request->request->add([
            'grant_type' => 'password',
            'client_id' => $password_client->id,
            'client_secret' => $password_client->secret,
            'username' => $credentials[$this->username()],
            'password' => $credentials['password'],
            'scope' => ''
        ]);

        $proxy = Request::create(
            'api/oauth/token',
            'POST'
        );

        $response = \Route::dispatch($proxy);

        return $response;
    }

    protected function authenticated(Request $request)
    {
        return $this->authenticateClient($request);
    }

    protected function sendLoginResponse(Request $request)
    {
        $this->clearLoginAttempts($request);

        return $this->authenticated($request);
    }

    protected function sendFailedLoginResponse(Request $request)
    {
        $msg = $request['errors'];
        $code = $request['code'];
        return $this->setStatusCode($code)->failed($msg);
    }
}