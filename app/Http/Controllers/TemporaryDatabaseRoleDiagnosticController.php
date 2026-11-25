<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TemporaryDatabaseRoleDiagnosticController extends Controller
{
    public function __invoke(Request $request): JsonResponse
    {
        $expectedToken = config('services.pariksha_db_role_diagnostic.token');
        $providedToken = $request->header('X-Pariksha-Diagnostic-Token');

        if (
            ! is_string($expectedToken)
            || strlen($expectedToken) < 32
            || ! is_string($providedToken)
            || $providedToken === ''
            || ! hash_equals($expectedToken, $providedToken)
        ) {
            abort(404);
        }

        $connection = DB::connection();
        $identity = $connection->selectOne(
            'SELECT current_user, session_user, current_database(), '
            .'inet_server_addr()::text AS inet_server_addr, inet_server_port() AS inet_server_port'
        );

        return response()->json([
            'current_user' => $identity->current_user,
            'session_user' => $identity->session_user,
            'current_database' => $identity->current_database,
            'inet_server_addr' => $identity->inet_server_addr,
            'inet_server_port' => $identity->inet_server_port === null
                ? null
                : (int) $identity->inet_server_port,
            'connection_name' => $connection->getName(),
            'driver' => $connection->getDriverName(),
        ]);
    }
}
