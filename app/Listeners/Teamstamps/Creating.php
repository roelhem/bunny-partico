<?php

namespace App\Listeners\Teamstamps;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Queue\InteractsWithQueue;

class Creating
{

    /**
     * When the model is being created.
     *
     * @param Model $model
     * @return void
     */
    public function handle($model)
    {
        //
    }
}
