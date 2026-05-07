<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\AttendeeResource;
use App\Http\Traits\CanLoadRelationships;
use App\Models\Attendee;
use App\Models\Event;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class AttendeeController extends Controller implements HasMiddleware
{
    use CanLoadRelationships;

    private $relations = ['user'];

    public function __construct()
    {
        $this->middleware();
    }

    public function index(Event $event)
    {
        $attendees = $this->loadRelationships($event->attendees()->latest());

        return AttendeeResource::collection(
            $attendees->paginate()
        );
    }

    public function store(Request $request, Event $event)
    {
        $attendee = $this->loadRelationships(
            $event->attendees()->create([
                'user_id' => 1
            ])
        );

        return new AttendeeResource($this->loadRelationships($attendee));
    }

    public function show(Event $event, Attendee $attendee)
    {
        return new AttendeeResource($attendee);
    }

    public function update(Request $request, string $id)
    {
        //
    }

    // Not setting $event to Event to not load it since we don't need it here
    public function destroy(Event $event, Attendee $attendee)
    {
        $attendee->delete();

        return response(status: 204);
    }

    // Implementation of middleware method
    public static function middleware(): array
    {
        return [
            new Middleware('auth:sanctum', except: ['index', 'show', 'update']),

            new Middleware('can:viewAny,' . Event::class, only: ['index']),
            new Middleware('can:view,event', only: ['show']),
            new Middleware('can:create,' . Event::class, only: ['create', 'store']),
            new Middleware('can:update,event', only: ['edit', 'update']),
            new Middleware('can:delete,event', only: ['destroy'])
        ];
    }
}
