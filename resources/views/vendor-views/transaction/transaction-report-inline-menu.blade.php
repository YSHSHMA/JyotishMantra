<div class="inline-page-menu my-4">
    <ul class="list-unstyled">
        <li class="{{ Request::is('vendor/transaction/order-list') ?'active':'' }}"><a href="{{route('vendor.transaction.order-list')}}">{{translate('order_Transactions')}}</a></li>
        <li class="{{ Request::is('vendor/transaction/expense-list') ?'active':'' }}"><a href="{{route('vendor.transaction.expense-list')}}">{{translate('expense_Transactions')}}</a></li>
        <li class="{{ Request::is('vendor/report/transaction/refund-transaction-list') ?'active':'' }}"><a href="{{ route('vendor.report.transaction.refund-transaction-list') }}">{{translate('refund_Transactions')}}</a></li>
    </ul>
</div>
