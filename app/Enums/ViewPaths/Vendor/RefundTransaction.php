<?php

namespace App\Enums\ViewPaths\Vendor;

enum RefundTransaction
{
    const INDEX = [
        URI => 'refund-transaction-list',
        VIEW => 'vendor-views.refund-transaction.list'
    ];
    const EXPORT = [
        URI => 'refund-transaction-export',
        VIEW => ''
    ];
    const GENERATE_PDF = [
        URI => 'refund-transaction-summary-pdf',
        VIEW => 'vendor-views.refund_transaction_summary_report_pdf'
    ];
}
