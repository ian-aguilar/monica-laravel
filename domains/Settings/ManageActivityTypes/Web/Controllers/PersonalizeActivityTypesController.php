<?php

namespace App\Settings\ManageActivityTypes\Web\Controllers;

use App\Http\Controllers\Controller;
use App\Settings\ManageActivityTypes\Services\CreateActivityType;
use App\Settings\ManageActivityTypes\Services\DestroyActivityType;
use App\Settings\ManageActivityTypes\Services\UpdateActivityType;
use App\Settings\ManageActivityTypes\Web\ViewHelpers\PersonalizeActivityTypesIndexViewHelper;
use App\Vault\ManageVault\Web\ViewHelpers\VaultIndexViewHelper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class PersonalizeActivityTypesController extends Controller
{
    public function index()
    {
        return Inertia::render('Settings/Personalize/ActivityTypes/Index', [
            'layoutData' => VaultIndexViewHelper::layoutData(),
            'data' => PersonalizeActivityTypesIndexViewHelper::data(Auth::user()->account),
        ]);
    }

    public function store(Request $request)
    {
        $data = [
            'account_id' => Auth::user()->account_id,
            'author_id' => Auth::user()->id,
            'label' => $request->input('activityTypeName'),
        ];

        $activityType = (new CreateActivityType())->execute($data);

        return response()->json([
            'data' => PersonalizeActivityTypesIndexViewHelper::dtoActivityType($activityType),
        ], 201);
    }

    public function update(Request $request, int $activityTypeId)
    {
        $data = [
            'account_id' => Auth::user()->account_id,
            'author_id' => Auth::user()->id,
            'activity_type_id' => $activityTypeId,
            'label' => $request->input('activityTypeName'),
        ];

        $activityType = (new UpdateActivityType())->execute($data);

        return response()->json([
            'data' => PersonalizeActivityTypesIndexViewHelper::dtoActivityType($activityType),
        ], 200);
    }

    public function destroy(Request $request, int $activityTypeId)
    {
        $data = [
            'account_id' => Auth::user()->account_id,
            'author_id' => Auth::user()->id,
            'activity_type_id' => $activityTypeId,
        ];

        (new DestroyActivityType())->execute($data);

        return response()->json([
            'data' => true,
        ], 200);
    }
}
