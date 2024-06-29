<?php

namespace App\DataTables;

use App\Models\Producer;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Services\DataTable;

class ProducersDataTable extends DataTable
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
            ->rawColumns(['logo', 'action'])
            ->editColumn('logo', function ($item) {
                return '<img src="' . asset('storage/' . $item->logo) . '" width="100px">';
            })
            ->editColumn('created_at', function ($item) {
                return Carbon::parse($item->created_at)->format('d-m-Y H:i:s');
            })
            ->editColumn('updated_at', function ($item) {
                return Carbon::parse($item->updated_at)->format('d-m-Y H:i:s');
            })
            ->addColumn('action', function ($item) {
                return '
                <a href="' . route('admin.producers.edit', ['producer' => $item]) . '"
                class="btn btn-icon btn-sm btn-primary tip" title="Chỉnh Sửa">
                  <i class="fa fa-pencil" aria-hidden="true"></i>
                </a>
                <a href="javascript:void(0);" data-id="{{ $producer->id }}"
                   class="btn btn-icon btn-sm btn-danger deleteDialog tip" title="Xóa"
                   data-url="' . route('admin.producers.destroy', ['producer' => $item]) . '">
                    <i class="fa fa-trash"></i>
                </a>';
            });
    }

    /**
     * Get query source of dataTable.
     *
     * @param Producer $model
     * @return Builder
     */
    public function query(Producer $model): Builder
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
            ->setTableId('producers-table')
            ->columns($this->getColumns())
            ->minifiedAjax()
            ->stateSave(true)
            ->orderBy(1)
//            ->selectStyleSingle()
            ->responsive()
            ->autoWidth(true)
            ->dom('rtip')
            ->addTableClass('table-hover')
            ->ajax([
                'url' => route('admin.producers.index'),
                'type' => 'GET',
                'data' => "function(d) { d.search = $('#searchInput').val(); }",
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
            Column::computed('logo', 'Logo'),
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
        return 'Producers' . date('YmdHis');
    }
}
