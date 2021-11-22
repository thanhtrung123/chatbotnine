<div class="container">
    <div class="row">
        <div class="col-md-12 flush_message_area">

            <div class="alert alert-danger alert-dismissible" role="alert" style="display:{{ empty(session('flush_error')) ?  'none' : 'block' }};">
                <button type="button" class="close" data-dismiss="alert" aria-label="閉じる"><span aria-hidden="true">×</span></button>
                @if(session('flush_error'))
                    @foreach(session('flush_error') as $key => $val)
                        <p><strong>{{ $key }}</strong> {{ $val }}</p>
                    @endforeach
                @endif
            </div>

            <div class="alert alert-info alert-dismissible" role="alert" style="display:{{ empty(session('flush_message')) ?  'none' : 'block' }};">
                <button type="button" class="close" data-dismiss="alert" aria-label="閉じる"><span aria-hidden="true">×</span></button>
                @if(session('flush_message'))
                    @foreach(session('flush_message') as $key => $val)
                        <p><strong>{{ $key }}</strong> {{ $val }}</p>
                    @endforeach
                @endif
            </div>

            <template>
                <div class="alert alert-info alert-dismissible" role="alert">
                    <button type="button" class="close" data-dismiss="alert" aria-label="閉じる"><span aria-hidden="true">×</span></button>
                    <p class="flush_message"></p>
                </div>
            </template>

        </div>
    </div>
</div>
