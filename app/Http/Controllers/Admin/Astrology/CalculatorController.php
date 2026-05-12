<?php

namespace App\Http\Controllers\Admin\Astrology;

use Illuminate\Http\Request;
use App\Contracts\Repositories\CalculatorRepositoryInterface;
use App\Contracts\Repositories\TranslationRepositoryInterface;
use App\Enums\ViewPaths\Admin\Calculator;
use App\Http\Controllers\BaseController;
use App\Http\Requests\Admin\CalculatorAddRequest;
use App\Http\Requests\Admin\CalculatorUpdateRequest;
use App\Services\CalculatorService;
use App\Utils\Helpers;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class CalculatorController extends BaseController
{
    public function __construct(
        private readonly CalculatorRepositoryInterface           $calculatorRepo,
        private readonly TranslationRepositoryInterface     $translationRepo,
    )
    {
    }

    /**
     * @param Request|null $request
     * @param string|null $type
     * @return View Index function is the starting point of a controller
     * Index function is the starting point of a controller
     */
    public function index(Request|null $request, string $type = null): View
    {
        return $this->getList($request);
    }

    public function getList(Request $request): Application|Factory|View
    {
        $calculators = $this->calculatorRepo->getListWhere(orderBy:['id'=>'desc'],searchValue:$request->get('searchValue'), dataLimit: getWebConfig(name: 'pagination_limit'));
        return view(Calculator::LIST[VIEW], compact('calculators'));
    }

    public function getAddView(): View
    {
        $language = getWebConfig(name: 'pnc_language') ?? null;
        $defaultLanguage = $language[0];
        return view(Calculator::ADD[VIEW], compact( 'language', 'defaultLanguage'));
    }

    public function add(CalculatorAddRequest $request, CalculatorService $calculatorService): RedirectResponse
    {
        $dataArray = $calculatorService->getAddData(request:$request);
        $savedAttributes = $this->calculatorRepo->add(data:$dataArray);
        $this->translationRepo->add(request:$request, model:'App\Models\Calculator', id:$savedAttributes->id);

        Toastr::success(translate('calculator_added_successfully'));
        Helpers::editDeleteLogs('Calculator','Calculator','Insert');
        return redirect()->route('admin.calculator.list');
    }

    public function getUpdateView(string|int $id): View|RedirectResponse
    {
        $calculator = $this->calculatorRepo->getFirstWhere(params:['id'=>$id], relations: ['translations']);
        $language = getWebConfig(name: 'pnc_language') ?? null;
        $defaultLanguage = $language[0];
        return view(Calculator::UPDATE[VIEW], compact('calculator', 'language', 'defaultLanguage'));
    }

    public function update(CalculatorUpdateRequest $request, $id, CalculatorService $calculatorService): RedirectResponse
    {
        $calculator = $this->calculatorRepo->getFirstWhere(params:['id'=>$request['id']]);
        $dataArray = $calculatorService->getUpdateData(request: $request, data:$calculator);
        $this->calculatorRepo->update(id:$request['id'], data:$dataArray);
        $this->translationRepo->update(request:$request, model:'App\Models\Calculator', id:$request['id']);
        Helpers::editDeleteLogs('Calculator','Calculator','Update');
        Toastr::success(translate('calculator_updated_successfully'));
        return redirect()->route('admin.calculator.list');
    }

    public function delete(string|int $id, CalculatorService $calculatorService): RedirectResponse
    {
        $calculator = $this->calculatorRepo->getFirstWhere(params:['id'=>$id]);
        if($calculator){
            $this->translationRepo->delete(model:'App\Models\Calculator', id:$id);
            $calculatorService->deleteImage(data:$calculator);
            $this->calculatorRepo->delete(params: ['id'=>$id]);
            Helpers::editDeleteLogs('Calculator','Calculator','Delete');
            Toastr::success(translate('calculator_deleted_successfully'));
        }else {
            Toastr::error(translate('error occured'));
        }
        return redirect()->back();
    }
}
