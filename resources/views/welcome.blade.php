@extends('layouts.app')

@section('content')
    <div class="container content">
        <br>
            <h2>{{trans('welcome.header')}}</h2>
                @for ($i = 0; $i < 5; $i++)
                    <article class="message is-info">
                        <div class="message-header">
                            <p>Feature {{$i + 1}}</p>
                        </div>
                        <div class="message-body">
                            Lorem ipsum dolor sit amet, consectetur adipiscing elit. <strong>Pellentesque risus mi</strong>, tempus quis placerat ut, porta nec nulla. Vestibulum rhoncus ac ex sit amet fringilla. Nullam gravida purus diam, et dictum <a>felis venenatis</a> efficitur. Aenean ac <em>eleifend lacus</em>, in mollis lectus. Donec sodales, arcu et sollicitudin porttitor, tortor urna tempor ligula, id porttitor mi magna a neque. Donec dui urna, vehicula et sem eget, facilisis sodales sem.
                        </div>
                    </article>
                @endfor
            <h3><a class="welcome-redirect" href="{{$redirect_link}}">Go to my Actionlime</a></h3>
    </div>
@endsection