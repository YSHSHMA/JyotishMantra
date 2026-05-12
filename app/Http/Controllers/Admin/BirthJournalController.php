<?php

namespace App\Http\Controllers\Admin;

use App\Contracts\Repositories\BirthJournalKundaliRepositoryInterface;
use App\Contracts\Repositories\BirthJournalRepositoryInterface;
use App\Contracts\Repositories\TranslationRepositoryInterface;
use App\Enums\ViewPaths\Admin\BirthJournalPath;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\BirthJournalRequest;
use App\Models\Followsup;
use App\Models\KundaliLeads;
use App\Services\BirthJournalService;
use App\Traits\FileManagerTrait;
use App\Traits\PdfGenerator;
use App\Utils\Helpers;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Http\Request;



class BirthJournalController extends Controller
{
    use FileManagerTrait;
    use PdfGenerator;
    public function __construct(
        private readonly TranslationRepositoryInterface      $translationRepo,
        private readonly BirthJournalRepositoryInterface      $birthjoRepo,
        private readonly BirthJournalKundaliRepositoryInterface      $birthjournalKuRepo,
    ) {}

    public function Add()
    {
        $language = getWebConfig(name: 'pnc_language') ?? null;
        $defaultLanguage = $language[0];
        return view(BirthJournalPath::ADD[VIEW], compact('language', 'defaultLanguage'));
    }

    public function Store(BirthJournalRequest $request, BirthJournalService $service)
    {
        $array = $service->getAddData($request);
        // dd($array);
        $insert = $this->birthjoRepo->add(data: $array);
        $this->translationRepo->add(request: $request, model: 'App\Models\BirthJournal', id: $insert->id);
        Toastr::success(translate('Birth_Journal_added_successfully'));
        Helpers::editDeleteLogs('Birth Journal', 'Birth Journal', 'Insert');
        return redirect()->route(BirthJournalPath::LIST[REDIRECT]);
    }

    public function list(Request $request)
    {
        $getData = $this->birthjoRepo->getListWhere();
        return view(BirthJournalPath::LIST[VIEW], compact('getData'));
    }

    public function StatusUpdate(Request $request)
    {
        $data['status'] = $request->get('status', 0);
        $this->birthjoRepo->update(id: $request['id'], data: $data);
        return response()->json(['success' => 1, 'message' => translate('status_updated_successfully')], 200);
    }

    public function deleted(Request $request, BirthJournalService $service)
    {
        $old_data = $this->birthjoRepo->getFirstWhere(params: ['id' => $request['id']]);
        if ($old_data) {
            $service->removeImage($old_data);
            $this->birthjoRepo->delete(params: ['id' => $request['id']]);
            $this->translationRepo->delete('App\Models\BirthJournal', $request['id']);
            Helpers::editDeleteLogs('Birth Journal', 'Birth Journal', 'Delete');
            return response()->json(['success' => 1, 'message' => translate('Birth_Journal_Deleted_successfully')], 200);
        } else {
            return response()->json(['success' => 1, 'message' => translate('Birth_Journal_Deleted_Failed')], 400);
        }
    }

    public function Update(Request $request, $id)
    {
        $getData = $this->birthjoRepo->getFirstWhere(params: ['id' => $id], relations: ['translations']);
        if ($getData) {
            $language = getWebConfig(name: 'pnc_language') ?? null;
            $defaultLanguage = $language[0];
            return view(BirthJournalPath::UPDATE[VIEW], compact('language', 'defaultLanguage', 'getData'));
        } else {
            Toastr::success(translate('Birth_Journal_invalid_ID'));
            return redirect()->route(BirthJournalPath::LIST[REDIRECT]);
        }
    }

    public function UpdateSave(BirthJournalRequest $request, BirthJournalService $service, $id)
    {
        $getData = $this->birthjoRepo->getFirstWhere(params: ['id' => $id], relations: ['translations']);
        if ($getData) {
            $array = $service->getUpdateData($request, $getData);
            $this->birthjoRepo->update(id: $id, data: $array);
            $this->translationRepo->update(request: $request, model: 'App\Models\BirthJournal', id: $id);
            Toastr::success(translate('Birth_Journal_Updated_successfully'));
            Helpers::editDeleteLogs('Birth Journal', 'Birth Jounal', 'Update');
        }
        return redirect()->route(BirthJournalPath::LIST[REDIRECT]);
    }

