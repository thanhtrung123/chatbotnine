<?php

use Illuminate\Database\Seeder;
use App\Models\Learning;

class LearningSeeder extends Seeder
{

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Learning::truncate();

        $api_service = app(\App\Services\Bot\Api\ApiService::class);
        /* @var $api \App\Services\Bot\Api\QnaService */
        $api = $api_service->getService(\App\Services\Bot\Api\QnaService::class);
        $repo = app(\App\Repositories\Learning\LearningRepositoryInterface::class);
        $data = $api->getLearningData();
        foreach ($data as $row) {
            $repo->create([
                'question' => $row['questions'][0],
                'question_morph' => isset($row['questions'][1]) ? $row['questions'][1] : $row['questions'][0],
                'answer' => $row['answer'],
                'metadata' => $row['metadata']['meta'] ?? null,
                'api_id' => $row['id'],
            ]);
        }
    }
}