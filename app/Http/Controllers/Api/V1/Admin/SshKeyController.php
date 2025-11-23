<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\SshKeyResource;
use App\Models\SshKey;
use App\Notifications\ResourceDeletedNotification;
use App\Services\SshKeyService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Notification;
use Spatie\QueryBuilder\QueryBuilder;

class SshKeyController extends Controller
{
    public function __construct(
        private SshKeyService $sshKeyService
     ) {}

    public function index(Request $request)
    {
        $sshKeys = $this->sshKeyService->listSshKeys($request);

        return SshKeyResource::collection($sshKeys);
    }

    public function show(SshKey $sshKey)
    {
        $newSshKey = $this->sshKeyService->getSshKey($sshKey);

        return new SshKeyResource($newSshKey);
    }

    public function destroy(SshKey $sshKey)
    {
        $isAssociated = $this->sshKeyService->sshKeyAssociated($sshKey);
        if ($isAssociated) {
            return response()->json([
                'message' => __('messages.ssh_keys.associated_servers_msg'),
            ], 422);
        }

        $this->sshKeyService->delete($sshKey);
        
        $notifiable = \App\Models\User::find($sshKey->created_by);
        $lang = $notifiable->language ?? 'en';
        
        Notification::locale($lang)
        ->send($notifiable, new ResourceDeletedNotification($sshKey, 'ssh key', 'ssh-keys'));

        return response()->noContent();
    }

}
