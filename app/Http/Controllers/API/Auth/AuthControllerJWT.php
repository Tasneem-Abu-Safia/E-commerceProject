<?php
namespace App\Http\Controllers\API\Auth;
use App\Http\Controllers\API\apiResponseTrait;
use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Support\Facades\Validator;

class AuthControllerJWT extends Controller
{
    use apiResponseTrait;
    /**
     * Create a new AuthControllerJWT instance.
     *
     * @return void
     */
    public function __construct() {
        $this->middleware('auth:api', ['except' => ['login', 'register']]);
    }

    public function login(Request $request){
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required|string|min:6',
        ]);
        if ($validator->fails()) {
            return $this->apiResponse($validator->errors(),"fails",422);
        }
        if (! $token = auth()->attempt($validator->validated())) {
            return $this->apiResponse(['error' => 'Unauthorized'],"fails",401);
        }
        return $this->createNewToken($token);
    }

    public function register(Request $request) {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|between:2,100',
            'phone_number' => 'required|string|between:9,15',
            'email' => 'required|string|email|max:100|unique:users',
            'password' => 'required|string|min:6',
        ]);
        if($validator->fails()){
            return $this->apiResponse($validator->errors(),"fails",422);
        }
        $user = User::create(array_merge(
            $validator->validated(),
            ['password' => bcrypt($request->password)]
        ));
        return $this->apiResponse($user, 'User successfully registered',201);

    }

    public function logout() {
        auth()->logout();
        return $this->apiResponse([], 'User successfully signed out',201);

    }

    public function refresh() {
        return $this->createNewToken(auth()->refresh());
    }

    public function userProfile() {

        return $this->apiResponse(auth()->user(), 'UserProfile',201);
    }

    protected function createNewToken($token){
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth()->factory()->getTTL() * 120,
            'user' => auth()->user()
        ]);
    }
}
