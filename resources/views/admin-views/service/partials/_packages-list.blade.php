@php 
    $package=\App\Models\Package::where('id',$pac->package_id)->first();
@endphp
<tr>
    <td class="text-center">
        <span class="py-1">{{ $key+1 }}</span>
    </td>
    <td class="text-center">
        <span class="py-1">{{ $package->title }}</span>
    </td>
    <td class="text-center">
        <span class="py-1">₹ {{ $pac->package_price }}/-</span>
    </td>
</tr>