    public function OrderList(Request $request)
    {
        $getData = $this->birthjournalKuRepo->getListWhere(relations: ['country', 'birthJournal_kundalimilan', 'astrologer'], orderBy: ['id' => 'desc'], searchValue: $request->get('searchValue'), dataLimit: getWebConfig(name: 'pagination_limit'));
        return view(BirthJournalPath::ALLORDER[VIEW], compact('getData'));
    }
    public function OrderPending(Request $request)
    {
        $getData = $this->birthjournalKuRepo->getListWhere(relations: ['country', 'birthJournal_kundalimilan', 'astrologer'], orderBy: ['id' => 'desc'], searchValue: $request->get('searchValue'), filters: ['payment_status' => 1, 'milan_verify' => 0, 'kundali_pdf' => $request->get('kundali_pdf')], dataLimit: getWebConfig(name: 'pagination_limit'));
        $types = 'pendings';
        return view(BirthJournalPath::ALLORDER[VIEW], compact('getData', 'types'));
    }
    public function OrderCompleted(Request $request)
    {
        $getData = $this->birthjournalKuRepo->getListWhere(relations: ['country', 'birthJournal_kundalimilan', 'astrologer'], orderBy: ['id' => 'desc'], searchValue: $request->get('searchValue'), filters: ['payment_status' => 1, 'milan_verify' => 1], dataLimit: getWebConfig(name: 'pagination_limit'));
        return view(BirthJournalPath::ALLORDER[VIEW], compact('getData'));
    }

    public function KundliMilandetails(Request $request, $id)
    {
        $kundalis = $this->birthjournalKuRepo->getFirstWhere(relations: ['country', 'birthJournal', 'userData', 'astrologer'], params: ['id' => $id]);
        $inHouseAstrologers = \App\Models\Astrologer\Astrologer::select('id', 'name')
            ->where('primary_skills', 4)
            ->where('type', 'in house')
            ->where('status', 1)
            ->get();
        $freelancerAstrologers = \App\Models\Astrologer\Astrologer::select('id', 'name')
            ->where('primary_skills', 4)
            ->where('type', 'freelancer')
            ->where('status', 1)
            ->get();
        return view(BirthJournalPath::VIEWKUNDLI[VIEW], compact('kundalis', 'inHouseAstrologers', 'freelancerAstrologers'));
    }

    public function KundliMilanVerify(Request $request, $id)
    {
        $pdfcheck = $this->birthjournalKuRepo->getFirstWhere(params: ['id' => $id], relations: ['astrologer', 'birthJournal']);
        if (isset($pdfcheck['kundali_pdf']) && !empty($pdfcheck['kundali_pdf'])) {
            $verify = $this->birthjournalKuRepo->update(id: $id, data: ['milan_verify' => 1]);
            if ($verify) {
                $getGst =  \App\Models\ServiceTax::find(1);
                $servicecommission = (($pdfcheck['birthJournal']['type'] == 'basic') ? ($pdfcheck['astrologer']['kundali_make_commission'] ?? 0) : ($pdfcheck['astrologer']['kundali_make_commission_pro'] ?? 0));
                $servicetax = $getGst['kundali'] ?? 0;

                $final_amount = $pdfcheck['amount'] ?? 0;
                $tax_commission = 0;
                $admin_commission = 0;
                if ($servicetax) {
                    $tax_commission = (($servicetax * $final_amount) / 100);
                    $final_amount = ($final_amount - $tax_commission);
                }
                if ($servicecommission) {
                    $admin_commission = (($servicecommission * $final_amount) / 100);
                    $final_amount = ($final_amount - $admin_commission);
                }
                $pandit_prices = (($pdfcheck['birthJournal']['type'] == 'basic') ? ($pdfcheck['astrologer']['kundali_make_charge'] ?? 0) : ($pdfcheck['astrologer']['kundali_make_charge_pro'] ?? 0));
                $this->birthjournalKuRepo->update(id: $id, data: ['tax_amount' => $tax_commission, 'admin_amount' => $admin_commission, 'final_amount' => $final_amount, 'pandit_price' => $pandit_prices]);

                $message_data['kundli_page'] = $pdfcheck['birthJournal']['pages'] ?? '';
                $message_data['kundli_type'] = $pdfcheck['birthJournal']['type'] ?? '';
                $message_data['orderId'] = $pdfcheck['order_id'] ?? '';
                $message_data['booking_date'] = date('d M,Y h:i A', strtotime($pdfcheck['created_at'] ?? ''));
                $message_data['final_amount'] = webCurrencyConverter(amount: (float)$pdfcheck['amount'] ?? 0);
                $message_data['customer_id'] =  $pdfcheck['user_id'];
                if ($pdfcheck['kundali_pdf']) {
                    $message_data['type'] = 'text-with-media';
                    $message_data['attachment'] = asset('storage/app/public/birthjournal/kundali_milan/' . $pdfcheck['kundali_pdf']);;
                }
                Helpers::whatsappMessage('kundali', 'kundali_milan_confirm_pdf', $message_data);
                Toastr::success(translate('PDF_Verify_successfully'));
            } else {
                Toastr::error(translate('PDF_Not_Verify_found'));
            }
        } else {
            Toastr::error(translate('Please_Upload_Kundali_milan_PDF'));
        }
        return back();
    }

