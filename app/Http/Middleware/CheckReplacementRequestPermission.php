<?php

namespace App\Http\Middleware;

use App\ReplacementRequest;
use Closure;
use Illuminate\Support\Facades\Auth;

class CheckReplacementRequestPermission
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $user_teachers_ids = Auth::user()->teachers->pluck('id')->toArray();

        if (isset($request->prev_replace_rules)
            && ! in_array(json_decode($request->prev_replace_rules, true)['teacher_id'], $user_teachers_ids))
        {
            return redirect()->route('home', ['permission_error' => true]);
        }

        $replacement_request = ReplacementRequest::where('id', $request->id)->first();

        if (isset($replacement_request->is_sent) || isset($replacement_request->is_cancelled)) {
            if (! in_array($replacement_request->replaceable_lesson->teacher_id, $user_teachers_ids)) {
                return redirect()->route('home', ['permission_error' => true]);
            }
        }

        if (isset($replacement_request->is_agreed) || isset($replacement_request->is_declined)) {
            if (! in_array($replacement_request->replacing_lesson->teacher_id, $user_teachers_ids)) {
                return redirect()->route('home', ['permission_error' => true]);
            }
        }

        if (isset($replacement_request->is_permitted) || isset($replacement_request->is_not_permitted)) {
            if (! Auth::user()->is_admin) {
                return redirect()->route('home', ['permission_error' => true]);
            }
        }

        return $next($request);
    }
}
