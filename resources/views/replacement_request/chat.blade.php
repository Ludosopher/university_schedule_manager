    <!doctype html>
    <html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
        <head>
            <meta charset="utf-8">
            <meta name="viewport" content="width=device-width, initial-scale=1">

            <!-- CSRF Token -->
            <meta name="csrf-token" content="{{ csrf_token() }}">

            <title>{{ config('app.name', 'Laravel') }}</title>

            <!-- JavaScript Bundle with Popper -->
            <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0/dist/js/bootstrap.bundle.min.js" integrity="sha384-A3rJD856KowSb7dwlZdYEkO39Gagi7vIsF0jrRAoQmDKKtQBHUuLZ9AsSv4jD4Xa" crossorigin="anonymous"></script>

            <!-- Fonts -->
            <link rel="dns-prefetch" href="//fonts.gstatic.com">
            <link href="https://fonts.googleapis.com/css?family=Nunito" rel="stylesheet">

            <!-- Styles -->
            <link href="{{ asset('css/main.css') }}" rel="stylesheet">
            <link href="{{ asset('css/app.css') }}" rel="stylesheet">
        </head>
        <body>
            <section class="body-section">
                <div class="container py-5">
                    <div class="row d-flex justify-content-center">
                        <div class="col-md-8 col-lg-6 col-xl-4">
                            <h2 class="mb-0">{{ __('header.replacement_chat') }}</h2>
                            <div class="card">
                                <div class="card-header d-flex justify-content-between align-items-center p-3 replace_description">
                                    <h5 class="mb-0">{{ $data['replacement_request_name'] }}</h5>
                                </div>
                                <div id="card-body" data-mdb-perfect-scrollbar="true" style="position: relative; height: 400px; padding: 0px 10px 0px 10px; overflow-y: scroll;">
                                        {{--  --}}
                                </div>
                                <div class="card-footer text-muted d-flex justify-content-start align-items-center p-3">
                                    <div class="input-group mb-0">
                                        <form id="form" method="POST" action="" style="width: 100%;">
                                        @csrf
                                            <input type="hidden" name="replacement_request_id" value="{{ $data['replacement_request_id'] }}">
                                            <input type="hidden" name="author_id" value="{{ $data['author_id'] }}">
                                            <input type="hidden" name="author_name" value="{{ $data['author_name'] }}">
                                            <div id="chat-button-group">
                                                <input type="text" name="body" class="form-control" placeholder="Type message" aria-label="Recipient's username"/>
                                                <button type="submit" class="btn btn-dark chat-button">{{ __('form.send') }}</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <script>
                let socket = new WebSocket("ws://192.168.56.1:8080");
                                
                var blade_data = @json($data);
                        
                socket.onopen = function(e) {
                    socket.send('{"open_replacement_request_id": ' + blade_data['replacement_request_id'] + ', "author_name": "' + blade_data['author_name'] + '"}');
                };

                socket.onmessage = function(event) {
                    var socket_data = JSON.parse(event.data);
                    var messages = document.getElementById('card-body');
                    let message = '';
                    if (socket_data.is_author !== undefined) {
                        if (socket_data.is_author) {
                            message = '' + '<div class="d-flex justify-content-between">' +
                                                '<p class="small mb-1 text-muted">' + socket_data.date + '</p>' +
                                                '<p class="small mb-1">' + socket_data.author_name + '</p>' +
                                            '</div>' +
                                            '<div class="d-flex flex-row justify-content-end mb-4 pt-1">' +
                                                '<div>' +
                                                    '<p class="small p-2 me-3 mb-3 text-white rounded-3 bg-dark" style="font-size: 100%; letter-spacing: 0.1em;">' + socket_data.body + '</p>' +
                                                '</div>' +
                                            '</div>' + '';
                        } else {
                            message = '' + '<div class="d-flex justify-content-between">' +
                                                '<p class="small mb-1 text-muted">' + socket_data.author_name + '</p>' +
                                                '<p class="small mb-1">' + socket_data.date + '</p>' +
                                            '</div>' +
                                            '<div class="d-flex flex-row justify-content-start">' +
                                                '<div>' +
                                                    '<p class="small p-2 ms-3 mb-3 rounded-3" style="background-color: #f5f6f7; font-size: 100%; letter-spacing: 0.1em;">' + socket_data.body + '</p>' +
                                                '</div>' +
                                            '</div>' + '';
                        }
                    }
                    if (socket_data.new_partisipant !== undefined) {
                        message = '<div>' +
                                          '<p class="small p-2 me-3 mb-3 text-white rounded-3 bg-success" style="font-size: 100%; letter-spacing: 0.1em;">' + socket_data.new_partisipant + ' logged into the chat</p>' +
                                      '</div>'
                    }
                    if (socket_data.left_partisipant !== undefined) {
                        message = '<div>' +
                                          '<p class="small p-2 me-3 mb-3 text-white rounded-3 bg-info" style="font-size: 100%; letter-spacing: 0.1em;">' + socket_data.left_partisipant + ' left the chat</p>' +
                                      '</div>'
                    }
                    if (socket_data.existing_messages !== undefined && socket_data.existing_messages.length !== 0) {
                        socket_data.existing_messages.forEach(function (item) {
                            if (item.is_author) {
                                message = message + '' + '<div class="d-flex justify-content-between">' +
                                                              '<p class="small mb-1 text-muted">' + item.date + '</p>' +
                                                              '<p class="small mb-1">' + item.author_name + '</p>' +
                                                          '</div>' +
                                                          '<div class="d-flex flex-row justify-content-end mb-4 pt-1">' +
                                                              '<div>' +
                                                                  '<p class="small p-2 me-3 mb-3 text-white rounded-3 bg-dark" style="font-size: 100%; letter-spacing: 0.1em;">' + item.body + '</p>' +
                                                              '</div>' +
                                                          '</div>' + '';
                            } else {
                                message = message + '' + '<div class="d-flex justify-content-between">' +
                                                            '<p class="small mb-1 text-muted">' + item.author_name + '</p>' +
                                                            '<p class="small mb-1">' + item.date + '</p>' +
                                                         '</div>' +
                                                         '<div class="d-flex flex-row justify-content-start">' +
                                                            '<div>' +
                                                                '<p class="small p-2 ms-3 mb-3 rounded-3" style="background-color: #f5f6f7; font-size: 100%; letter-spacing: 0.1em;">' + item.body + '</p>' +
                                                            '</div>' +
                                                         '</div>' + '';
                            }
                        });
                    }
                    messages.insertAdjacentHTML('beforeend', message);
                    document.getElementById('card-body').scrollTop = 9999999;
                };

                const form = document.getElementById('form');
                form.addEventListener('submit', getFormValue);

                function getFormValue(event) {
                    event.preventDefault();
                    const replacement_request_id = form.querySelector('[name="replacement_request_id"]'),
                        author_id = form.querySelector('[name="author_id"]'),
                        author_name = form.querySelector('[name="author_name"]'),
                        body = form.querySelector('[name="body"]');

                    const data = {
                        replacement_request_id: replacement_request_id.value,
                        author_id: author_id.value,
                        author_name: author_name.value,
                        body: body.value,
                    }

                    let json = JSON.stringify(data);
                    socket.send(json);
                };

                socket.onclose = function(event) {
                    if (event.wasClean) {
                        console.log(`[close] Соединение закрыто чисто, код=${event.code} причина=${event.reason}`);
                    } else {
                        console.log('[close] Соединение прервано');
                    }
                };

                socket.onerror = function(error) {
                    console.log(error.code);
                };
                            
            </script>
        </body>

    

    
     