    public function KundliMilanUploadPDF(Request $request, BirthJournalService $service, $id)
    {
        $request->validate([
            'kundli_milan_pdf' => 'required|mimes:pdf',
        ]);
        $pdfcheck = $this->birthjournalKuRepo->getFirstWhere(params: ['id' => $id]);
        if (!empty($pdfcheck)) {
            $array = $service->uploadKundliMilan($request, $pdfcheck);
            $array['reject_status'] = 1;
            $verify = $this->birthjournalKuRepo->update(id: $id, data: $array);
            if ($verify) {
                Toastr::success(translate('PDF_Upload_successfully'));
            } else {
                Toastr::error(translate('PDF_Upload_Failed'));
            }
        } else {
            Toastr::error(translate('Not_found_Information'));
        }
        return back();
    }

    public function PaidKundli(Request $request)
    {
        if ($request->type == 0 || $request->type == 1) {
            $getData = $this->birthjournalKuRepo->getListWhere(relations: ['country', 'birthJournal_kundali'], orderBy: ['id' => 'desc'], searchValue: $request->get('searchValue'), filters: ['payment_status' => 1, 'kundali_pdf' => $request->type], dataLimit: getWebConfig(name: 'pagination_limit'));
        } else {
            $getData = $this->birthjournalKuRepo->getListWhere(relations: ['country', 'birthJournal_kundali'], orderBy: ['id' => 'desc'], searchValue: $request->get('searchValue'), filters: ['payment_status' => 1], dataLimit: getWebConfig(name: 'pagination_limit'));
        }
        return view(BirthJournalPath::PAIDKUNDLI[VIEW], compact('getData'));
    }

    public function KundaliLeads(Request $request)
    {
        $getData = KundaliLeads::whereIn('status', [0, 2])->where('payment_status', '!=', '1')->orderBy('id','desc')->paginate(getWebConfig(name: 'pagination_limit'));
        return view(BirthJournalPath::LEADS[VIEW], compact('getData'));
    }

    public function KundaliLeadsDelete($id, Request $request)
    {
        $lead = KundaliLeads::where('id', $id)->first();
        if ($lead) {
            $lead->delete();
            Toastr::success(translate('lead_Delete_successfully'));
        } else {
            Toastr::error(translate('lead_Not_found'));
        }
        return back();
    }

    public function followup_store(Request $request)
    {
        $follows = [
            'customer_id' => $request->input('customer_id'),
            'pooja_id' => $request->input('pooja_id'),
            'lead_id' => $request->input('lead_id'),
            'type' => $request->input('type'),
            'follow_by' => $request->input('follow_by'),
            'follow_by_id' => $request->input('follow_by_id'),
            'last_date' => $request->input('last_date'),
            'message' => $request->input('message'),
            'next_date' => $request->input('next_date'),
        ];
        Followsup::create($follows);
        //  dd($followStore);
        Toastr::success(translate('lead_follow_up_successfully'));
        return back();
    }
    public function getFollowList($id)
    {
        $followlist = Followsup::where('lead_id', $id)->get();
        if ($followlist) {
            return response()->json($followlist);
        } else {
            return response()->json([], 200);
        }
    }

    public function AssignAstrologer(Request $request, $id)
    {
        $array['assign_pandit'] = $request->assign_pandit;
        $array['astrologer_type'] = $request->astrologer_type;
        $kundalis = $this->birthjournalKuRepo->getFirstWhere(params: ['id' => $id]);
        if (empty($kundalis['kundali_pdf'])) {
            $this->birthjournalKuRepo->update(id: $id, data: $array);
        } else {
            Toastr::error('PDF has been uploaded so you cannot change the astrologer');
        }
        return back();
    }

    public function GenerateInvoice($id)
    {
        $companyPhone = getWebConfig(name: 'company_phone');
        $companyEmail = getWebConfig(name: 'company_email');
        $companyName = getWebConfig(name: 'company_name');
        $companyWebLogo = getWebConfig(name: 'company_web_logo');
        $details = $this->birthjournalKuRepo->getFirstWhere(params: ['id' => $id], relations: ['country', 'birthJournal', 'userData', 'astrologer']);
        // dd($details);
        $mpdf_view = \Illuminate\Support\Facades\View::make('admin-views.birth_journal.order_invoice', compact('details', 'companyPhone', 'companyEmail', 'companyName', 'companyWebLogo'));
        $this->generatePdf($mpdf_view, 'order_invoice_', $details['order_id']);
    }

