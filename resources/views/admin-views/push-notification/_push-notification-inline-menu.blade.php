@php use App\Utils\Helpers; @endphp
<div class="inline-page-menu">
    <ul class="list-unstyled">
        @if (Helpers::modules_permission_check('Notifications', 'Push Notification Setup', 'view'))
        <li class="{{ Request::is('admin/push-notification/index') ?'active':'' }}">
            <a href="{{route('admin.push-notification.index')}}" class="text-capitalize">
                <i class="tio-notifications-on-outlined"></i>
                {{translate('push_notification')}}
            </a>
        </li>
        @endif
        @if (Helpers::modules_permission_check('Notifications', 'Push Notification Setup', 'firebase-view'))
        <li class="{{ Request::is('admin/push-notification/firebase-configuration') ?'active':'' }}">
            <a href="{{route('admin.push-notification.firebase-configuration')}}" class="text-capitalize">
                <i class="tio-cloud-outlined"></i>
                {{translate('firebase_configuration')}}
            </a>
        </li>
        @endif
    </ul>
</div>
