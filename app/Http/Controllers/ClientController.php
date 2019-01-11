<?php
namespace App\Http\Controllers;

use App\Http\Requests\ClientRequest;
use App\Http\Resources\ClientResource;
use App\Http\Resources\ClientCollection;
use App\Models\System\SoapType;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class ClientController extends Controller
{
    public function index()
    {
        return view('clients.index');
    }

    public function records()
    {
        $records = User::where('role', 'client')->advancedFilter();
        return new ClientCollection($records);
    }

    public function tables()
    {
        $soap_types = SoapType::all();
        return to_json(compact('soap_types'));
    }

    public function create($id)
    {
        $form = new ClientResource(User::with(['client'])->findOrFail($id));
        return to_json(compact('form'));
    }

    public function store(ClientRequest $request)
    {
        $id = $request->input('id');
        DB::transaction(function () use($id, $request) {
            $user = User::firstOrNew(['id' => $id]);
            if (!$id) {
                $user->role = 'client';
                $user->username = $request->input('email');
                $user->password = bcrypt(str_random(8));
                $user->api_token = str_random(50);
            } else {
                if (!$user->api_token) {
                    $user->api_token = str_random(50);
                }
            }
            $user->fill($request->all());
            $user->save();

            $user->client()->updateOrCreate(['user_id' => $user->id],
                                            [
                                                'company_name' => $request->input('company_name'),
                                                'company_number' => $request->input('company_number'),
                                                'soap_type_id' => $request->input('soap_type_id'),
                                                'soap_username' => $request->input('soap_username'),
                                                'soap_password' => $request->input('soap_password'),
                                                'certificate' => $request->input('certificate')
                                            ]);
        });

        return to_json([
            'success' => true,
            'message' => __('app.actions.'.((!$id)?'create':'update'). '.success')
        ]);
    }

    public function destroy($id)
    {
        $user = User::findOrFail($id);
        $user->delete();

        return to_json([
            'success' => true
        ]);
    }

    public function uploadFile(Request $request)
    {
        if ($request->hasFile('file')) {
            $type = $request->input('type');
            $file = $request->file('file');
            $name = $file->getClientOriginalName();
            $path = $file->storeAs(($type === 'logo')?'public/uploads/logo':'certificates', $name);
            return [
                'success' => true,
                'message' => __('app.actions.upload.success'),
                'path' => $path,
                'type' => $type
            ];
        }
        return [
            'success' => false,
            'message' =>  __('app.actions.upload.error'),
        ];
    }
}