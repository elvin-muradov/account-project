<?php

namespace App\Http\Controllers\Api\V1\Envelopes;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\Envelopes\EnvelopeStoreRequest;
use App\Http\Requests\Api\V1\Envelopes\EnvelopeUpdateRequest;
use App\Http\Resources\Api\V1\Envelopes\EnvelopeCollection;
use App\Http\Resources\Api\V1\Envelopes\EnvelopeResource;
use App\Models\Envelopes\Envelope;
use App\Traits\HttpResponses;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class EnvelopeController extends Controller
{
    use HttpResponses;

    public function index(Request $request): JsonResponse
    {
        $envelopes = Envelope::query()->with([
            'fromCompany', 'toCompany', 'creator'
        ])->paginate($request->input('limit') ?? 10);

        return $this->success(data: new EnvelopeCollection($envelopes));
    }

    public function show($envelope): JsonResponse
    {
        $envelope = Envelope::query()->with([
            'fromCompany', 'toCompany', 'creator'
        ])->find($envelope);

        if (!$envelope) {
            return $this->error(message: "Məktub tapılmadı", code: 404);
        }

        return $this->success(data: EnvelopeResource::make($envelope));
    }

    public function store(EnvelopeStoreRequest $request): JsonResponse
    {
        $data = $request->validated();

        if ($request->hasFile('envelopes')) {
            $envelopes = $request->file('envelopes');
            $data = array_merge($data, ['envelopes' => returnFilesArray($envelopes,
                'envelopes')]);
        }

        $data = array_merge($data, [
            'code' => 'ENVP-' . Envelope::query()
                    ->where('type', '=', $request->type)
                    ->count() + 1 . '/' . date('Y') . '/' . $request->type,
            'sent_at' => now(),
            'creator_id' => $request->user('user')->id
        ]);

        $envelope = Envelope::query()->create($data);

        return $this->success(data: EnvelopeResource::make($envelope),
            message: "Məktub uğurla əlavə olundu",
            code: 201);
    }

    public function update(EnvelopeUpdateRequest $request, $envelope): JsonResponse
    {
        $data = $request->validated();
        $envelope = Envelope::query()->find($envelope);

        if (!$envelope) {
            return $this->error(message: "Məktub tapılmadı", code: 404);
        }

        if ($request->has('delete_envelopes') && $request->delete_envelopes != null) {
            $deletedEnvelopes = $request->input('delete_envelopes');
            $envelopes = $envelope->envelopes ?? [];
            $deletedFiles = deleteFiles($deletedEnvelopes, $envelopes, true);

            if (is_array($deletedFiles)) {
                $envelope->envelopes = array_values($deletedFiles);
            } else {
                return $this->error(
                    message: "Ən az bir faylın qalması vacibdir", code: 400
                );
            }
        }

        if ($request->hasFile('envelopes')) {
            $envelopes = $request->file('envelopes');
            $envelopesArr = $envelope->envelopes ?? [];
            $updatedFiles = returnFilesArray($envelopes, 'envelopes');
            $data = array_merge($data, ['envelopes' => array_merge($envelopesArr, $updatedFiles)]);
        }

        $envelope->update($data);

        return $this->success(data: EnvelopeResource::make($envelope),
            message: "Məktub uğurla yeniləndi");
    }

    public function destroy($envelope): JsonResponse
    {
        $envelope = Envelope::query()->find($envelope);

        if (!$envelope) {
            return $this->error(message: "Məktub tapılmadı", code: 404);
        }

        if ($envelope->envelopes != null && count($envelope->envelopes) > 0) {
            checkFilesAndDeleteFromStorage($envelope->envelopes);
        }

        $envelope->delete();

        return $this->success(message: "Məktub uğurla silindi");
    }
}
