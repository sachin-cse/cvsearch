@foreach($rows as $oneNotification)
    @php
        $active = $class = '';
        $data = json_decode($oneNotification['data']);

        $idNotification = @$data->id;
        $forAdmin = @$data->for_admin;
        $usingData = @$data->notification;

        $services = @$usingData->type;
        $idServices = @$usingData->id;
        $title = @$usingData->message;
        $name = @$usingData->name;
        $avatar = @$usingData->avatar;
        $link = @$usingData->link;

        if(empty($oneNotification->read_at)){
            $class = 'markAsRead';
            $active = 'active';
        }
    @endphp
    <li class="notification-item notify-f-item {{$active}}">
        <span class="user-avatar">
            @if($avatar)
                <img class="image-responsive" src="{{$avatar}}" alt="{{$name}}">
            @else
                <span class="avatar-text">{{ucfirst($name[0])}}</span>
            @endif
        </span>
        <a class="{{$class}} p-0" data-id="{{$idNotification}}" href="{{$link}}">{!! $title !!}</a>
        <div class="notification-meta">
            <small class="timestamp">{{format_interval($oneNotification->created_at)}}</small>
        </div>
    </li>
@endforeach
