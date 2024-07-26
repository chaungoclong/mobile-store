<?php

namespace App\DataTables;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Services\DataTable;

class CustomerDataTable extends DataTable
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
                            ->orWhere('email', 'like', "%" . request('search') . "%")
                            ->orWhere('phone', 'like', "%" . request('search') . "%")
                            ->orWhere('address', 'like', "%" . request('search') . "%");
                    });
                }

                if (request()?->has('status')) {
                    $status = request('status');
                    if (is_string($status) && trim($status) !== '') {
                        $status = strtolower(trim($status));
                        if ($status === 'active') {
                            $query->where('active', 1);
                        } elseif ($status === 'inactive') {
                            $query->where('active', 0);
                        }
                    }
                }
            })
            ->rawColumns(['avatar_image', 'action', 'status', 'link', 'position'])
            ->editColumn('avatar_image', function ($item) {
                return '<img src="' . $item->avatar_url . '" width="50px">';
            })
            ->editColumn('created_at', function ($item) {
                return Carbon::parse($item->created_at)->format('d-m-Y H:i:s');
            })
            ->addColumn('action', function ($item) {
                $actions = '
                <a href="' . route('admin.user_show', ['id' => $item->id]) . '" class="btn btn-icon btn-sm btn-primary tip" title="Chi tiết">
                  <i class="fa fa-eye"></i>
                </a>
                <a href="javascript:void(0);" style="margin-left: 5px;" data-id="' . $item->id
                    . '" class="btn btn-icon btn-sm btn-danger deleteDialog tip" title="Xóa" data-url="'
                    . route('admin.user_delete') . '">
                        <i class="fa fa-trash"></i>
                      </a>';

                return $actions;
            })
            ->addColumn('status', function ($item) {
                if ($item->active == 1) {
                    return '<span class="label-success label">Active</span>';
                }

                return '<span class="label-danger label">Inactive</span>';
            });
    }

    /**
     * Get query source of dataTable.
     *
     * @param User $model
     * @return Builder
     */
    public function query(User $model): Builder
    {
        return $model->newQuery()->where('admin', '!=', 1);
    }

    /**
     * Optional method if you want to use html builder.
     *
     * @return HtmlBuilder
     */
    public function html(): HtmlBuilder
    {
        return $this->builder()
            ->setTableId('customers-table')
            ->columns($this->getColumns())
            ->minifiedAjax()
            ->orderBy(7)
            ->responsive()
            ->autoWidth(true)
            ->dom('rltp')
            ->addTableClass('table-hover')
            ->ajax([
                'url' => route('admin.users'),
                'type' => 'GET',
                'data' => "function(d) { d.search = $('#search').val(); d.status = $('#status').val(); }",
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
            Column::computed('avatar_image', 'Avatar'),
            Column::make('name')->title('Tên'),
            Column::make('email')->title('Email'),
            Column::make('phone')->title('SĐT'),
            Column::computed('status', 'Trạng thái'),
            Column::computed('address', 'Địa chỉ'),
            Column::make('created_at')->title('Tạo lúc'),
            Column::computed('action', 'Hành động')
        ];
    }
}
