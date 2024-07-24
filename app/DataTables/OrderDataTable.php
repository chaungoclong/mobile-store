<?php

namespace App\DataTables;

use App\Enums\OrderStatus;
use App\Enums\PaymentStatus;
use App\Helpers\Helpers;
use App\Models\Order;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Services\DataTable;

class OrderDataTable extends DataTable
{
    /**
     * Build DataTable class.
     *
     * @param Builder $query Results from query() method.
     * @return EloquentDataTable
     */
    public function dataTable(Builder $query): EloquentDataTable
    {
        return (new EloquentDataTable($query))
            ->addIndexColumn()
            ->filter(function (Builder $query) {
                if (request()?->has('search')) {
                    $query->where(function (Builder $searchQuery) {
                        $searchQuery
                            ->where('email', 'like', "%" . request('search') . "%")
                            ->orWhere('phone', 'like', "%" . request('search') . "%")
                            ->orWhere('address', 'like', "%" . request('search') . "%")
                            ->orWhereHas('customer', function (Builder $customerQuery) {
                                $customerQuery
                                    ->where('name', 'like', "%" . request('search') . "%")
                                    ->orWhere('phone', 'like', "%" . request('search') . "%")
                                    ->orWhere('address', 'like', "%" . request('search') . "%");
                            });
                    });
                }

                if (request()?->has('status')) {
                    $status = request('status');
                    if (is_numeric($status)) {
                        $status = (int)$status;
                        $query->where('status', $status);
                    }
                }

                if (request()?->has('payment_status')) {
                    $paymentStatus = request('payment_status');
                    if (is_numeric($paymentStatus)) {
                        $paymentStatus = (int)$paymentStatus;
                        $query->where('payment_status', $paymentStatus);
                    }
                }

                if (request()?->has('payment_method')) {
                    $paymentMethod = request('payment_method');
                    if (is_numeric($paymentMethod)) {
                        $paymentMethod = (int)$paymentMethod;
                        $query->where('payment_method_id', $paymentMethod);
                    }
                }
            })
            ->rawColumns(['action', 'status', 'payment_status', 'payment_method', 'customer'])
            ->editColumn('created_at', function ($item) {
                return Carbon::parse($item->created_at)->format('d-m-Y H:i:s');
            })
            ->addColumn('action', function ($item) {
                return '
                <a href="' . route('admin.order.show', ['id' => $item->id]) . '"
                   class="btn btn-icon btn-sm btn-primary tip" title="Chỉnh Sửa">
                    <i class="fa fa-pencil" aria-hidden="true"></i>
                </a>';
            })
            ->editColumn('status', function ($item) {
                $status = OrderStatus::tryFrom((int)$item->status);
                if ($status !== null) {
                    return $status->toHtml();
                }
                return '';
            })
            ->editColumn('payment_status', function ($item) {
                $paymentStatus = PaymentStatus::tryFrom((int)$item->payment_status);
                if ($paymentStatus !== null) {
                    return $paymentStatus->toHtml();
                }
                return '';
            })
            ->editColumn('amount', function ($item) {
                return Helpers::formatVietnameseCurrency($item?->amount);
            })
            ->addColumn('payment_method', function ($item) {
                return $item?->payment_method?->name ?? '';
            })
            ->addColumn('customer', function ($item) {
                return '<a href="' . route('admin.user_show', ['id' => $item?->customer?->id ?? '']
                    ) . '" class="text-info">' . ($item?->customer?->name ?? '') . '</a>';
            });
    }

    /**
     * Get query source of dataTable.
     *
     * @param Order $model
     * @return Builder
     */
    public function query(Order $model): Builder
    {
        return $model->newQuery()->with(['customer:id,name', 'paymentMethod:name']);
    }

    /**
     * Optional method if you want to use html builder.
     *
     * @return HtmlBuilder
     */
    public function html(): HtmlBuilder
    {
        return $this->builder()
            ->setTableId('orders-table')
            ->columns($this->getColumns())
            ->minifiedAjax()
            ->stateSave(true)
            ->orderBy(1)
            ->selectStyleSingle()
            ->responsive()
            ->autoWidth(true)
            ->dom('rltp')
            ->addTableClass('table-hover')
            ->ajax([
                'url' => route('admin.order.index'),
                'type' => 'GET',
                'data' => "function(d) { d.search = $('#search').val(); d.status = $('#status').val(); d.payment_status = $('#paymentStatus').val(); d.payment_method = $('#paymentMethod').val(); }",
            ]);
    }

    /**
     * Get the dataTable columns definition.
     *
     * @return array
     */
    public function getColumns(): array
    {
        return [
            Column::computed('DT_RowIndex', 'STT'),
            Column::make('order_code')->title('Mã đơn hàng'),
            Column::computed('customer', 'Khách hàng'),
            Column::computed('payment_method', 'Phương thức thanh toán'),
            Column::computed('payment_status', 'Trạng thái thanh toán'),
            Column::computed('status', 'Trạng thái thực hiện'),
            Column::make('amount')->title('Tổng cộng'),
            Column::make('created_at')->title('Tạo lúc'),
            Column::computed('action', 'Hành động')
        ];
    }
}
