<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'password' => 'hashed',
    ];

    public function setPasswordAttribute($value)
    {
        $this->attributes['password'] = bcrypt($value);
    }

    public function getAllUsers()
    {
        $results = DB::select("
            SELECT * from users;
        ");

        foreach ($results as $k => $result) {
            $results[$k] = (array) $result;
        }

        return $results;
    }

    public function getCurrentUserID() {
        return Auth::id();
    }

    public function isCurrentUserAdmin() {
        $user_id = $this->getCurrentUserID();
        $results = DB::select(
            "SELECT is_admin
                   FROM users
                   WHERE id = ?",
            [
                $user_id
            ]
        );

        return $results[0]->is_admin;
    }

    public function getUserDetails($user_id)
    {
        $results = DB::select(
            "SELECT name, phone, is_admin
                   FROM users
                   WHERE id = ?",
            [
                $user_id
            ]
        );

        return $results[0];
    }

    public function isUniqueUsername($checked_username)
    {
        if (ctype_alnum($checked_username))
        {
            $count = DB::table('users')->where('name', '=', $checked_username)->count();
            if ($count > 0)
            {
                return 0;
            }
            else
            {
                return 1;
            }
        }

        return 0;
    }



    public function insertNewUser($new_username, $new_password, $new_phone_number, $new_is_admin) {
        DB::table('users')->insert([
            'name' => $new_username,
            'password' => bcrypt($new_password),
            'phone' => $new_phone_number,
            'is_admin' => $new_is_admin
        ]);
    }

    public function editUser($edited_user_id, $new_username, $new_password, $new_phone_number, $new_is_admin, $keep_old_password) {
        if ($keep_old_password)
        {
            DB::table('users')
                ->where('id', '=', $edited_user_id)
                ->update([
                    'name' => $new_username,
                    'phone' => $new_phone_number,
                    'is_admin' => $new_is_admin
                ]);
        }
        else
        {
            DB::table('users')
                ->where('id', '=', $edited_user_id)
                ->update([
                    'name' => $new_username,
                    'password' => bcrypt($new_password),
                    'phone' => $new_phone_number,
                    'is_admin' => $new_is_admin
                ]);
        }
    }

    public function deleteUser($id_to_delete) {
        DB::table('users')->where('id', $id_to_delete)->delete();
    }
}
