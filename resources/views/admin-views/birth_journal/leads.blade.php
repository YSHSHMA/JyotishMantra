@php use App\Utils\Helpers; @endphp
@extends('layouts.back-end.app')

@section('title', translate('Kundli_lead'))
@push('css_or_js')
<link href="https://unpkg.com/gijgo@1.9.14/css/gijgo.min.css" rel="stylesheet" type="text/css" />

<style>
    .gj-datepicker-bootstrap [role=right-icon] button .gj-icon {
        top: 14px;
        right: 5px;
    }


.bg-label-primary {
  background-color: #007bff;
  color: #fff;
}

.bg-label-primary:hover {
  background-color: #0056b3;
}

.bg-label-danger {
  background-color: #dc3545;
  color: #fff;
}

.bg-label-danger:hover {
  background-color: #c82333;
}

.bg-label-success {
  background-color: #28a745;
  color: #fff;
}

.bg-label-success:hover {
  background-color: #218838;
}

.bg-label-info {
  background-color: #17a2b8;
  color: #fff;
}

.bg-label-info:hover {
  background-color: #117a8b;
}

.bg-label-warning {
  background-color: #ffc107;
  color: #212529;
}

.bg-label-warning:hover {
  background-color: #e0a800;
}

.dropdown-menufollow {
  background-color: #fff;
  border: 1px solid #ccc;
  border-radius: 0.375rem;
  box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.1);
  padding: 1rem;
  width: 225px;
  margin-right: 13rem;
  text-align: center;
  display: flex;
  gap: 0.5rem;
  position: absolute;
}

.d-flex {
  display: flex;
}

.justify-content-center {
  justify-content: center;
}

