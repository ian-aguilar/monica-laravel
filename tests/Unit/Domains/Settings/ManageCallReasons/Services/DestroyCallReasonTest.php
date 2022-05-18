<?php

namespace Tests\Unit\Domains\Settings\ManageCallReasons\Services;

use App\Exceptions\NotEnoughPermissionException;
use App\Models\Account;
use App\Models\CallReason;
use App\Models\CallReasonType;
use App\Models\User;
use App\Settings\ManageCallReasons\Services\DestroyCallReason;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class DestroyCallReasonTest extends TestCase
{
    use DatabaseTransactions;

    /** @test */
    public function it_destroys_a_type(): void
    {
        $ross = $this->createAdministrator();
        $type = CallReasonType::factory()->create([
            'account_id' => $ross->account_id,
        ]);
        $reason = CallReason::factory()->create([
            'call_reason_type_id' => $type->id,
        ]);
        $this->executeService($ross, $ross->account, $type, $reason);
    }

    /** @test */
    public function it_fails_if_wrong_parameters_are_given(): void
    {
        $request = [
            'title' => 'Ross',
        ];

        $this->expectException(ValidationException::class);
        (new DestroyCallReason)->execute($request);
    }

    /** @test */
    public function it_fails_if_user_doesnt_belong_to_account(): void
    {
        $this->expectException(ModelNotFoundException::class);

        $ross = $this->createAdministrator();
        $account = Account::factory()->create();
        $type = CallReasonType::factory()->create([
            'account_id' => $ross->account_id,
        ]);
        $reason = CallReason::factory()->create([
            'call_reason_type_id' => $type->id,
        ]);
        $this->executeService($ross, $account, $type, $reason);
    }

    /** @test */
    public function it_fails_if_type_doesnt_belong_to_account(): void
    {
        $this->expectException(ModelNotFoundException::class);

        $ross = $this->createAdministrator();
        $type = CallReasonType::factory()->create();
        $reason = CallReason::factory()->create([
            'call_reason_type_id' => $type->id,
        ]);
        $this->executeService($ross, $ross->account, $type, $reason);
    }

    /** @test */
    public function it_fails_if_user_doesnt_have_right_permission_in_vault(): void
    {
        $this->expectException(NotEnoughPermissionException::class);

        $ross = $this->createUser();
        $type = CallReasonType::factory()->create([
            'account_id' => $ross->account_id,
        ]);
        $reason = CallReason::factory()->create([
            'call_reason_type_id' => $type->id,
        ]);
        $this->executeService($ross, $ross->account, $type, $reason);
    }

    private function executeService(User $author, Account $account, CallReasonType $type, CallReason $reason): void
    {
        $request = [
            'account_id' => $account->id,
            'author_id' => $author->id,
            'call_reason_type_id' => $type->id,
            'call_reason_id' => $reason->id,
        ];

        (new DestroyCallReason)->execute($request);

        $this->assertDatabaseMissing('call_reasons', [
            'id' => $reason->id,
        ]);
    }
}
