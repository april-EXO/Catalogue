<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;

class OrderController extends Controller
{
	public function submitOrder(Request $request)
	{
		$order = new Order();
		$order->recipient_name = $request->input('name');
		$order->address = $request->input('address');
		$order->phone_number = $request->input('phone');
		$order->items = json_encode($request->input('cartItems'));
		$order->status = 'Submitted';
		$order->save();

		return response()->json(['message' => 'Order submitted successfully']);
	}

	public function getOrder(Request $request)
	{
		$orders = Order::all();

		return response()->json($orders);
	}

	public function changeStatus(Request $request)
	{
		$orderId = $request->input('id');
		$status = $request->input('status');

		$order = Order::find($orderId);

		if (!$order) {
			return response()->json(['message' => 'Order not found'], 404);
		}

		$order->status = $status;
		$order->save();

		return response()->json(['message' => 'Order status updated successfully']);
	}

	public function getOrderWithID(Request $request, $id)
	{
		$order = Order::find($id);

		if (!$order) {
			return response()->json(['message' => 'Order not found'], 404);
		}

		return response()->json($order);
	}
}
