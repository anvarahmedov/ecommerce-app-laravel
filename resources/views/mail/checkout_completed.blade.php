<x-mail::message>
<h1 style="text-align:center; font-size:24px;">Payment was completed successfully</h1>
@foreach ($orders as $order)
<x-mail::table>
<table>
<tbody>
<tr>
<td>Seller</td>
<td>
<a href="{{ url('/') }}">
{!! $order->vendorUser->vendor->store_name !!}
</a>
</td>
</tr>
<tr>
<td>Order #</td>
<td>#{{ $order->id }}</td>
</tr>
<tr>
<td>Items</td>
<td>#{{ $order->orderItems->count() }}</td>
</tr>
<tr>
<td>Total</td>
<td>{!! \Illuminate\Support\Number::currency($order->total_price) !!}</td>
</tr>
</tbody>
</table>
</x-mail::table>
<x-mail::table>
<table>
<thead>
<tr>
<th>Item</th>
<th>Quantity</th>
<th>Price</th>
</tr>
</thead>
<tbody>
@foreach($order->orderItems as $item)
<tr>
<td>
<table>
<tbody>
<tr>
<td padding="5" style="padding:5px">
<img style="min-width: 60px; max-width: 60px;" src="{!! $item->product->getImageForOptions($item->variation_type_option_ids) !!}" alt=""/>
</td>
<td style="font-size:13px; padding:5px">
{!! $item->product->title !!}
</td>
</tr>
</tbody>
</table>
</td>
<td>{!! $item->quantity !!}</td>
<td>{!! \Illuminate\Support\Number::currency($item->price) !!}</td>
</tr>
@endforeach
</tbody>
</table>
</x-mail::table>
<x-mail::button :url="url('/orders/' . $order->id)">
View Order Details
</x-mail::button>
@endforeach
<x-mail::panel>
Neque porro quisquam est qui dolorem ipsum quia dolor sit amet, consectetur, adipisci velit...
</x-mail::panel>
<x-mail::subcopy>
Neque porro quisquam est qui dolorem ipsum quia dolor sit amet, consectetur, adipisci velit...
</x-mail::subcopy>
Thanks,<br>
{!! config('app.name') !!}
</x-mail::message>

