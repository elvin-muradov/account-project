<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\V1\Tasks\TaskCollection;
use App\Http\Resources\Api\V1\Tasks\TaskResource;
use App\Models\Task;
use App\Traits\HttpResponses;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TaskController extends Controller
{
    use HttpResponses;

    public function index(Request $request): JsonResponse
    {
        $request->validate([
            'limit' => ['nullable', 'integer'],
        ]);

        $companyId = getHeaderCompanyId();

        if (!$companyId) {
            return $this->error(message: "Şirkət tapılmadı", code: 404);
        }

        $tasks = Task::query()
            ->where('company_id', $companyId)
            ->where('accountant_id', auth()->id())
            ->with(['employee', 'company', 'accountant'])
            ->paginate($request->input('limit') ?? 10);

        if ($request->has('type')) {
            $tasks = Task::query()
                ->where('company_id', $companyId)
                ->where('accountant_id', auth()->id())
                ->where('type', $request->input('type'))
                ->with(['employee', 'company', 'accountant'])
                ->paginate($request->input('limit') ?? 10);
        }

        if ($request->has('subtype')) {
            $tasks = Task::query()
                ->where('company_id', $companyId)
                ->where('accountant_id', auth()->id())
                ->where('subtype', $request->input('subtype'))
                ->with(['employee', 'company', 'accountant'])
                ->paginate($request->input('limit') ?? 10);
        }

        return $this->success(data: new TaskCollection($tasks));
    }

    public function show($task): JsonResponse
    {
        $companyId = getHeaderCompanyId();

        if (!$companyId) {
            return $this->error(message: "Şirkət tapılmadı", code: 404);
        }

        $task = Task::query()
            ->where('company_id', $companyId)
            ->with(['employee', 'company', 'accountant'])
            ->find($task);

        if (!$task) {
            return $this->error(message: 'Tapşırıq tapılmadı', code: 404);
        }

        return $this->success(data: new TaskResource($task));
    }

    public function update(Request $request, $task): JsonResponse
    {
        $request->validate([
            'is_completed' => ['required', 'boolean']
        ]);

        $companyId = getHeaderCompanyId();

        if (!$companyId) {
            return $this->error(message: "Şirkət tapılmadı", code: 404);
        }

        $task = Task::query()
            ->where('company_id', $companyId)
            ->with(['accountant', 'company', 'employee'])
            ->find($task);

        if (!$task || !empty($task->completed_at) || $task->accountant_id !== auth()->id()) {
            return $this->error(message: 'Tapşırıq tapılmadı', code: 404);
        }

        if ($task->is_completed && $request->input('is_completed')) {
            return $this->error(message: 'Bu tapşırıq artıq tamamlanıb', code: 404);
        }

        switch ($task->type) {
            case 'EMPLOYEE':
                if ($task->employee?->salary_card_expired_at < now()) {
                    return $this
                        ->error(message: 'İşçinin maaş kartı ilə bağlı tapşırığı tamamlanmayıb',
                            code: 404);
                }
                break;
            case 'COMPANY':
                if ($task->subtype == 'ASAN_SIGN' && $task->company?->asan_sign_expired_at < now()) {
                    return $this
                        ->error(message: 'ASAN imza vaxtı ilə bağlı tapşırığınız tamamlanmayıb',
                            code: 404);
                }

                if ($task->subtype == 'YDM_CARD' && $task->company?->ydm_card_expired_at < now()) {
                    return $this
                        ->error(message: 'YDM kartının bitmə vaxtı ilə bağlı tapşırığınız tamamlanmayıb',
                            code: 404);
                }
                break;

            default:
                return $this
                    ->error(message: 'Bu tapşırıq tipi mövcud deyil', code: 404);
        }

        $task->update([
            'is_completed' => $request->input('is_completed'),
            'completed_at' => $request->input('is_completed') ? now() : null
        ]);

        return $this->success(message: 'Tapşırıq uğurla yeniləndi');
    }

    public function destroy($task): JsonResponse
    {
        $companyId = getHeaderCompanyId();

        if (!$companyId) {
            return $this->error(message: 'Şirkət tapılmadı', code: 404);
        }

        $task = Task::query()
            ->where('company_id', $companyId)
            ->find($task);

        if (!$task) {
            return $this->error(message: 'Tapşırıq tapılmadı', code: 404);
        }

        $task->delete();

        return $this->success(message: 'Tapşırıq uğurla silindi');

    }
}
