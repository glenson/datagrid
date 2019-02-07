<div class="dg-wrapper" {!! $id ? 'id='.$id : '' !!}>
	<table data-dg-type="table" class="table table-striped table-hover table-bordered">
		<thead>
		<!-- Titles -->
		<tr data-dg-type="titles" style="background-color: rgba(0, 0, 0, 0.05);">
			@if ($grid->isItBulkable())
				<th data-dg-col="bulks">@if ( ! $grid->hasFilters() )<input type="checkbox" />@endif</th>
			@endif

			@foreach ($grid->getColumns() as $col)
				@if ($col->isAction() === false)
					<th class="sorting" data-dg-col="{{ $col->getKey() }}" {!! $col->getAttributesHtml() !!}>
						@if ($col->isSortable())
							<a href="{{ Datagrid::currentUri($grid->getSortParams($id, $col->getKey())) }}">{!! $col->getTitle() !!}<i class="fa @if (\Request::input('f.order_by', '') == $col->getKey() && \Request::input('f.order_dir', 'ASC') == 'ASC') fa-sort-asc @elseif (\Request::input('f.order_by', '') == $col->getKey() && \Request::input('f.order_dir', 'ASC') == 'DESC') fa-sort-desc @else fa-sort @endif"></i></a>
						@else
							{{ $col->getTitle() }} 
						@endif
					</th>
				@else
					<th data-dg-col="actions" style="color:#009688; vertical-align: middle;" {!! $col->getAttributesHtml() !!} > Actions </th>
				@endif
			@endforeach
		</tr>
		<!-- END Titles -->

		@if ($grid->hasFilters())
			<!-- Filters -->
			@if ($grid->hasFilters())
				{!! Form::open(['role' => 'form', 'method' => 'GET']) !!}
				<?php
				if(isset($_SERVER['QUERY_STRING'])){
					parse_str($_SERVER['QUERY_STRING'], $parameter); 
					ksort($parameter);
				?>
				@foreach ($parameter as $name => $value)
					@if(is_array($value))
						<?php ksort($value); ?>
						@foreach ($value as $name0 => $value0)
							@if($id !== $name || ($id == $name && ($name0 == 'order_dir' || $name0 == 'order_by' || $name0 == 'page')))
								{!! Form::hidden($name.'['.$name0.']', $value0); !!}
							@endif
						@endforeach
					@else
						{!! Form::hidden($name, $value); !!}
					@endif
				@endforeach
				<?php } ?>
				@foreach ($grid->getHiddens() as $name => $value)
					{!! Form::hidden($name, $value); !!}
				@endforeach
			@endif
			<tr data-dg-type="filters-row">
				@if ($grid->isItBulkable())
					<th data-dg-col="bulks">
						<input type="checkbox" value="1" data-dg-bulk-select="all" />
					</th>
				@endif

				@foreach ($grid->getColumns() as $col)
					@if ($col->isAction() === false)
						@if ($col->hasFilters())
							<th data-dg-col="{{ $col->getKey() }}" {!! $col->getAttributesHtml() !!} style="padding: 3px;">
								@if ( is_array($col->getFilters()) && count($col->getFilters()) > 0 )
									{!!
										Form::select(
											$col->getFilterNameById($id),
											$col->getFilters(true),
											$grid->getFilter($col->getKey()),
											($col->hasFilterMany() ? ['multiple' => 'multiple'] : []) + array('class' => 'form-control input-sm', 'data-dg-type' => "filter")
										)
									!!}
								@else
									{!!
										Form::text(
											$col->getFilterNameById($id),
											$grid->getFilter($col->getKey()),
											array('class' => 'form-control input-sm', 'data-dg-type' => "filter", 'placeholder' => $col->getTitle())
										)
									!!}
								@endif
							</th>
						@else
							<th data-dg-col="{{ $col->getKey() }}" style="padding: 3px;"> &nbsp;</th>
						@endif
					@else
						<th data-dg-col="actions" style="text-align:center; min-width:80px; padding: 3px;" class="align-middle">
							<div class='btn-group' role='group'>
							{{ Form::button('<i class="'.config('icons.search_btn').'"></i>'.__('common.search'), ['type' => 'submit', 'class' => 'btn btn-primary btn-sm'] )  }}
							<?php 
								unset($parameter[$id]);
								$current_action = \Illuminate\Support\Facades\Route::current()->getAction();
								$controller = '\\' . $current_action['controller'];
								$parameters = \Illuminate\Support\Facades\Route::current()->parameters();
								ksort($parameter);
        						$uri = action($controller, $parameters) . ($parameter ? '?' . http_build_query($parameter) : '');
							?>
							<a href="{{ $uri }}" class="btn btn-info btn-sm" title="Clear"><i class="fas fa-eraser" aria-hidden="true"></i> Clear </a>
							</div>
						</th>
					@endif
				@endforeach
			</tr>
			@if ($col->isAction() === false)
				<div style="display:none;">
					{{ Form::button('<i class="'.config('icons.search_btn').'"></i>'.__('common.search'), ['type' => 'submit', 'class' => 'btn btn-primary btn-sm'] )  }}
				</div>
			@endif
			@if ($grid->hasFilters())
				{!! Form::close() !!}
			@endif
			<!-- END Filters -->
		@endif
		</thead>

		<tbody>
		@forelse($grid->getRows() as $row)
			<tr data-dg-type="data-row">
				@if ($grid->isItBulkable())
					<th data-dg-col="bulks"><input type="checkbox" value="{{ $row->{$grid->getBulk()} }}" data-dg-bulk-select="row" /></th>
				@endif

				@foreach ($grid->getColumns() as $col)
					<td data-dg-col="{{ $col->getKey() }}" {!! $col->getAttributesHtml() !!}> <?php $value = $row->{$col->getKey(true)}  ?> @if ($col->hasWrapper()) {!! $col->wrapper($value, $row) !!}  @else  {!! $value !!}  @endif </td>
				@endforeach
			</tr>
		@empty
			<tr data-dg-type="empty-result">
				<td colspan="{{ $grid->getColumnsCount() }}">
					No results found!
				</td>
			</tr>
		@endforelse
		</tbody>
	</table>
	@if ($grid->hasPagination())
		<div class="row-fluid text-center">
			{!! $grid->getPagination()->render() !!}
		</div>
	@endif

</div><!-- /.dg-wrapper -->
