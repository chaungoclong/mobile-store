<?php

namespace App\DataTables;

use App\Enums\StockStatus;
use App\Helpers\Helpers;
use App\Models\Product;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Services\DataTable;

class ProductDataTable extends DataTable
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
                            ->where('name', 'like', "%" . request('search') . "%");
                    });
                }

                if (request()?->has('stock_status')) {
                    $stockStatus = request('stock_status');
                    if (is_string($stockStatus) && trim($stockStatus) !== '') {
                        $stockStatus = strtolower(trim($stockStatus));
                        if ($stockStatus === StockStatus::IN_STOCK->value) {
                            $query->having('product_details_sum_quantity', '>=', 10);
                        } elseif ($stockStatus === StockStatus::RUNNING_OUT->value) {
                            $query
                                ->having('product_details_sum_quantity', '>', 0)
                                ->having('product_details_sum_quantity', '<', 10);
                        } elseif ($stockStatus === StockStatus::OUT_OF_STOCK->value) {
                            $query->having('product_details_sum_quantity', '<=', 0);
                        }
                    }
                }

                if (is_numeric(request('category_id'))) {
                    $query->where('category_id', (int)request('category_id'));
                }

                if (is_numeric(request('producer_id'))) {
                    $query->where('producer_id', (int)request('producer_id'));
                }
            })
            ->rawColumns(['image', 'action', 'stock_status'])
            ->editColumn('image', function ($item) {
                return '<img src="' . Helpers::get_image_product_url($item->image) . '" width="100px">';
            })
            ->editColumn('created_at', function ($item) {
                return Carbon::parse($item->created_at)->format('d-m-Y H:i:s');
            })
            ->editColumn('rate', function ($item) {
                return $item?->rate . '/5';
            })
            ->addColumn('action', function ($item) {
                return '
                <a href="' . route('admin.product.edit', ['id' => $item->id]) . '"
                   class="btn btn-icon btn-sm btn-primary tip" title="Chỉnh Sửa">
                    <i class="fa fa-pencil" aria-hidden="true"></i>
                </a>
                <a href="javascript:void(0);" data-id="' . $item->id . '"
                   class="btn btn-icon btn-sm btn-danger deleteDialog tip" title="Xóa"
                   data-url="' . route('admin.product.delete') . '">
                    <i class="fa fa-trash"></i>
                </a>';
            })
            ->addColumn('status', function ($item) {
                if ($item->end_date >= date('Y-m-d')) {
                    return '<span class="label-success label">Còn Hạn</span>';
                }

                return '<span class="label-danger label">Hết Hạn</span>';
            })
            ->addColumn('producer', function ($item) {
                return $item?->producer?->name;
            })
            ->addColumn('category', function ($item) {
                return $item?->category?->name;
            })
            ->addColumn('quantity', function ($item) {
                return $item?->product_details_sum_quantity ?? 0;
            })
            ->addColumn('stock_status', function ($item) {
                $quantity = (int)($item?->product_details_sum_quantity ?? 0);
                if ($quantity >= 10) {
                    return '<span class="label-success label">Còn hàng</span>';
                }

                if ($quantity > 0) {
                    return '<span class="label-warning label">Sắp hết hàng</span>';
                }

                return '<span class="label-danger label">Hết hàng</span>';
            });
    }

    /**
     * Get query source of dataTable.
     *
     * @param Product $model
     * @return Builder
     */
    public function query(Product $model): Builder
    {
        return $model->newQuery()
            ->with([
                'category:id,name',
                'producer:id,name'
            ])
            ->withSum('productDetails', 'quantity');
    }

    /**
     * Optional method if you want to use html builder.
     *
     * @return HtmlBuilder
     */
    public function html(): HtmlBuilder
    {
        return $this->builder()
            ->setTableId('products-table')
            ->columns($this->getColumns())
            ->minifiedAjax()
            ->orderBy(10)
            ->responsive()
            ->autoWidth(true)
            ->dom('rltp')
            ->addTableClass('table-hover')
            ->ajax([
                'url' => route('admin.product.index'),
                'type' => 'GET',
                'data' => "function(d) { d.search = $('#search').val(); d.stock_status = $('#stock_status').val(); d.category_id = $('#category_id').val(); d.producer_id = $('#producer_id').val();}",
            ])
            ->parameters([
                'language' => [
                    'sProcessing' => 'Đang xử lý...',
                    'sLengthMenu' => 'Hiển thị _MENU_ mục',
                    'sZeroRecords' => 'Không tìm thấy dữ liệu',
                    'sInfo' => 'Hiển thị từ _START_ đến _END_ trong tổng số _TOTAL_ mục',
                    'sInfoEmpty' => 'Hiển thị 0 đến 0 trong tổng số 0 mục',
                    'sInfoFiltered' => '(được lọc từ tổng số _MAX_ mục)',
                    'sSearch' => 'Tìm kiếm:',
                    'sLoadingRecords' => 'Đang tải...',
                    'oPaginate' => [
                        'sFirst' => '<i class="fa fa-angle-double-left"></i>', // Biểu tượng cho "Đầu tiên"
                        'sLast' => '<i class="fa fa-angle-double-right"></i>', // Biểu tượng cho "Cuối cùng"
                        'sNext' => '<i class="fa fa-angle-right"></i>', // Biểu tượng cho "Tiếp theo"
                        'sPrevious' => '<i class="fa fa-angle-left"></i>' // Biểu tượng cho "Trước đó"
                    ],
                    'oAria' => [
                        'sSortAscending' => ': Sắp xếp cột theo thứ tự tăng dần',
                        'sSortDescending' => ': Sắp xếp cột theo thứ tự giảm dần'
                    ]
                ],
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
            Column::computed('image', 'Ảnh'),
            Column::make('sku_code')->title('Mã sản phẩm'),
            Column::make('name')->title('Tên sản phẩm'),
            Column::make('slug')->title('Slug'),
            Column::computed('producer', 'Hãng sản xuất'),
            Column::computed('category', 'Danh mục'),
            Column::make('rate')->title('Sao đánh giá'),
            Column::make('quantity')->title('Số lượng'),
            Column::computed('stock_status', 'Trạng thái tồn kho'),
            Column::make('created_at')->title('Tạo lúc'),
            Column::computed('action', 'Hành động')
        ];
    }
}