.gap-2 {
  gap: 0.5rem;
}
</style>
@endpush
@section('content')
<div class="content container-fluid">
    <div class="mb-3">
        <h2 class="h1 mb-0 d-flex gap-2">
            {{ translate('Kundli_Lead') }}
            <span class="badge badge-soft-dark radius-50 fz-14">{{ $getData->total() }}</span>
        </h2>
    </div>
    <div class="row mt-20">
        <div class="col-md-12">
            <div class="card">
                <div class="px-3 py-4">
                    <div class="row g-2 flex-grow-1">
                        <div class="col-sm-8 col-md-6 col-lg-4">
                            <form action="{{ url()->current() }}" method="GET">
                                <div class="input-group input-group-custom input-group-merge">
                                    <div class="input-group-prepend">
                                        <div class="input-group-text">
                                            <i class="tio-search"></i>
                                        </div>
                                    </div>
                                    <input id="datatableSearch_" type="search" name="searchValue" class="form-control"
                                        placeholder="{{ translate('search_by_name') }}" aria-label="{{ translate('search_by_name') }}" value="{{ request('searchValue') }}" required>
                                    <button type="submit" class="btn btn--primary input-group-text">{{ translate('search') }}</button>
                                </div>
                            </form>
                        </div>
                        <div class="col-sm-4 col-md-6 col-lg-8 d-flex justify-content-end">

                           

                        </div>
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover table-borderless table-thead-bordered table-nowrap table-align-middle card-table w-100 text-start">
                            <thead class="thead-light thead-50 text-capitalize">
                                <tr>
                                    <th>{{ translate('SL') }}</th>
                                    <th>{{ translate('name') }}</th>
                                    <th class="text-center">{{ translate('Phone_no') }}</th>
                                    <th class="text-center">{{ translate('amount') }}</th>
                                    <th class="text-center">{{ translate('type') }}</th>
                                    <th class="text-center">{{ translate('booking_date') }}</th>
                                    @if (Helpers::modules_permission_check('Birth Journal', 'Lead', 'delete') || Helpers::modules_permission_check('Birth Journal', 'Lead', 'follow-up') || Helpers::modules_permission_check('Birth Journal', 'Lead', 'follow-up-history') || Helpers::modules_permission_check('Birth Journal', 'Lead', 'call'))
                                    <th class="text-center"> {{ translate('action') }}</th>
                                    @endif
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($getData as $key => $lead)
                                <tr>
                                    <td>{{ $getData->firstItem()+$key }}</td>
                                    <td> {{ ($lead['user_name']??"") }}  </td>
                                    <td> {{ ($lead['phone_no']??"") }}  </td>
                                    <td class="text-center">{{ ($lead['amount']??"") }}</td>
                                    <td class="text-center">{{  translate(\App\Models\BirthJournal::where('id',($lead['kundali_id']??""))->first()['name']??"") }}</td>
                                    <td class="text-center">{{ date('d M,Y H:i:s',strtotime($lead['booking_date']??"")) }}</td>
                                    <td class="text-center">
                                        @if (Helpers::modules_permission_check('Birth Journal', 'Lead', 'delete') || Helpers::modules_permission_check('Birth Journal', 'Lead', 'follow-up') || Helpers::modules_permission_check('Birth Journal', 'Lead', 'follow-up-history') || Helpers::modules_permission_check('Birth Journal', 'Lead', 'call'))
                                    <div class="d-flex justify-content-center gap-2">
                                                    <button class="btn" type="button" id="LeadFollowUP-{{ $lead['id'] }}" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                        <i class="tio-format-bullets text-muted"></i>
                                                    </button>
                                                    <div class="dropdown-menufollow" style="display: none;" id="LeadFollowList-{{ $lead['id'] }}">
                                                        @if (Helpers::modules_permission_check('Birth Journal', 'Lead', 'delete'))
                                                        <a href="{{ route('admin.birth_journal.lead-delete', ['id' => $lead['id']]) }}" class="btn btn-icon bg-label-danger waves-effect waves-light" onclick="return confirm('Are your sure, you want to delete');" data-toggle="tooltip" aria-label="Delete" data-bs-original-title="Delete"><i class="tio-delete-outlined"></i></a>
                                                        @endif
                                                        @if (Helpers::modules_permission_check('Birth Journal', 'Lead', 'follow-up'))
                                                        <a href="javascript:void(0)" class="btn btn-icon bg-label-success waves-effect waves-light"  
                                                            data-custId="{{ $lead['user_id'] }}" 
                                                            data-PoojaId="{{ $lead['kundali_id'] }}"  
                                                            data-type ='paid_kundali'
                                                            data-leadsId="{{ $lead['id'] }}" 
                                                            onclick="followUp(this)" 
                                                            data-toggle="tooltip" 
                                                            aria-label="Follow Up" 
                                                            data-bs-original-title="Follow Up">
                                                            <i class="tio-settings-back"></i>
                                                        </a>
                                                        @endif
                                                        @if (Helpers::modules_permission_check('Birth Journal', 'Lead', 'follow-up-history'))
                                                        <a href="javascript:0" class="btn btn-icon bg-label-info waves-effect waves-light"  data-leadsId="{{ $lead['id'] }}"  data-type="{{ $lead['type'] }}"  onclick="followHistory(this)" data-toggle="tooltip" aria-label="Follow Up History" data-bs-original-title="Follow Up History"><i class="tio-history"></i></a>
                                                        @endif
                                                        @if (Helpers::modules_permission_check('Birth Journal', 'Lead', 'call'))
                                                        <a href="tel:{{ $lead['phone_no'] }}" class="btn btn-icon bg-label-warning waves-effect waves-light" data-toggle="tooltip" aria-label="Call" data-bs-original-title="Call"><i class="tio-call"></i></a>
                                                        @endif
                                                    </div>
                                                </div>
                                    </td>
                                    @endif
                                   
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="table-responsive mt-4">
                    <div class="d-flex justify-content-lg-end">
                        {{ $getData->links() }}
                    </div>
                </div>
                @if(count($getData)==0)
                <div class="text-center p-4">
                    <img class="mb-3 w-160" src="{{ dynamicAsset(path: 'public/assets/back-end/svg/illustrations/sorry.svg') }}" alt="">
                    <p class="mb-0">{{ translate('no_data_to_show') }}</p>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="followUpModal" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="followUpModalTitleId">
                        Follow Up
                    </h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form action="{{ route('admin.birth_journal.lead-follow-up') }}" method="POST">
                   @csrf 
                   @php
                    if (auth('admin')->check()) {
                        $adminId = App\Models\Admin::where('id', auth('admin')->id())->first();
                    }
                    @endphp
                    <div class="modal-body">
                        <div class="row">
                            <input type="hidden" name="follow_by_id" id="followUpFollowId" class="form-control" value="{{ $adminId['id'] }}">
                            <input type="hidden" name="follow_by" id="followUpFollowId" class="form-control" value="{{ $adminId['name'] }}">
                            <input type="hidden" name="pooja_id" id="followUpPoojaId" class="form-control">
                            <input type="hidden" name="type" id="followUpType" class="form-control">
                            <input type="hidden" name="customer_id" id="followUpUserId" class="form-control">
                            <input type="hidden" name="lead_id" id="followUpLeadID" class="form-control">
                        </div>
                        <div class="row">
                            <div class="col-md-12 mb-2">
                                <label for="" class="form-label">Date</label>
                                <input type="text" name="last_date" class="form-control" value="{{ now() }}" readonly="" required="">
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12 mb-2">
                                <label for="" class="form-label">Message</label>
                                <textarea name="message" rows="5" class="form-control" placeholder="Enter Message"></textarea>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12 mb-2">
                                <label for="" class="form-label">Next Follow Up Date</label>
                                <input type="text" name="next_date" class="form-control" id="next_date" required="">
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary waves-effect waves-light" data-dismiss="modal">
                            Close
                        </button>
                        <button type="submit" class="btn btn-primary waves-effect waves-light">Submit</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

