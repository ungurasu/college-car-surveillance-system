<?php

namespace App\Http\Controllers;

use App\Models\RecordedEvent;
use App\Models\User as UserModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Response;

class AppController extends Controller
{
    public function dashboard()
    {
        $recorded_event_model = new RecordedEvent();

        $end_datetime = $recorded_event_model->getCurrentTimeDB();
        $start_datetime = date('Y-m-d 00:00:00', strtotime('-1 week'));

        $events = $recorded_event_model->filterRecordedEvents($start_datetime, $end_datetime);

        return view('dashboard', [
            'events' => $events,
            'end_datetime' => $end_datetime,
            'start_datetime' => $start_datetime
        ]);
    }

    public function filter(Request $request)
    {
        $recorded_event_model = new RecordedEvent();

        $start_datetime = $request->start_date." ".$request->start_hour.":".$request->start_minute.":00";
        $end_datetime   = $request->end_date." ".$request->end_hour.":".$request->end_minute.":00";

        $events = $recorded_event_model->filterRecordedEvents($start_datetime, $end_datetime);

        return view(
            'dashboard',
            [
                'events' => $events,
                'end_datetime' => $end_datetime,
                'start_datetime' => $start_datetime
            ]
        );
    }

    public function accountsPage(Request $request)
    {
        $users_model = new UserModel();

        $users_list = $users_model->getAllUsers();
        $user_id = $users_model->getCurrentUserID();
        $user_is_admin = $users_model->isCurrentUserAdmin();

        return view(
            'accounts',
            [
                'users_list' => $users_list,
                'user_is_admin' => $user_is_admin,
                'user_id' => $user_id
            ]
        );
    }

    public function newAccountForm(Request $request)
    {
        $users_model = new UserModel();

        $user_is_admin = $users_model->isCurrentUserAdmin();

        if (!$user_is_admin)
        {
            return redirect('/dashboard');
        }

        return view('accountform');
    }

    public function editAccountForm(Request $request)
    {
        $users_model = new UserModel();

        $user_is_admin = $users_model->isCurrentUserAdmin();

        if (!$user_is_admin || !isset($request->user_id) || $request->user_id == $users_model->getCurrentUserID())
        {
            return redirect('/dashboard');
        }

        $user_details = $users_model->getUserDetails($request->user_id);

        return view('accountform',
            [
                'is_edit' => 1,
                'new_username' => $user_details->name,
                'new_is_admin' => $user_details->is_admin,
                'new_phone_number' => $user_details->phone,
                'edited_user_id' => $request->user_id
            ]
        );
    }

    public function showVideo(string $video)
    {
        $path = storage_path('app/videos/' . $video);

        if (!File::exists($path))
        {
            abort(404);
        }

        $file = File::get($path);
        $type = File::mimeType($path);

        $response = Response::make($file, 200);
        $response->header("Content-Type", $type);

        return $response;
    }

    public function deleteEvent(Request $request)
    {
        $users_model = new UserModel();
        $user_is_admin = $users_model->isCurrentUserAdmin();

        if (!$user_is_admin || !isset($request->event_id))
        {
            return redirect('/dashboard');
        }

        $recorded_event_model = new RecordedEvent();
        $event_details = $recorded_event_model->getRecordedEventById($request->event_id);

        $path = storage_path('app/videos/' . $event_details->video_path);
        if (!File::exists($path))
        {
            abort(404);
        }
        File::delete($path);
        $recorded_event_model->deleteRecordedEvent($event_details->video_id);

        return redirect('/dashboard');
    }

    public function showEvent(Request $request)
    {
        $recorded_event_model = new RecordedEvent();

        if (!isset($request->event_id))
        {
            return redirect('/dashboard');
        }

        $video_details = $recorded_event_model->getRecordedEventById($request->event_id);
        $users_model = new UserModel();
        $user_is_admin = $users_model->isCurrentUserAdmin();

        return view(
            'event',
            [
                'video_details' => $video_details,
                'user_is_admin' => $user_is_admin
            ]
        );
    }

