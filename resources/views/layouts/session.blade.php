@foreach (['danger','info','success','warning'] as $msg)
    @if (session()->has($msg))
        <div class="alert alert-{{$msg}} alert-dismissible fade show">
            <button class="close" type="button" data-dismiss="alert">&times;</button>
            {{session()->get($msg)}}
        </div>
    @endif
@endforeach
