@component('mail::message')
# {{$heading}}
{!! $post->parsedContent !!}
<section id="photos" style="line-height: 0; column-count:3; column-gap:0px;">
@foreach($links as $link)
            <img src="{{$link}}" style=" width: 100% !important; height: auto !important;  padding: 5px;">
@endforeach
</section>
@component('mail::button', ['url' => $post_url, 'color' => 'blue'])
{{$visit_text}}
@endcomponent
@endcomponent
