<?php

namespace App\Settings\ManageNotificationChannels\Web\Controllers;

use App\Http\Controllers\Controller;
use App\Models\UserNotificationChannel;
use App\Settings\ManageNotificationChannels\Web\ViewHelpers\NotificationsLogIndexViewHelper;
use App\Vault\ManageVault\Web\ViewHelpers\VaultIndexViewHelper;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class NotificationsLogController extends Controller
{
    public function index(Request $request, int $userNotificationChannelId)
    {
        try {
            $channel = UserNotificationChannel::where('user_id', Auth::user()->id)
                ->findOrFail($userNotificationChannelId);
        } catch (ModelNotFoundException) {
            return redirect('vaults');
        }

        return Inertia::render('Settings/Notifications/Logs/Index', [
            'layoutData' => VaultIndexViewHelper::layoutData(),
            'data' => NotificationsLogIndexViewHelper::data($channel, Auth::user()),
        ]);
    }
}
