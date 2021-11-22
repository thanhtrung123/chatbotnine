@extends('layouts.admin')
@section('pageTitle', '裏ツール シナリオ')
@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <h3>@yield('pageTitle')</h3>
                <a href="{{route('admin.tools.index')}}">裏ツールトップへ戻る</a>

                <div class="row">
                    <div class="col-md-12">
                        <div class="panel panel-default">
                            <div class="panel-body">
                                <div class="row">
                                    <div class="col-md-10 col-md-offset-1">

                                        <form id="data_form">

                                            @foreach($categories as $key => $val)

                                                <div class="panel panel-default">
                                                    <div class="panel-heading">{{$val}}</div>
                                                    <div class="panel-body">

                                                        <template id="clone_src_{{$key}}">
                                                            <tr id="s_{{$key}}_row_%idx%">
                                                                <input name="s_{{$key}}_idx[%idx%]" type="hidden" value="%idx%">
                                                                <td><input name="s_{{$key}}_id[%idx%]" class="form-control scenario_id" size="1" required></td>
                                                                <td><input name="s_{{$key}}_name[%idx%]" class="form-control" required></td>
                                                                <td><input name="s_{{$key}}_pids[%idx%]" class="form-control" size="2"></td>
                                                                <td><input name="s_{{$key}}_aid[%idx%]" class="form-control" size="1"></td>
                                                                <td><input name="s_{{$key}}_odr[%idx%]" class="form-control" size="1"></td>
                                                                <td><input type="button" value="削除" class="btn btn-default" data-dom-delete='@json(['target'=>"#s_{$key}_row_%idx%",'parent'=>"#clone_area_{$key}"])'></td>
                                                            </tr>
                                                        </template>

                                                        <table class="table">
                                                            <thead>
                                                            <tr>
                                                                <th>ID</th>
                                                                <th>シナリオ</th>
                                                                <th>親ID</th>
                                                                <th>API_ID</th>
                                                                <th>表示順</th>
                                                                <th></th>
                                                            </tr>
                                                            </thead>
                                                            <tbody id="clone_area_{{$key}}">
                                                            @foreach($scenarios[$key] as $idx => $row)
                                                                <tr id="s_{{$key}}_row_{{$idx}}">
                                                                    <input name="s_{{$key}}_idx[{{$idx}}]" value="{{$idx}}" type="hidden">
                                                                    <td><input name="s_{{$key}}_id[{{$idx}}]" class="form-control scenario_id" size="1" value="{{$row['id']}}" readonly></td>
                                                                    <td><input name="s_{{$key}}_name[{{$idx}}]" class="form-control" value="{{$row['name']}}" required></td>
                                                                    <td><input name="s_{{$key}}_pids[{{$idx}}]" class="form-control" size="2" value="{{implode(',',$row['parent_scenario_ids'])}}"></td>
                                                                    <td><input name="s_{{$key}}_aid[{{$idx}}]" class="form-control" size="1" value="{{implode(',',$row['api_ids'])}}"></td>
                                                                    <td><input name="s_{{$key}}_odr[{{$idx}}]" class="form-control" size="1" value="{{$row['order']}}"></td>
                                                                    <td><input type="button" value="削除" class="btn btn-default" data-dom-delete='@json(['target'=>"#s_{$key}_row_{$idx}",'parent'=>"#clone_area_{$key}"])'></td>
                                                                </tr>
                                                            @endforeach
                                                            </tbody>
                                                        </table>

                                                        @php
                                                            $add=['src'=>"#clone_src_{$key}",'area'=>"#clone_area_{$key}",'idx_src'=>"[name^=s_{$key}_idx]"]
                                                        @endphp

                                                        <input type="button" value="追加" class="btn btn-default" data-dom-copy='@json($add)'>

                                                    </div>
                                                </div>

                                            @endforeach

                                            <div class="row">
                                                <div class="col-md-2">
                                                    <button class="btn btn-primary btn-block" type="submit">適用</button>
                                                </div>
                                            </div>

                                        </form>

                                        {{ Form::open(['url'=>route('admin.tools.scenario'),'method'=>'POST','class'=>'','id'=>'entry_form']) }}
                                        <input type="hidden" value="" id="data" name="data">
                                        {{ Form::close() }}

                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>

    <script>
        $('#data_form').on('submit', function (e) {
            e.preventDefault();
            var dform = $(this), form = $('#entry_form');
            var fs = dform.serialize();
            $('#data').val(fs);
            form[0].submit();
        });

        base.form.callback.domCopy.after.add(function (src, area) {
            var max = 0;
            $('.scenario_id').each(function () {
                var val = $(this).val() - 0;
                if (max < val) max = val;
            });
            src.find('.scenario_id').val(max + 1);
        });
    </script>

@endsection