{{-- Histore --}}
    <div class="modal fade" id="followUpHistoryModal" tabindex="-1">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="followUpModalHistoryTitleId">
                        Follow Up History
                    </h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="table-responsive">
                            <table class="table">
                                <thead class="bg-dark">
                                    <tr>
                                        <th scope="col" class="text-white">#</th>
                                        <th scope="col" class="text-white">Follow By</th>
                                        <th scope="col" class="text-white">Last Followup</th>
                                        <th scope="col" class="text-white">Message</th>
                                        <th scope="col" class="text-white">Next Followup</th>
                                    </tr>
                                </thead>
                                <tbody id="followUpPoojaHistoryTBody">
                                </tbody>
                            </table>
                        </div>

                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary waves-effect waves-light" data-dismiss="modal">
                        Close
                    </button>
                </div>
            </div>
        </div>
    </div>

@endsection

@push('script')
<script src="{{ dynamicAsset(path: 'public/assets/back-end/js/products-management.js') }}"></script>
</script>
    <script src="https://unpkg.com/gijgo@1.9.14/js/gijgo.min.js" type="text/javascript"></script>
    <script>
        // datepicker
        var today = new Date();
        var tomorrow = new Date(today);
        tomorrow.setDate(today.getDate());
        $('#next_date').datepicker({
            uiLibrary: 'bootstrap4',
            format: 'yyyy/mm/dd',
            modal: true,
            footer: true,
            minDate: tomorrow,
            todayHighlight: true
        });

     
    </script>
    <script>
        $('#date_type').change(function (e) { 
            e.preventDefault();
            
            var value = $(this).val();
            if(value == 'custom_date'){
                $('#from-to-div').show();
            }
            else{
                $('#from-to-div').hide();
            }
        });
    </script>
      <script>
        $(document).ready(function() {
          $('.btn[data-bs-toggle="dropdown"]').click(function() {
              var $dropdownMenu = $(this).siblings('.dropdown-menufollow');
              $('.dropdown-menufollow').not($dropdownMenu).hide(); // Hide all other dropdown menus
              $dropdownMenu.toggle();
          });
      });
      </script>
<script>
          function followUp(that) {
              var id = $(that).attr('data-custId');
              var poojaid = $(that).attr('data-PoojaId');
              var type = $(that).attr('data-type');
              var lead = $(that).attr('data-leadsId');
              console.log(lead);
              $('#followUpLeadID').val(lead);
              $('#followUpUserId').val(id);
              $('#followUpPoojaId').val(poojaid);
              $('#followUpType').val(type);
              $('#followUpModal').modal('show');
          }
      </script>
      <script>
          function followHistory(that) {
              var leadId = $(that).attr('data-leadsId');
              var types= $(that).attr('data-type');
              var row = "";
              $.ajax({
                url: "{{ url('admin/birth_journal/kundli-follow-list') }}/" + leadId,
                  method: 'GET',
                //   data:{id:leadId},
                  success: function(response) {
                    console.log(response);
                      $('#followUpPoojaHistoryTBody').html('');
                      if(response.length != 0){
                          $.each(response, function (key, value) { 
                               row += `<tr> <td>${key+1}</td> <td>${value.follow_by}</td> <td>${new Date(value.last_date).toLocaleDateString('en-GB')}</td> <td>${value.message}</td> <td>${new Date(value.next_date).toLocaleDateString('en-GB')}</td> </tr>`;
                          });
                      }
                      else{
                          row = '<tr> <td colspan="5" class="text-center"> No Data Available </td> </tr>';
                      }
                      $('#followUpPoojaHistoryTBody').append(row);
                  },
              });
              $('#followUpHistoryModal').modal('show');
          }
      </script>
@endpush