<?php

namespace App\Queue;

use MongoDate;
use Illuminate\Queue\Failed\FailedJobProviderInterface;

class MongoLiteFailedJobProvider implements FailedJobProviderInterface
{
    /**
     * Log a failed job into storage.
     *
     * @param  string  $connection
     * @param  string  $queue
     * @param  string  $payload
     * @return void
     */
    public function log($connection, $queue, $payload)
    {
        $failed_at = new MongoDate();

        $this->getMongoLite()->insert(compact('connection', 'queue', 'payload', 'failed_at'));
    }

    /**
     * Get a list of all of the failed jobs.
     *
     * @return array
     */
    public function all()
    {
        return $this->getMongoLite()->sort(['_id' => -1])->get();
    }

    /**
     * Get a single failed job.
     *
     * @param  mixed  $id
     * @return array
     */
    public function find($id)
    {
        return $this->getMongoLite()->first($id);
    }

    /**
     * Delete a single failed job from storage.
     *
     * @param  mixed  $id
     * @return bool
     */
    public function forget($id)
    {
        return $this->getMongoLite()->delete($id);
    }

    /**
     * Flush all of the failed jobs from storage.
     *
     * @return void
     */
    public function flush()
    {
        $this->getMongoLite()->collection()->drop();
    }

    /**
     * Get mongo lite query instance.
     *
     * @return \Hexcores\MongoLite\Query
     */
    protected function getMongoLite()
    {
        $lite = mongo_lite(env('QUEUE_FAILED_MONGO_LITE', 'failed_jobs'));
        $lite::date(false);

        return $lite;
    }
}