@php use App\Utils\Helpers; @endphp
<div class="inline-page-menu my-4">
    <ul class="list-unstyled">
        @if (Helpers::modules_permission_check('Sales & Transaction Report', 'Transaction Report', 'order-view'))
        <li class="{{ Request::is('admin/transaction/order-transaction-list') ?'active':'' }}"><a href="{{route('admin.transaction.order-transaction-list')}}">{{translate('order_Transactions')}}</a></li>
        @endif
        @if (Helpers::modules_permission_check('Sales & Transaction Report', 'Transaction Report', 'expense-view'))
        <li class="{{ Request::is('admin/transaction/expense-transaction-list') ?'active':'' }}"><a href="{{route('admin.transaction.expense-transaction-list')}}">{{translate('expense_Transactions')}}</a></li>
        @endif
        @if (Helpers::modules_permission_check('Sales & Transaction Report', 'Transaction Report', 'refund-view'))
        <li class="{{ Request::is('admin/report/transaction/refund-transaction-list') ?'active':'' }}"><a href="{{ route('admin.report.transaction.refund-transaction-list') }}">{{translate('refund_Transactions')}}</a></li>
        @endif
    </ul>
</div>
