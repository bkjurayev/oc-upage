<?php

use RainLab\User\Models\User as UserModel;
use GuzzleHttp\Client;
use Itmaker\Upage\Models\PhoneVerification;


Route::group(['prefix' => 'api'], function () {
    
    // start
    Route::get('/', 'itmaker\upage\controllers\Api@index');


    // Categories
    Route::get('categories', 'itmaker\upage\controllers\Api@getCategories');
    Route::get('category/{id}', 'itmaker\upage\controllers\Api@getCategory');
    Route::get('category/{id}/companies', 'itmaker\upage\controllers\Api@getCompaniesByCategory');


    // Posts
    Route::get('posts', 'itmaker\upage\controllers\Api@getPosts');

    // Interestings
    Route::get('interestings', 'itmaker\upage\controllers\Api@getInterestings');
    
    //Cities
    Route::get('cities', 'itmaker\upage\controllers\Api@getCities');
    
    //Locations
    Route::get('districts', 'itmaker\upage\controllers\Api@getDistricts');
    
    //Reviews
    Route::get('review/{company}', 'itmaker\upage\controllers\Api@getReviewsByCompany');
    Route::post('review', 'itmaker\upage\controllers\Api@store');
    
    //Companies
    Route::get('companies', 'itmaker\upage\controllers\Api@getCompanies');
    Route::get('company/{id}', 'itmaker\upage\controllers\Api@show');
    
    /**
     * Favorites
     */
    Route::get('favorites', 'itmaker\upage\controllers\Api@favorites')->middleware('\Tymon\JWTAuth\Middleware\GetUserFromToken');
    Route::post('favorites', 'itmaker\upage\controllers\Api@associate')->middleware('\Tymon\JWTAuth\Middleware\GetUserFromToken');
    Route::delete('favorites', 'itmaker\upage\controllers\Api@detach');
    
    Route::post('test', function (\Request $request) {
        return $response->json(('ok!'));
    })->middleware('\Tymon\JWTAuth\Middleware\GetUserFromToken');
    
});



Route::group(['prefix' => 'api/user'], function() {
    Route::post('login', function (\Request $request) {
        
        if (! Request::get('email')) {
            $credentials = [
                'email' => Request::get('phone') . '@testmail.uz'
            ];
        } else {
            $credentials = [
                'email' => Request::get('email')
            ];
            
        }
        
        $credentials['password'] = Request::get('password');
        

            return $credentials;
        
        
        try {
            // verify the credentials and create a token for the user
            if (! $token = JWTAuth::attempt($credentials)) {
                return response()->json(['error' => 'invalid_credentials'], 401);
            }
        } catch (JWTException $e) {
            // something went wrong
            return response()->json(['error' => 'could_not_create_token'], 500);
        }
        $userModel = JWTAuth::authenticate($token);
        if ($userModel->methodExists('getAuthApiSigninAttributes')) {
            $user = $userModel->getAuthApiSigninAttributes();
        } else {
            $user = [
                'id' => $userModel->id,
                'name' => $userModel->name,
                'surname' => $userModel->surname,
                'username' => $userModel->username,
                'email' => $userModel->email,
                'is_activated' => $userModel->is_activated,
            ];
        }
        // if no errors are encountered we can return a JWT
        return response()->json(compact('token', 'user'));
    });
    
    
    Route::post('refresh', function (Request $request) {
        $token = Request::get('token');
        try {
            // attempt to refresh the JWT
            if (!$token = JWTAuth::refresh($token)) {
                return response()->json(['error' => 'could_not_refresh_token'], 401);
            }
        } catch (Exception $e) {
            // something went wrong
            return response()->json(['error' => 'could_not_refresh_token'], 500);
        }
        // if no errors are encountered we can return a new JWT
        return response()->json(compact('token'));
    });
    
    Route::post('invalidate', function (Request $request) {
        $token = Request::get('token');
        try {
            // invalidate the token
            JWTAuth::invalidate($token);
        } catch (Exception $e) {
            // something went wrong
            return response()->json(['error' => 'could_not_invalidate_token'], 500);
        }
        // if no errors we can return a message to indicate that the token was invalidated
        return response()->json('token_invalidated');
    });
    
    Route::post('signup', function () {
        $credentials = Input::only('name', 'phone', 'email', 'password', 'password_confirmation');
        
        if (!isset($credentials['email'])) {
            $credentials['email'] = $credentials['phone'] . '@testmail.uz';

        }
        
        try {
            $userModel = UserModel::create($credentials);
            
            if ($userModel->methodExists('getAuthApiSignupAttributes')) {
                $user = $userModel->getAuthApiSignupAttributes();
            } else {
                $user = [
                    'id' => $userModel->id,
                    'name' => $userModel->name,
                    'surname' => $userModel->surname,
                    'username' => $userModel->username,
                    'phone' => $userModel->phone,
                    'email' => $userModel->email,
                    'is_activated' => $userModel->is_activated,
                ];
            }
            
        } catch (Exception $e) {
            return Response::json(['error' => $e->getMessage()], 401);
        }
        
        $token = JWTAuth::fromUser($userModel);
        
        /*
        **
        **  Phone verification
        **
        */

        if ($user['email'] == $user['phone'] . '@testmail.uz') {
            $message = PhoneVerification::sendMessage([
                'user_id'    => $user['id'],
                'user_phone' => $user['phone']
            ]);
        } else {
            $message = 'email authentication'; 
            
            $userModel->activation_code = $code = implode('-', [$userModel->id, rand(1111, 9999)]);
            $userModel->save();

            $data = [
                'name' => $userModel->name,
                'code' => $code
            ];

            \Mail::send('itmaker.upage::mail.activate', $data, function($message) use ($userModel) {
                $message->to($userModel->email, $userModel->name);
            });
        }
        

        return Response::json(compact('token', 'user', 'message'));
    });
    
    
    Route::post('phone-verification', 'itmaker\upage\controllers\Api@phoneVerification')->middleware('\Tymon\JWTAuth\Middleware\GetUserFromToken');
    Route::post('email-verification', 'itmaker\upage\controllers\Api@emailVerification')->middleware('\Tymon\JWTAuth\Middleware\GetUserFromToken');
    
});



