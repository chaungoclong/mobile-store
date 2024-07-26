<?php

namespace App\DataTables;

use App\Helpers\Helpers;
use App\Models\Post;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Services\DataTable;

class PostDataTable extends DataTable
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
                        $searchQuery->where('title', 'like', "%" . request('search') . "%");
                    });
                }
            })
            ->rawColumns(['image', 'action'])
            ->editColumn('image', function ($item) {
                return '<img src="' . Helpers::get_image_post_url($item->image) . '" width="100px">';
            })
            ->editColumn('created_at', function ($item) {
                return Carbon::parse($item->created_at)->format('d-m-Y H:i:s');
            })
            ->addColumn('action', function ($item) {
                $actions = '
                <a href="' . route('admin.post.edit', ['id' => $item->id]) . '" class="btn btn-icon btn-sm btn-primary tip" title="Chi tiết">
                  <i class="fa fa-eye"></i>
                </a>
                <a href="javascript:void(0);" style="margin-left: 5px;" data-id="' . $item->id
                    . '" class="btn btn-icon btn-sm btn-danger deleteDialog tip" title="Xóa" data-url="'
                    . route('admin.post.delete') . '">
                        <i class="fa fa-trash"></i>
                      </a>';

                return $actions;
            });
    }

    /**
     * Get query source of dataTable.
     *
     * @param Post $model
     * @return Builder
     */
    public function query(Post $model): Builder
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
            ->setTableId('posts-table')
            ->columns($this->getColumns())
            ->minifiedAjax()
            ->orderBy(3)
            ->responsive()
            ->autoWidth(true)
            ->dom('rltp')
            ->addTableClass('table-hover')
            ->ajax([
                'url' => route('admin.post.index'),
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
            Column::computed('image', 'Ảnh'),
            Column::make('title')->title('Tiêu Đề'),
            Column::make('created_at')->title('Tạo lúc'),
            Column::computed('action', 'Hành động')
        ];
    }
}
