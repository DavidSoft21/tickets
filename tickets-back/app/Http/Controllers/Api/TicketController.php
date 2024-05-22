<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Cmixin\BusinessDay;
use Illuminate\Support\Carbon;
use Illuminate\Http\Request;
use App\Http\Requests\Api\Tickets\TicketCreateRequest;
use App\Http\Requests\Api\Tickets\TicketUpdateRequest;
use Illuminate\Http\JsonResponse;
use App\Models\Ticket;
use App\Models\User;
use Exception;
use Illuminate\Support\Facades\Log;

class TicketController extends Controller
{

    /**
     * @OA\Schema(
     *     schema="Ticket",
     *     required={"title ","description", "deadline", "user_id", "status_id"},
     *    @OA\Property(
     *       property="title",
     *       type="string",
     *       description="title of ticket",
     *       example="implement CRUD for ticket management"
     *  ),
     * 
     *     @OA\Property(
     *         property="description",
     *         type="string",
     *         description="Don't forget to implement a deadline for the ticket",
     *         example=""
     *     ),
     *     @OA\Property(
     *         property="deadline",
     *         type="string",
     *         description="deadline of the  ticket",
     *         example="24-05-2024"
     *     ),
     *     @OA\Property(
     *         property="user_id",
     *         type="integer",
     *         description="User responsible for resolving the ticket",
     *         example=1 
     *     ),
     *     @OA\Property(
     *         property="status_id",
     *         type="integer",
     *         description="status ID of the  ticket",
     *         example=1 
     *     ),
     * ),
     * @OA\Get(
     *   path="/tickets/index",
     *   summary="All tickets",
     *   description="Show all  tickets",
     *   operationId="IndexTickets",
     *   tags={"Tickets"},
     *   security={{"bearerAuth": {}}},
     *   @OA\Parameter(
     *     name="Authorization",
     *     in="header",
     *     required=true,
     *     description="Bearer token",
     *     @OA\Schema(
     *       type="string",
     *       default="Bearer id|YOUR_ACCESS_TOKEN_HERE"
     *    )
     *   ),
     *   @OA\Response(
     *     response=200,
     *     description="OK",
     *     @OA\JsonContent(
     *       @OA\Property(property="message", type="string", example="Ok 200"),
     *       @OA\Property(property="Tickets", type="array", @OA\Items(ref="#/components/schemas/Ticket")), 
     *     )
     *   ),
     *   @OA\Response(
     *     response=500,
     *     description="Internal Server Error",
     *     @OA\JsonContent(
     *       @OA\Property(property="message", type="string", example="Server Error"),
     *       @OA\Property(property="errors", type="object", example={"errors": {"Internal server error!"}}),
     *     )
     *   ),
     *   @OA\Response(
     *    response=404,
     *    description="Not Found",
     *    @OA\JsonContent(
     *      @OA\Property(property="message", type="string", example="NotFound"),
     *     @OA\Property(property="errors", type="object", example={"errors": {"Tickets not found."}}),
     *   )
     *  ),
     * )
     *
     * @return JsonResponse
     */
    public function index($id = null)
    {
        try {
            if ($id) {
                $tickets = Ticket::where('user_id', $id)->orderBy('id', 'asc')->get();

                if (count($tickets) == 0) {
                    return response()->json([
                        'message' => 'NotFound',
                        'errors' => 'Not Found tickets assigned to the user with the ID'
                    ], 404);
                }

                return response()->json([
                    'message' => 'Ok',
                    'Tickets' => $tickets
                ], 200);
            } else {
                $tickets = Ticket::orderBy('id', 'asc')->get();

                if (count($tickets) == 0) {
                    return response()->json([
                        'message' => 'NotFound',
                        'errors' =>  'Tickets not found.'
                    ], 404);
                }

                return response()->json([
                    'message' => 'Ok',
                    'Tickets' => $tickets
                ], 200);
            }
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Server Error',
                'errors' => ['errors' => 'Internal server error!']
            ], 500);
        }
    }

    /**
     * Calculates the number of weekdays, weekends, and holidays between a given date range in Colombia, ensuring the start date is after the current date.
     *
     * @param Carbon\Carbon $startDate The start date of the range (inclusive). Must be after the current date.
     * @param Carbon\Carbon $endDate The end date of the range (inclusive).
     * @return array An array containing the following keys:
     *   - totalDays: The total number of days between the start and end date (inclusive).
     *   - weekendDays: The number of Saturdays and Sundays within the date range.
     *   - holidayCount: The number of holidays in Colombia within the date range.
     *   - workingDays: The number of weekdays (excluding weekends and holidays) within the date range.
     *
     * @throws Exception If the start date is after the end date or before the current date.
     */
    public function countHolidaysColombia(Carbon $startDate, Carbon $endDate): array
    {
        $now = Carbon::now()->format('d-m-Y');
        $startDate = $startDate->startOfDay()->format('d-m-Y');
        $endDate = $endDate->endOfDay()->format('d-m-Y');


        if (Carbon::parse($startDate)->gt(Carbon::parse($endDate))) {
            throw new Exception('Start date cannot be after end date.');
        }

        if (Carbon::parse($startDate)->lt(Carbon::parse($now))) {
            throw new Exception('Start date must be on or after the current date.');
        }

        $businessDay = new BusinessDay();
        $totalDays = Carbon::parse($endDate)->diffInDays(Carbon::parse($startDate)) + 1;

        $weekendDays = Carbon::parse($startDate)->diffInDaysFiltered(function (Carbon $date) {
            return $date->isWeekend();
        }, $endDate);

        $holidays = $businessDay->getHolidays($startDate, $endDate, 'Colombia');
        $holidayCount = count($holidays());

        $nonWorkingDays = $weekendDays + $holidayCount;
        $workingDays = $totalDays - $nonWorkingDays;

        return [
            'totalDays' => $totalDays,
            'weekendDays' => $weekendDays,
            'holidayCount' => $holidayCount,
            'workingDays' => $workingDays,
        ];
    }

    /**
     * @OA\Post(
     *  path="/tickets/store",
     * summary="Create a ticket",
     * description="Create a ticket",
     * operationId="StoreTicket",
     * tags={"Tickets"},
     * security={{"bearerAuth": {}}},
     * @OA\Parameter(
     *    name="Authorization",
     *    in="header",
     *    required=true,
     * description="Bearer token",
     * @OA\Schema(
     *   type="string",
     *   default="Bearer
     *   id|YOUR_ACCESS
     *   TOKEN_HERE
     *  "
     *  )
     * ),
     * @OA\RequestBody(
     *   required=true,
     * description=" of ticket",
     * @OA\JsonContent(
     *  required={"title ","description", "deadline", "user_id", "status_id"},
     * @OA\Property(property="title", type="string", example="implement CRUD for ticket management"),
     * @OA\Property(property="description", type="string", example="Don't forget to implement a deadline for the ticket"),
     * @OA\Property(property="deadline", type="string", example="24-05-2024"),
     * @OA\Property(property="user_id", type="integer", example=1),
     * @OA\Property(property="status_id", type="integer", example=1),
     * )
     * ),
     * @OA\Response(
     *  response=200,
     * description="Successfully registered",
     * @OA\JsonContent(
     *   @OA\Property(property="message", type="string", example="Successfully registered"),
     *   @OA\Property(property="response", type="object", ref="#/components/schemas/Ticket"),
     *  )
     * ),
     * @OA\Response(
     *  response=422,
     *  description="Validation Error",
     *  @OA\JsonContent(
     *   @OA\Property(property="message", type="string", example="Validation Error"),
     *   @OA\Property(property="errors", type="object", example={"title": {"The title field is required."}}),
     *  )
     * ),
     * @OA\Response(
     *  response=500,
     *  description="Internal Server Error",
     * @OA\JsonContent(
     *   @OA\Property(property="message", type="string", example="Server Error"),
     *   @OA\Property(property="errors", type="object", example={"errors": {"Internal server error!"}}),
     *  ) 
     * ),
     * )
     * @param TicketCreateRequest $request
     * @return JsonResponse
     * @throws Exception
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(TicketCreateRequest $request)
    {


        try {

            $validatedData = $request->validated();
            $today = Carbon::now()->startOfDay();
            $user = User::find($validatedData['user_id']);

            if (!$user) {
                return response()->json([
                    'message' => 'NotFound',
                    'errors' => 'The User with the provided ID was not found.'
                ], 404);
            }

            $userHasTicket = Ticket::where('user_id', $user->id)->where('status_id', '<>', 3)->get();
            $deadline = '';

            if (!$user->hasRole('admin') && count($userHasTicket) > 5) {
                return response()->json([
                    'message' => '"The Users with title: ' . $request->title . ' the user have reached the maximum of 5 unclosed tickets, please select another."',
                ], 400);
            }

            switch (true) {
                case $user->hasRole('admin'):
                    $deadline = $today->copy()->addDays(20);
                    break;
                case $user->hasRole('guest'):
                    $deadline = $today->copy()->addDays(7);
                    break;
                default:
                    return response()->json([
                        "mensaje" => "Tipo de usuario no permitido en la biblioteca.",
                    ], 400);
                    break;
            }

            $holidays = $this->countHolidaysColombia($today, $deadline);
            $nonWorkingDays = $holidays['holidayCount'] + $holidays['weekendDays'] - 1;
            $deadline->addDays($nonWorkingDays);

            while ($deadline->isWeekend()) {
                $deadline->addDay();
            }

            $ticket = Ticket::create([
                'title' => $validatedData['title'],
                'description' => $validatedData['description'],
                'deadline' => isset($validatedData['deadline']) ?  Carbon::parse($validatedData['deadline'])->format('d-m-Y') : $deadline->format('d-m-Y'),
                'user_id' => $validatedData['user_id'],
                'status_id' => $validatedData['status_id']
            ]);

            return response()->json([
                // 'message' => 'Successfully registered',
                'response' => [
                    "id" => $ticket->id,
                    'title' => $validatedData['title'],
                    "deadline" => $ticket->deadline,
                    "unclosed_tickets" => count($userHasTicket),
                ],
            ], 200);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'message' => 'Validation Error',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage()
            ], 422);
        }
    }


    /**
     * @OA\Get(
     *   path="/tickets/show/{id}",
     *   summary="Show ticket",
     *   description="Show a ticket by ID",
     *   operationId="ShowTicket",
     *   tags={"Tickets"},
     *   security={{"bearerAuth": {}}},
     *   @OA\Parameter(
     *     name="Authorization",
     *     in="header",
     *     required=true,
     *     description="Bearer token",
     *     @OA\Schema(
     *       type="string",
     *       default="Bearer YOUR_ACCESS_TOKEN_HERE"
     *     )
     *   ),
     *   @OA\Parameter(
     *     name="id",
     *     in="path",
     *     required=true,
     *     description="Ticket ID",
     *     @OA\Schema(
     *       type="integer"
     *     )
     *   ),
     *   @OA\Response(
     *     response=200,
     *     description="Ok",
     *     @OA\JsonContent(
     *       @OA\Property(property="message", type="string", example="Ok"),
     *       @OA\Property(property="ticketUsers", type="array", @OA\Items(
     *         @OA\Property(property="title", type="string", example="implemnet crud for tickets"),
     *         @OA\Property(property="first_name", type="string", example="John"),
     *         @OA\Property(property="last_name", type="string", example="Doe"),
     *         @OA\Property(property="created_at", type="string", example="2021-05-24 00:00:00"),
     *         @OA\Property(property="updated_at", type="string", example="2021-05-24 00:00:00"),
     *         @OA\Property(property="deadline", type="string", example="2021-05-24 00:00:00"),
     *       )),
     *     ),
     *   ),
     *   @OA\Response(
     *     response=404,
     *     description="Not Found",
     *     @OA\JsonContent(
     *       @OA\Property(property="message", type="string", example="NotFound"),
     *       @OA\Property(property="errors", type="object", example={"errors": {"The ticket with the provided ID was not found."}}),
     *     ),
     *   ),
     *   @OA\Response(
     *     response=500,
     *     description="Internal Server Error",
     *     @OA\JsonContent(
     *       @OA\Property(property="message", type="string", example="Server Error"),
     *       @OA\Property(property="errors", type="object", example={"errors": {"Internal server error!"}}),
     *     ),
     *   ),
     * )
     * @param int $id
     * @return JsonResponse
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     * @throws \Exception
     * @throws \Illuminate\Validation\ValidationException
     */
    public function show(int $id)
    {
        try {
            $ticket = Ticket::findOrFail($id);

            $showTicketUser = DB::table('users')
                ->join('tickets', 'users.id', '=', 'tickets.user_id')
                ->select('tickets.title', 'users.first_name', 'users.last_name', 'tickets.created_at', 'tickets.updated_at', 'tickets.deadline')
                ->where([['tickets.user_id', '=', $ticket->user_id], ['tickets.id', '=', $id]])
                ->get();

            return response()->json([
                'message' => 'Ok',
                'ticketUsers' => $showTicketUser
            ], 200);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'message' => 'NotFound',
                'errors' => ['id' => 'The Ticket with the provided ID was not found.']
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Server Error',
                'errors' => ['errors' => 'Internal server error!']
            ], 500);
        }
    }



    /**
     *  @OA\Put(
     *  path="/tickets/update/{id}",
     * summary="Update ticket",
     * description="Update a ticket by ID",
     * operationId="UpdateTicket",
     * tags={"Tickets"},
     * security={{"bearerAuth": {}}},
     * @OA\Parameter(
     * name="Authorization",
     * in="header",
     * required=true,
     * description="Bearer token",
     * @OA\Schema(
     * type="string
     * ",
     * default="Bearer
     * id|YOUR_ACCESS
     * _TOKEN_HERE
     * "
     * )
     * ),
     * @OA\Parameter(
     * name="id",
     * in="path",
     * required=true,
     * description=" of ticket ID",
     * @OA\Schema(
     * type="integer"
     * )
     * ),
     * @OA\RequestBody(
     * required=true,
     * description=" of ticket",
     * @OA\JsonContent(
     * required={"title ","description", "deadline", "user_id", "status_id"},
     * @OA\Property(property="title", type="string", example="implement CRUD for ticket management"),
     * @OA\Property(property="description", type="string", example="Don't forget to implement a deadline for the ticket"),
     * @OA\Property(property="deadline", type="string", example="24-05-2024"),
     * @OA\Property(property="user_id", type="integer", example=1),
     * @OA\Property(property="status_id", type="integer", example=1),
     * )
     * ),
     * @OA\Response(
     * response=200,
     * description="Successfully updated",
     * @OA\JsonContent(
     * @OA\Property(property="message", type="string", example="Successfully updated"),
     * @OA\Property(property="ticket", type="object", ref="#/components/schemas/Ticket"),
     * )
     * ),
     * @OA\Response(
     * response=422,
     * description="Validation Error",
     * @OA\JsonContent(
     * @OA\Property(property="message", type="string", example="Validation Error"),
     * @OA\Property(property="errors", type="object", example={"title": {"The title field is required."}}),
     * )
     * ),
     * @OA\Response(
     * response=404,
     * description="Not Found",
     * @OA\JsonContent(
     * @OA\Property(property="message", type="string", example="NotFound"),
     * @OA\Property(property="errors", type="object", example={"errors": {"The ticket with the provided ID was not found."}}),
     * )
     * ),
     * @OA\Response(
     * response=500,
     * description="Internal Server Error",
     * @OA\JsonContent(
     * @OA\Property(property="message", type="string", example="Server Error"),
     * @OA\Property(property="errors", type="object", example={"errors": {"Internal server error!"}}),
     * )
     * ),
     * )
     * @param TicketUpdateRequest $request
     * @param int $id
     * @return JsonResponse
     * @throws \Illuminate\Validation\ValidationException
     * @throws \Exception
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     * 
     */
    public function update(TicketUpdateRequest $request, int $id)
    {
        try {
            $validatedData = $request->validated();

            $ticket = Ticket::find($id);

            if (!$ticket) {
                return response()->json([
                    'message' => 'NotFound',
                    'errors' => 'The Ticket with the provided ID was not found.'
                ], 404);
            }

            $ticket->update([
                'title' => $validatedData['title'],
                'description' => $validatedData['description'],
                'deadline' => isset($validatedData['deadline']) ? $validatedData['deadline'] : $ticket->deadline,
                'user_id' => $validatedData['user_id'],
                'status_id' => $validatedData['status_id']
            ]);

            return response()->json([
                'message' => 'Successfully updated',
                'ticket' => $ticket
            ], 200);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'message' => 'Validation Error',
                'errors' => $e->errors()
            ], 422);
        }
    }

    /**
     * @OA\Delete(
     * path="/tickets/destroy/{id}",
     * summary="Delete ticket",
     * description="Delete a ticket by ID",
     * operationId="DestroyTicket",
     * tags={"Tickets"},
     * security={{"bearerAuth": {}}},
     * @OA\Parameter(
     * name="Authorization",
     * in="header",
     * required=true,
     * description="Bearer token",
     * @OA\Schema(
     * type="string
     * ",
     * default="Bearer
     * id|YOUR_ACCESS
     * _TOKEN_HERE
     * "
     * )
     * ),
     * @OA\Parameter(
     * name="id",
     * in="path",
     * required=true,
     * description=" of ticket ID",
     * @OA\Schema(
     * type="integer"
     * )
     * ),
     * @OA\Response(
     * response=200,
     * description="Successfully deleted",
     * @OA\JsonContent(
     * @OA\Property(property="message", type="string", example="Successfully deleted"),
     * @OA\Property(property="ticket", type="object", ref="#/components/schemas/Ticket"),
     * )
     * ),
     * @OA\Response(
     * response=404,
     * description="Not Found",
     * @OA\JsonContent(
     * @OA\Property(property="message", type="string", example="NotFound"),
     * @OA\Property(property="errors", type="object", example={"errors": {"The ticket with the provided ID was not found."}}),
     * )
     * ),
     * @OA\Response(
     * response=500,
     * description="Internal Server Error",
     * @OA\JsonContent(
     * @OA\Property(property="message", type="string", example="Server Error"),
     * @OA\Property(property="errors", type="object", example={"errors": {"Internal server error!"}}),
     * )
     * ),
     * )
     * @param int $id
     * @return JsonResponse
     * @throws \Exception
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     * 
     */
    public function destroy(int $id)
    {
        try {
            $ticket = Ticket::findOrFail($id);
            $ticket->delete();

            return response()->json([
                'message' => 'Successfully deleted',
                'ticket' => $ticket
            ], 200);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'message' => 'ticket not found',
                'errors' => ['id' => 'The Ticket with the provided ID was not found.']
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Server Error',
                'errors' => ['errors' => 'Internal server error!']
            ], 500);
        }
    }
}
