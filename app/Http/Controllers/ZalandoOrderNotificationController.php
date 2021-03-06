<?php

namespace App\Http\Controllers;

use App\Models\ZalandoOrderNotification;
use App\Http\Requests\StoreZalandoOrderNotificationRequest;
use App\Http\Requests\UpdateZalandoOrderNotificationRequest;
use Illuminate\Http\Request;

class ZalandoOrderNotificationController extends Controller
{
    /**
     * Injecting the Model instance as contructor method .
     *
     * @return \Illuminate\Http\Response
     */
    public function __construct(ZalandoOrderNotification $order_notification)
    {
        $this->model = $order_notification;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Retrieve the resource by id.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function retrieve($order_number)
    {
        $notifications = $this->model
            ->with('items')
            ->with('delivery_details')
            ->with('customer_billing_address')
            ->where('order_number', '=', $order_number)
            ->get();

        return response()->json(['notifications' => $notifications], 200);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\StoreZalandoOrderNotificationRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreZalandoOrderNotificationRequest $request)
    {
        $this->model = $this->model->create(
            array_merge(
                $request->all(),
                ['authorization_basic_auth_id' => $request->get('authorization_basic_auth_id')]
            )
        );
        $this->model->items()->createMany($request->items);
        if ($request->delivery_details) $this->model->delivery_details()->create($request->delivery_details);
        if ($request->customer_billing_address) $this->model->customer_billing_address()->create($request->customer_billing_address);
        return response()->json([
            "success" => "[API] notification received successfully"
        ], 200);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\ZalandoOrderNotification  $zalandoOrderNotification
     * @return \Illuminate\Http\Response
     */
    public function show(ZalandoOrderNotification $zalandoOrderNotification)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\ZalandoOrderNotification  $zalandoOrderNotification
     * @return \Illuminate\Http\Response
     */
    public function edit(ZalandoOrderNotification $zalandoOrderNotification)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateZalandoOrderNotificationRequest  $request
     * @param  \App\Models\ZalandoOrderNotification  $zalandoOrderNotification
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateZalandoOrderNotificationRequest $request, ZalandoOrderNotification $zalandoOrderNotification)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\ZalandoOrderNotification  $zalandoOrderNotification
     * @return \Illuminate\Http\Response
     */
    public function destroy(ZalandoOrderNotification $zalandoOrderNotification)
    {
        //
    }

    /**
     * Remove the duplicated resource storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function sync(Request  $request, $order_number)
    {
        // $order_numbers = $this->model->distinct()->get(['order_number']);
        // dd($order_numbers->toArray());

        $diff_notifications = [];
        $notifications = $this->model
            // ->with('items')
            // ->with('delivery_details')
            // ->with('customer_billing_address')
            ->where('order_number', '=', $order_number)
            ->get();
        foreach ($notifications as $key => $notification) {
            $diff_notifications[$order_number][$key] = array_diff($notification->toArray(), $notifications[0]->toArray());
            $diff_notification = array_diff($notification->toArray(), $notifications[0]->toArray());
            if (
                isset($diff_notification['id']) &&
                isset($diff_notification['created_at']) &&
                isset($diff_notification['updated_at']) &&
                count($diff_notification) == 3
            ) {
                $notification->delete();
            }
        }
        return response()->json(['diff_notifications' => $diff_notifications], 200);
    }
}
