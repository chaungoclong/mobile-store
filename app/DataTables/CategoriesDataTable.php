<?php

namespace App\DataTables;

use App\Models\Category;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Services\DataTable;

class CategoriesDataTable extends DataTable
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
                            ->where('name', 'like', "%" . request('search') . "%")
                            ->orWhere('slug', 'like', "%" . request('search') . "%");
                    });
                }
            })
            ->rawColumns(['image', 'action'])
            ->editColumn('image', function ($item) {
                return '<img src="' . asset('storage/' . $item->image) . '" width="100px">';
            })
            ->editColumn('created_at', function ($item) {
                return Carbon::parse($item->created_at)->format('d-m-Y H:i:s');
            })
            ->editColumn('updated_at', function ($item) {
                return Carbon::parse($item->updated_at)->format('d-m-Y H:i:s');
            })
            ->addColumn('action', function ($item) {
                return '
                <a href="' . route('admin.categories.edit', ['category' => $item]) . '"
                class="btn btn-icon btn-sm btn-primary tip" title="Chỉnh Sửa">
                  <i class="fa fa-pencil" aria-hidden="true"></i>
                </a>
                <a href="javascript:void(0);"
                   class="btn btn-icon btn-sm btn-danger deleteDialog tip" title="Xóa"
                   data-url="' . route('admin.categories.destroy', ['category' => $item]) . '">
                    <i class="fa fa-trash"></i>
                </a>';
            });
    }

    /**
     * Get query source of dataTable.
     *
     * @param Category $model
     * @return Builder
     */
    public function query(Category $model): Builder
    {
        return $model->newQuery();
    }

    /**
     * Optional method if you want to use html builder.
     *
     * @return HtmlBuilder
     */
    public function html(): HtmlBuilder
    {
        return $this->builder()
            ->setTableId('categories-table')
            ->columns($this->getColumns())
            ->minifiedAjax()
            ->orderBy(4)
            ->responsive()
            ->autoWidth(true)
            ->dom('rltp')
            ->addTableClass('table-hover')
            ->ajax([
                'url' => route('admin.categories.index'),
                'type' => 'GET',
                'data' => "function(d) { d.search = $('#search').val(); }",
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
            Column::make('name')->title('Tên'),
            Column::make('slug')->title('Slug'),
            Column::computed('image', 'Ảnh'),
            Column::make('created_at')->title('Tạo lúc'),
            Column::make('updated_at')->title('Cập nhật lúc'),
            Column::computed('action', 'Hành động')
        ];
    }

    /**
     * Get filename for export.
     *
     * @return string
     */
    protected function filename(): string
    {
        return 'Categories_' . date('YmdHis');
    }
}
