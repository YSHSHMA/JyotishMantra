<?php

namespace App\Http\Controllers\Vendor;

use App\Contracts\Repositories\CustomerRepositoryInterface;
use App\Http\Controllers\BaseController;
use App\Http\Requests\Vendor\CustomerRequest;
use App\Services\CustomerService;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use phpseclib3\Common\Functions\Strings;

class CustomerController extends BaseController
{
    public function __construct(
        private readonly CustomerRepositoryInterface $customerRepo,

    )
    {
    }
    public function index(?Request $request, string $type = null): View|Collection|LengthAwarePaginator|null|callable|RedirectResponse
    {
        // TODO: Implement index() method.
    }
    public function getList(Request $request):JsonResponse
    {
        $customers = $this->customerRepo->getCustomerNameList(
            request:$request,
            dataLimit: getWebConfig(name:'pagination_limit')
        );
        return response()->json($customers);
    }
    public function add(CustomerRequest $request,CustomerService $customerService):RedirectResponse
    {
        $this->customerRepo->add($customerService->getCustomerData(request: $request));
        Toastr::success(('customer_added_successfully'));
        return redirect()->back();
    }
    public function update(CustomerRequest $request,CustomerService $customerService):RedirectResponse
    {
        $this->customerRepo->update($customerService->getCustomerData(request: $request));
        Toastr::success(('customer_added_successfully'));
        return redirect()->back();
    }

}
