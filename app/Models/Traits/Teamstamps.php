<?php


namespace App\Models\Traits;


use App\Models\Team;

trait Teamstamps
{

    /**
     * Boot the userstamps trait for a model.
     *
     * @return void
     */
    public static function bootTeamstamps()
    {
        static::registerTeamstampListeners();
    }

    /**
     * Register events we need to listen for.
     *
     * @return void
     */
    public static function registerTeamstampListeners()
    {
        static::creating(function (self $model) {
            if(\Auth::user()) {
                $currentTeamId = \Auth::user()->current_team_id;
                $model->created_by_team = $currentTeamId;
                $model->updated_by_team = $currentTeamId;
            }
        });
        static::updating(function (self $model) {
            if(\Auth::user()) {
                $currentTeamId = \Auth::user()->current_team_id;
                $model->updated_by_team = $currentTeamId;
            } else {
                $model->updated_by_team = null;
            }
        });
    }

    /**
     * Get the user that created the model.
     */
    public function creatorTeam()
    {
        return $this->belongsTo(Team::class, 'created_by_team');
    }

    /**
     * Get the user that edited the model.
     */
    public function editorTeam()
    {
        return $this->belongsTo(Team::class, 'updated_by_team');
    }
}