    private function validateAccoundCredentials($new_username, $new_phone_number, $new_password, $confirm_password, $is_edit = 0, $ignore_password_checks = 0)
    {
        $users_model = new UserModel();
        $error_messages = [];

        if (strlen($new_username) < 5 || strlen($new_username) > 20)
        {
            $error_messages[] = "Numele de utilizator trebuie sa fie de minim 5 si maxim 20 caractere!";
        }
        if (!ctype_alnum($new_username))
        {
            $error_messages[] = "Numele de utilizator poate fi alcatuit doar din litere si cifre!";
        }
        if (!$is_edit && !$users_model->isUniqueUsername($new_username))
        {
            $error_messages[] = "Exista deja un utilizator inregistrat sub acest nume!";
        }
        if (!$ignore_password_checks)
        {
            if (strlen($new_password) < 7 || strlen($new_username) > 20)
            {
                $error_messages[] = "Parola trebuie sa fie de minim 7 si maxim 20 caractere!";
            }
            if (!ctype_alnum($new_password))
            {
                $error_messages[] = "Parola poate fi alcatuita doar din litere si cifre!";
            }
            if ($new_password != $confirm_password)
            {
                $error_messages[] = "Parola din campul de confirmare nu se potriveste!";
            }
        }
        if (!ctype_digit($new_phone_number))
        {
            $error_messages[] = "Numarul de telefon poate fi alcatuit doar din cifre!";
        }
        if (strlen($new_phone_number) != 10)
        {
            $error_messages[] = "Numarul de telefon trebuie sa aiba o lungime de 10 cifre!";
        }

        return $error_messages;
    }

    public function submitNewAccount(Request $request)
    {
        $users_model = new UserModel();

        $user_is_admin = $users_model->isCurrentUserAdmin();

        if (!$user_is_admin)
        {
            return redirect('/dashboard');
        }

        $new_username = $request->new_username;
        $new_phone_number = $request->new_phone_number;
        $new_password = $request->new_password;
        $confirm_password = $request->confirm_password;
        $new_is_admin = $request->new_is_admin == 'on' ? 1 : 0;

        $error_messages = $this->validateAccoundCredentials($new_username, $new_phone_number, $new_password, $confirm_password);

        if (count($error_messages)) {
            return view('accountform',
                [
                    'error_messages' => $error_messages,
                    'new_username' => $new_username,
                    'new_phone_number' => $new_phone_number,
                    'new_password' => $new_password,
                    'new_is_admin' => $new_is_admin
                ]
            );
        }

        $users_model->insertNewUser($new_username, $new_password, $new_phone_number, $new_is_admin);

        return redirect('/accounts');
    }

    public function submitAccountEdit(Request $request)
    {
        $users_model = new UserModel();

        $user_is_admin = $users_model->isCurrentUserAdmin();

        if (!$user_is_admin || !isset($request->edited_user_id) || $request->edited_user_id == $users_model->getCurrentUserID())
        {
            return redirect('/dashboard');
        }

        $edited_user_id = $request->edited_user_id;
        $new_username = $request->new_username;
        $new_phone_number = $request->new_phone_number;
        $new_password = $request->new_password;
        $confirm_password = $request->confirm_password;
        $new_is_admin = $request->new_is_admin == 'on' ? 1 : 0;
        $keep_old_password = $request->keep_old_password == 'on' ? 1 : 0;

        $error_messages = $this->validateAccoundCredentials($new_username, $new_phone_number, $new_password, $confirm_password, 1, $keep_old_password);

        if (count($error_messages)) {
            return view('accountform',
                [
                    'is_edit' => 1,
                    'error_messages' => $error_messages,
                    'new_username' => $new_username,
                    'new_phone_number' => $new_phone_number,
                    'new_password' => $new_password,
                    'new_is_admin' => $new_is_admin,
                    'keep_old_password' => $keep_old_password,
                    'edited_user_id' => $request->user_id
                ]
            );
        }

        $users_model->editUser($edited_user_id, $new_username, $new_password, $new_phone_number, $new_is_admin, $keep_old_password);

        return redirect('/accounts');
    }

    public function deleteAccount(Request $request) {
        $users_model = new UserModel();

        $user_is_admin = $users_model->isCurrentUserAdmin();

        if (!$user_is_admin || !isset($request->id_to_delete) || $request->id_to_delete == $users_model->getCurrentUserID())
        {
            return redirect('/dashboard');
        }

        $users_model->deleteUser($request->id_to_delete);

        return redirect('/accounts');
    }
}