    public function ReUploadBithPDF(Request $request)
    {
        $findData = $this->birthjournalKuRepo->getFirstWhere(params: ['id' => $request->id], relations: ['birthJournal']);
        $companyPhone = getWebConfig(name: 'company_phone');
        $companyEmail = getWebConfig(name: 'company_email');
        $companyName = getWebConfig(name: 'company_name');
        $kundaliPdf = '';
        if ($findData && $findData['birthJournal']['name'] == 'kundali') {
            $apiData = array(
                'name' => $findData['name'],
                'gender' => $findData['gender'],
                'day' => date('d', strtotime($findData['bod'])),
                'month' => date('m', strtotime($findData['bod'])),
                'year' => date('Y', strtotime($findData['bod'])),
                'hour' => date('H', strtotime($findData['time'])),
                'min' => date('i', strtotime($findData['time'])),
                'lat' => $findData['lat'],
                'lon' => $findData['log'],
                'language' => $findData['language'],
                'tzone' => $findData['tzone'],
                'place' => $findData['state'],
                'chart_style' => $findData['chart_style'],
                'footer_link' => route('home'),
                'logo_url' => dynamicStorage(path: "storage/app/public/company/" . getWebConfig(name: 'company_web_logo')),
                'company_name' => $companyName,
                'company_info' => 'Description of Mahakal Astrotech (OPC) PVT LTD@2025.',
                'domain_url' => route('home'),
                'company_email' => $companyEmail,
                'company_landline' => $companyPhone,
                'company_mobile' => $companyPhone
            );

            $kundali_Pdf = '';
            if (($findData['birthJournal']['type'] ?? "basic") == "basic") {
                $kundali_Pdf = json_decode(\App\Utils\ApiHelper::astroApi('https://pdf.astrologyapi.com/v1/basic_horoscope_pdf', $apiData['language'], $apiData), true);
            } else if (($findData['birthJournal']['type'] ?? "basic") == "pro") {
                $language = in_array($findData['language'], ['hi', 'en']) ? $findData['language'] : 'hi';
                $kundali_Pdf = json_decode(\App\Utils\ApiHelper::astroApi('https://pdf.astrologyapi.com/v1/pro_horoscope_pdf', $language, $apiData), true);
            }
            if (!empty($kundali_Pdf['pdf_url'])) {
                $fileName = $kundaliPdf = $findData['birthJournal']['pages'] . 'page' . $apiData['language'] . 'kundali' . time() . '.pdf';
                $filePath = storage_path('app/public/birthjournal/kundali/' . $fileName);

                if (!file_exists(dirname($filePath))) {
                    mkdir(dirname($filePath), 0755, true);
                }
                $pdfContent = file_get_contents($kundali_Pdf['pdf_url']);
                file_put_contents($filePath, $pdfContent);
                $message_data['kundli_page'] = $findData['birthJournal']['pages'] ?? '';
                $message_data['kundli_type'] = $findData['birthJournal']['type'] ?? '';
                $message_data['final_amount'] = webCurrencyConverter(amount: (float)$findData['amount'] ?? 0);
                $message_data['customer_id'] =  $findData['user_id'];
                $message_data['orderId'] = $findData['order_id'] ?? '';
                $message_data['booking_date'] = date('Y-m-d', strtotime($findData['created_at'] ?? ''));
                if ($filePath) {
                    $message_data['type'] = 'text-with-media';
                    $message_data['attachment'] = asset('storage/app/public/birthjournal/kundali/' . $fileName);
                }
                Helpers::whatsappMessage('kundali', 'kundali_pdf', $message_data);
            }
            $array['milan_verify'] = 1;
            $array['kundali_pdf'] = $kundaliPdf;
            \App\Models\BirthJournalKundali::where('id', $request->id)->update($array);
        }
        return redirect()->route('admin.birth_journal.paid_kundli', ['type' => 0]);
    }

    public function KundliMilanReject(Request $request, $id)
    {
        $request->validate([
            'message' => 'required',
        ]);
        $pdfcheck = $this->birthjournalKuRepo->getFirstWhere(params: ['id' => $id]);
        if (!empty($pdfcheck)) {
            $array['reject_status'] = 2;
            $array['reject_message'] = $request->message;
            $verify = $this->birthjournalKuRepo->update(id: $id, data: $array);
            if ($verify) {
                Toastr::success(translate('reject_pdf_Updated_successfully'));
            } else {
                Toastr::error(translate('reject_PDF_Updated_Failed'));
            }
        } else {
            Toastr::error(translate('Not_found_Information'));
        }
        return back();
    }
}