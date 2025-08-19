<?php
namespace App\Services;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;
use App\Models\User;
use App\Interfaces\UserInterface;
class UserService implements UserInterface
{
  
  
    public function find(int $id): ?User
    {
        return User::where('id',$id)->where('is_active',1)->first();
    }
    public function findbyMobile(string $mobile_no): ?User
    {
        return User::where('mobile_no',$mobile_no)->where('is_active',1)->first();
    }
    public function isPasswordSet(int $userId): bool
    {
        $user=User::where('id',$userId)->where('is_active',1)->first();
        if(!is_null($user->password) && $user->password==''){
            return true;
        }
        else
        return true;
    }
    
    public function isDDOLogin(int $userId): bool
    {
       
        return true;
    }
    public function validPassword(int $userId,string $password): bool
    {
       
        $user=User::where('id',$userId)->where('is_active',1)->first();
        if( Hash::check($password, $user->password)){
            return true;
        }
        else
        return false;
    }
    public function isPasswordExpired(int $userId): bool
    {
        $current_timestamp = Carbon::now()->setTimezone('Asia/Kolkata')->format('Y-m-d H:i:s');
        $user=User::where('id',$userId)->where('is_active',1)->first();
       // dump($user->password_expires_at);dd($current_timestamp);
        if((strtotime($user->password_expires_at) > strtotime($current_timestamp))){
            return true;
        }
        else
        return false;
    }
    
    
}