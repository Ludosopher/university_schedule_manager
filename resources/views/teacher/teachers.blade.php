@extends('layouts.app')
@section('content')
    <div class="container">
        @includeIf('parts.notices.response')
        @includeIf('parts.notices.errors_instances')
        @includeIf('parts.notices.errors_instance_delete')
        <div class="getAllContainer">
            <div class="getAllLeft">
                @includeIf('parts.forms.find')
            </div>
            <div class="getAllRight">
                {{-- <h1>{{ __('header.teachers') }}</h1>
                <table id="dtBasicExample" class="table table-striped table-bordered table-sm" cellspacing="0" width="100%">
                    <thead>
                        <tr>
                            @if (Auth::check() && (Auth::user()->is_admin || Auth::user()->is_moderator))
                                <th class="th-sm text-center align-top"></th>
                            @endif
                            @foreach($data['table_properties'] as $property)
                                @if($property['sorting'])
                                    @if(is_array($property['field']) && isset($property['sort_name']))
                                        <th class="th-sm text-center align-top">
                                            <div class="sorting-header"><div class="header-name"></div><div>@sortablelink($property['sort_name'], __('table_header.'.$property['header']), [], ['title' => __('title.sort'), 'class' => 'sort-button'])</div></div>
                                        </th>
                                    @elseif(is_array($property['field']))
                                        @php
                                            $full_field = implode('.', $property['field']);
                                        @endphp
                                        <th class="th-sm text-center align-top">
                                            <div class="sorting-header"><div class="header-name"></div><div>@sortablelink($full_field, __('table_header.'.$property['header']), [], ['title' => __('title.sort'), 'class' => 'sort-button'])</div></div>
                                        </th>
                                    @else
                                        <th class="th-sm text-center align-top">
                                            <div class="sorting-header"><div class="header-name"></div><div>@sortablelink($property['field'], __('table_header.'.$property['header']), [], ['title' => __('title.sort'), 'class' => 'sort-button'])</div></div>
                                        </th>
                                    @endif
                                @else
                                    <th class="th-sm text-center align-top">{{ __('table_header.'.$property['header']) }}</th>
                                @endif
                            @endforeach
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($data['instances'] as $instance)
                            <tr>
                                @if (Auth::check() && (Auth::user()->is_admin || Auth::user()->is_moderator))
                                    <td>
                                        <a class="" href="{{ route('teacher-update-form', ['updating_id' => $instance->id]) }}" title="{{ __('title.update') }}">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-pencil-square" viewBox="0 0 16 16">
                                                <path d="M15.502 1.94a.5.5 0 0 1 0 .706L14.459 3.69l-2-2L13.502.646a.5.5 0 0 1 .707 0l1.293 1.293zm-1.75 2.456-2-2L4.939 9.21a.5.5 0 0 0-.121.196l-.805 2.414a.25.25 0 0 0 .316.316l2.414-.805a.5.5 0 0 0 .196-.12l6.813-6.814z"/>
                                                <path fill-rule="evenodd" d="M1 13.5A1.5 1.5 0 0 0 2.5 15h11a1.5 1.5 0 0 0 1.5-1.5v-6a.5.5 0 0 0-1 0v6a.5.5 0 0 1-.5.5h-11a.5.5 0 0 1-.5-.5v-11a.5.5 0 0 1 .5-.5H9a.5.5 0 0 0 0-1H2.5A1.5 1.5 0 0 0 1 2.5v11z"/>
                                            </svg>
                                        </a>
                                        <a class="" href="{{ route('teacher-delete', ['deleting_id' => $instance->id]) }}" title="{{ __('title.delete') }}">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-x-circle-fill" viewBox="0 0 16 16">
                                                <path d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0zM5.354 4.646a.5.5 0 1 0-.708.708L7.293 8l-2.647 2.646a.5.5 0 0 0 .708.708L8 8.707l2.646 2.647a.5.5 0 0 0 .708-.708L8.707 8l2.647-2.646a.5.5 0 0 0-.708-.708L8 7.293 5.354 4.646z"/>
                                            </svg>
                                        </a>
                                    </td>
                                @endif
                                @foreach($data['table_properties'] as $property)
                                    @php $field = $property['field'] @endphp
                                    @if(is_array($field) && count($field) > 1)
                                        @php
                                            $value = $instance;
                                            foreach ($field as $part) {
                                                $value = $value->$part;
                                                if (!is_object($value)) {
                                                    break;
                                                }
                                            }
                                        @endphp
                                        <td class="regular-cell">{{ \Lang::has('dictionary.'.$value) ? __('dictionary.'.$value) : $value }}</td>
                                    @elseif($field == 'full_name')
                                        <td class="regular-cell"><a href="{{ route('teacher-schedule', ['schedule_teacher_id' => $instance->id]) }}" title="{{ __('title.teacher_schedule') }}">{{ \Lang::has('dictionary.'.$instance->$field) ? __('dictionary.'.$instance->$field) : $instance->$field }}</a></td>
                                    @else
                                        <td class="regular-cell">{{ \Lang::has('dictionary.'.$instance->$field) ? __('dictionary.'.$instance->$field) : $instance->$field }}</td>
                                    @endif
                                @endforeach
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr>
                            @if (Auth::check() && (Auth::user()->is_admin || Auth::user()->is_moderator))
                                <th class="th-sm text-center align-top"></th>
                            @endif
                            @foreach($data['table_properties'] as $property)
                                <th class="th-sm text-center align-top">{{ __('table_header.'.$property['header']) }}</th>
                            @endforeach
                        </tr>
                    </tfoot>
                </table>
                <div class="nav-pagination">
                    {{ $data['instances']->links('pagination::bootstrap-4') }}
                </div> --}}
                @includeIf('parts.tables.instances')
            </div>
        </div>
    </div>
@endsection
