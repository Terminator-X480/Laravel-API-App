<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Http\Controllers\Controller;

class LeadLogsController extends Controller
{
    public function getLeadLogs(Request $request, $id)
    {
        $lead_id = (int) $id;

        $log_table    = 'wp_mt_lead_logs';
        $action_table = 'wp_mt_lead_actions';
        $lead_table   = 'wp_mt_leads';

        $logs = DB::table("$log_table as logs")
            ->leftJoin("$action_table as actions", 'actions.id', '=', 'logs.action_id')
            ->where('logs.lead_id', $lead_id)
            ->orderByDesc('logs.id')
            ->select('logs.*', 'actions.name as action_name')
            ->get();

        $action_colors = [
            1 => '#28a745', // booked
            2 => '#dc3545', // unbooked
            3 => '#9f0a0a', // deleted
            4 => '#007bff', // updated
            5 => '#8b0808', //cancel
            6 => '#c3a307', //uncancel
            7 => '#089cab',
        ];

        $lead_created_at = DB::table($lead_table)->where('id', $lead_id)->value('created_at');

        $html = '';

        if ($logs->isEmpty() && !$lead_created_at) {
            $html = "<h5>No logs found for this lead.</h5>";
        } else {
            foreach ($logs as $log) {
                $user = DB::table('wp_users')->where('id', $log->user_id)->value('display_name') ?? '-';
                $html .= $this->buildSingleLogHtml('Log', $log->action_name, $log->created_on, $user, json_decode($log->action_data, true), $action_colors[$log->action_id] ?? '#6c757d');
            }

            if ($lead_created_at) {
                $html .= $this->buildSingleLogHtml('Log', 'Lead Created', $lead_created_at, '-', [], '#ffa500');
            }
        }

        return response()->json(['html' => $html]);
    }

    protected function buildSingleLogHtml($type, $label, $timestamp, $user_name, $action_data = [], $border_color = '#6c757d')
    {
        ob_start();
        ?>
        <div class="single-lead-log-wrapper" style="border-left: 4px solid <?= e($border_color) ?>">
            <div class="single-lead-log-type"><?= e($type) ?></div>
            <div class="lead-log-action-date-wrapper">
                <div class="single-lead-log-action" style="background-color: <?= e($border_color) ?>">
                    <?= e($label) ?>
                </div>
                <div class="single-lead-log-date"><?= e($this->getFormattedTime($timestamp)) ?></div>
            </div>
            <div class="single-lead-log-details">
                <?php if (!empty($action_data) && is_array($action_data)) : ?>
                    <ul>
                        <li>Details:</li>
                        <?php foreach ($action_data as $key => $values): ?>
                            <li><strong><?= e(ucwords(str_replace('_', ' ', $key))) ?>:</strong>
                                <?php
                                if (is_array($values)) {
                                    foreach ($values as $val_key => $val) {
                                        if (str_contains($key, 'date') || $key === 'created_at') {
                                            $val = $this->convertUtcToIst($val);
                                        }
                                        echo '<div>' . e($val_key) . ' : ' . e($val) . '</div>';
                                    }
                                } else {
                                    if (str_contains($key, 'date') || $key === 'created_at') {
                                        $values = $this->convertUtcToIst($values);
                                    }
                                    echo e($values);
                                }
                                ?>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                <?php endif; ?>
            </div>
            <div class="single-lead-log-action-done-by">By: <?= e($user_name) ?></div>
        </div>
        <?php
        return ob_get_clean();
    }

    protected function getFormattedTime($datetime)
    {
        return Carbon::parse($datetime)->timezone('Asia/Kolkata')->format('d M Y, h:i A');
    }

    protected function convertUtcToIst($datetime)
    {
        return Carbon::parse($datetime)->timezone('Asia/Kolkata')->toDateTimeString();
    }
}
