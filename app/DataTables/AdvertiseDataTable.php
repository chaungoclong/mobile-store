<?php

namespace App\DataTables;

use App\Helpers\Helpers;
use App\Models\Advertise;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Services\DataTable;

class AdvertiseDataTable extends DataTable
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
                            ->where('title', 'like', "%" . request('search') . "%")
                            ->orWhere('link', 'like', "%" . request('search') . "%");
                    });
                }

                if (request()?->has('status')) {
                    $status = request('status');
                    if (is_string($status) && trim($status) !== '') {
                        $status = strtolower(trim($status));
                        if ($status === 'active') {
                            $query->where('end_date', '>=', date('Y-m-d'));
                        } else {
                            $query->where('end_date', '<', date('Y-m-d'));
                        }
                    }
                }
            })
            ->rawColumns(['image', 'action', 'status', 'link', 'position'])
            ->editColumn('image', function ($item) {
                return '<img src="' . Helpers::get_image_advertise_url($item->image) . '" width="200px">';
            })
            ->editColumn('link', function ($item) {
                return "<a href='$item->link'>$item->link</a>";
            })
            ->editColumn('created_at', function ($item) {
                return Carbon::parse($item->created_at)->format('d-m-Y H:i:s');
            })
            ->addColumn('action', function ($item) {
                return '
                <a href="' . route('admin.advertise.edit', ['id' => $item->id]) . '"
                   class="btn btn-icon btn-sm btn-primary tip" title="Chỉnh Sửa">
                    <i class="fa fa-pencil" aria-hidden="true"></i>
                </a>
                <a href="javascript:void(0);" data-id="' . $item->id . '"
                   class="btn btn-icon btn-sm btn-danger deleteDialog tip" title="Xóa"
                   data-url="' . route('admin.advertise.delete') . '">
                    <i class="fa fa-trash"></i>
                </a>';
            })
            ->addColumn('status', function ($item) {
                if ($item->end_date >= date('Y-m-d')) {
                    return '<span class="label-success label">Còn Hạn</span>';
                }

                return '<span class="label-danger label">Hết Hạn</span>';
            })
            ->addColumn('position', function ($item) {
                if ($item->at_home_page) {
                    return '<span class="label-success label">Trang chủ</span>';
                }
                return '<span class="label-danger label">Trang thường</span>';
            });
    }

    /**
     * Get query source of dataTable.
     *
     * @param Advertise $model
     * @return Builder
     */
    public function query(Advertise $model): Builder
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
            ->setTableId('advertises-table')
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
                'url' => route('admin.advertise.index'),
                'type' => 'GET',
                'data' => "function(d) { d.search = $('#search').val(); d.status = $('#status').val(); }",
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
            Column::make('title')->title('Tiêu Đề'),
            Column::computed('link', 'Link'),
            Column::computed('image', 'Ảnh'),
            Column::computed('status', 'Trạng thái'),
            Column::computed('position', 'Vị trí'),
            Column::make('created_at')->title('Tạo lúc'),
            Column::computed('action', 'Hành động')
        ];
    }
}
