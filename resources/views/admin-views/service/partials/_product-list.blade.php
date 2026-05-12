@php
    $product_data = \App\Models\Product::where('id', $product)->first();
@endphp

    <tr>
        <td class="text-center">
            <span class="py-1">{{ $key+1 }}</span>
        </td>
        <td class="text-center">
            <span class="py-1">{{ $product_data->name }}</span>
        </td>
        <td class="text-center">
            <span class="py-1">₹ {{ $product_data->unit_price }}/-</span>
        </td>
    </tr>

